/**
 * Modal para confirmar la eliminación de un cliente
 */
async function cargarModalConfirmacion() {
    try {
        const existingModal = document.getElementById("confirmModal");
        if (existingModal) {
            const modal = bootstrap.Modal.getInstance(existingModal);
            if (modal) {
                modal.hide();
            }
            existingModal.remove(); // Eliminar la modal existente
        }

        // Realizar una solicitud GET usando Fetch para obtener el contenido de la modal
        const response = await fetch("modales/modalDelete.php");

        if (!response.ok) {
            throw new Error("Error al cargar la modal de confirmación");
        }

        // Obtener el contenido de la modal
        const modalHTML = await response.text();

        // Crear un elemento div para almacenar el contenido de la modal
        const modalContainer = document.createElement("div");
        modalContainer.innerHTML = modalHTML;

        // Agregar la modal al documento actual
        document.body.appendChild(modalContainer);

        // Mostrar la modal
        const myModal = new bootstrap.Modal(modalContainer.querySelector(".modal"));
        myModal.show();
    } catch (error) {
        console.error(error);
    }
}

/**
 * Función para eliminar un C.liente desde la modal
 */
async function eliminarcliente(id, cliente) {
    try {
        // Llamar a la función para cargar y mostrar la modal de confirmación
        await cargarModalConfirmacion();

        // Establecer el ID en el botón de confirmación
        document.getElementById("confirmDeleteBtn").setAttribute("data-id", id);

        // Agregar un event listener al botón "Eliminar"
        document.getElementById("confirmDeleteBtn").addEventListener("click", async function () {
            var idCliente = this.getAttribute("data-id");

            try {
                const response = await axios.post("../db/eliminar_cliente.php", { id: idCliente });

                if (response.status === 200 && response.data.status === "success") {
                    // Eliminar la fila correspondiente a este Cliente de la tabla
                    document.querySelector(`#cliente_${idCliente}`).remove();
                    
                    // Mostrar mensaje de éxito
                    if (window.toastrOptions) {
                        toastr.options = window.toastrOptions;
                        toastr.success("¡El Cliente se eliminó correctamente!");
                    }
                } else {
                    alert(`Error al eliminar al Cliente con ID ${idCliente}`);
                }
            } catch (error) {
                console.error(error);
                alert("Hubo un problema al eliminar al Cliente");
            } finally {
                // Cerrar la modal de confirmación
                var confirmModal = bootstrap.Modal.getInstance(document.getElementById("confirmModal"));
                confirmModal.hide();
            }
        });
    } catch (error) {
        console.error(error);
        alert("Hubo un problema al cargar la modal de confirmación");
    }
}
