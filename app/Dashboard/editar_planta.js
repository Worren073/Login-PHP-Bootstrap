/**
 * Modal para editar una planta
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

        // Llenar el formulario con los datos actuales de la planta
        await cargarDatosPlanta(id);

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
 * Cargar los datos actuales de la planta en el formulario del modal
 */
async function cargarDatosPlanta(id) {
    try {
        const response = await fetch(`plantas.php?id=${id}`);

        if (!response.ok) {
            throw new Error("Error al cargar los datos de la planta");
        }

        const data = await response.json();

        // Asignar los valores a los campos del formulario
        document.getElementById("id").value = data.id;
        document.getElementById("nombre_comun").value = data.nombre_comun;
        document.getElementById("nombre_cien").value = data.nombre_cien;
        document.getElementById("fecha_siembra").value = data.fecha_siembra;
        document.getElementById("etapa").value = data.etapa;
        document.getElementById("tipo").value = data.tipo;
        document.getElementById("cantidad").value = data.cantidad;

        // Establecer el ID en un campo oculto para enviar con el formulario
        document.getElementById("plantaId").value = id;

    } catch (error) {
        console.error(error);
        toastr.error("Error al cargar los datos de la planta: " + error.message); // Mensaje de error más descriptivo
    }
}

/**
 * Guardar los cambios realizados en la planta
 */
async function guardarCambios() {
    const id = document.getElementById("plantaId").value;
    const nombre_comun = document.getElementById("nombre_comun").value;
    const nombre_cien = document.getElementById("nombre_cien").value;
    const fecha_siembra = document.getElementById("fecha_siembra").value;
    const etapa = document.getElementById("etapa").value;
    const tipo = document.getElementById("tipo").value;
    const cantidad = document.getElementById("cantidad").value;

    try {
        const response = await axios.post("acciones/update.php", {
            id: id,
            nombre_comun: nombre_comun,
            nombre_cien: nombre_cien,
            fecha_siembra: fecha_siembra,
            etapa: etapa,
            tipo: tipo,
            cantidad: cantidad,
        });

        if (response.status === 200 && response.data.status === "success") {
            // Actualizar la fila correspondiente en la tabla (si es necesario)
            const fila = document.querySelector(`#planta_${id}`);
            if (fila) {
                fila.querySelector(".nombre_comun").textContent = nombre_comun;
                fila.querySelector(".nombre_cien").textContent = nombre_cien;
                fila.querySelector(".fecha_siembra").textContent = fecha_siembra;
                fila.querySelector(".etapa").textContent = etapa;
                fila.querySelector(".tipo").textContent = tipo;
                fila.querySelector(".cantidad").textContent = cantidad;
            }

            // Mostrar mensaje de éxito
            toastr.success("¡La planta se actualizó correctamente!");

            // Cerrar el modal
            const modal = document.querySelector("#modalEdit.modal"); // Selecciona el modal específico
            if (modal) {
                const confirmModal = bootstrap.Modal.getInstance(modal);
                if (confirmModal) {
                    confirmModal.hide();
                }
            }
        } else {
            toastr.error(`Error al actualizar la planta con ID ${id}: ${response.data.message || 'Error desconocido'}`); // Mensaje de error más descriptivo
        }
    } catch (error) {
        console.error(error);
        toastr.error("Hubo un problema al actualizar la planta: " + error.message); // Mensaje de error más descriptivo
    }
}
