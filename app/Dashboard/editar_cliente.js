/**
 * Modal para editar un Cliente
 */
async function cargarModalEdicion(id) {
    try {
        // Realizar una solicitud GET usando Fetch para obtener el contenido del modal
        const response = await fetch(`modales/modalEdit.php?id=${id}`);

        if (!response.ok) {
            throw new Error("Error al cargar la modal de edición");
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

        // Llenar el formulario con los datos actuales del Cliente
        await cargarDatosCliente(id);

        // Eliminar el contenedor del modal después de cerrarlo
        modalContainer.querySelector(".modal").addEventListener('hidden.bs.modal', () => {
            modalContainer.remove();
        });

    } catch (error) {
        console.error(error);
        toastr.error("Error al cargar la modal de edición: " + error.message); // Mensaje de error más descriptivo
    }
}

/**
 * Cargar los datos actuales del Cliente en el formulario del modal
 */
async function cargarDatosCliente(id) {
    try {
        const response = await fetch(`clientes.php?id=${id}`);

        if (!response.ok) {
            throw new Error("Error al cargar los datos del Cliente");
        }

        const data = await response.json();

        // Asignar los valores a los campos del formulario
        document.getElementById("id").value = data.id;
        document.getElementById("nombre_client").value = data.nombre_client;
        document.getElementById("apellido_client").value = data.apellido_client;
        document.getElementById("documento").value = data.documento;
        document.getElementById("num_telefono").value = data.num_telefono;
        document.getElementById("correo").value = data.correo;

        // Establecer el ID en un campo oculto para enviar con el formulario
        document.getElementById("clienteId").value = id;

    } catch (error) {
        console.error(error);
        toastr.error("Error al cargar los datos del Cliente: " + error.message); // Mensaje de error más descriptivo
    }
}

/**
 * Guardar los cambios realizados en el Cliente
 */
async function guardarCambios() {
    const id = document.getElementById("clienteId").value;
    const nombre_client = document.getElementById("nombre_client").value;
    const apellido_client = document.getElementById("apellido_client").value;
    const documento = document.getElementById("documento").value;
    const num_telefono = document.getElementById("num_telefono").value;
    const correo = document.getElementById("correo").value;

    try {
        const response = await axios.post("acciones/update.php", {
            id: id,
            nombre_client: nombre_client,
            apellido_client: apellido_client,
            documento: documento,
            num_telefono: num_telefono,
            correo: correo,
        });

        if (response.status === 200 && response.data.status === "success") {
            // Actualizar la fila correspondiente en la tabla (si es necesario)
            const fila = document.querySelector(`#cliente_${id}`);
            if (fila) {
                fila.querySelector(".nombre_client").textContent = nombre_client;
                fila.querySelector(".apellido_client").textContent = apellido_client;
                fila.querySelector(".documento").textContent = documento;
                fila.querySelector(".num_telefono").textContent = num_telefono;
                fila.querySelector(".correo").textContent = correo;
            }

            // Mostrar mensaje de éxito
            toastr.success("¡El Cliente se actualizó correctamente!");

            // Cerrar el modal
            const modal = document.querySelector("#modalEdit.modal"); // Selecciona el modal específico
            if (modal) {
                const confirmModal = bootstrap.Modal.getInstance(modal);
                if (confirmModal) {
                    confirmModal.hide();
                }
            }
        } else {
            toastr.error(`Error al actualizar al Cliente con ID ${id}: ${response.data.message || 'Error desconocido'}`); // Mensaje de error más descriptivo
        }
    } catch (error) {
        console.error(error);
        toastr.error("Hubo un problema al actualizar al Cliente: " + error.message); // Mensaje de error más descriptivo
    }
}
