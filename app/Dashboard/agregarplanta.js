// Obtener el modal
const modal = document.getElementById("myModal");

// Obtener el botón que abre el modal
const openModalBtn = document.getElementById("openModal");

// Obtener el elemento <span> que cierra el modal
const closeModalSpan = document.querySelector(".close"); // Usar querySelector para más flexibilidad

// Función para abrir el modal
const openModal = () => {
    modal.style.display = "block";
    document.body.classList.add("modal-open"); // Opcional: evita el scroll en el body
};

// Función para cerrar el modal
const closeModal = () => {
    modal.style.display = "none";
    document.body.classList.remove("modal-open"); // Opcional: remueve la clase del body
};

// Evento para abrir el modal cuando se hace clic en el botón
openModalBtn.addEventListener("click", openModal);

// Evento para cerrar el modal cuando se hace clic en el span (x)
closeModalSpan.addEventListener("click", closeModal);

// Evento para cerrar el modal cuando se hace clic fuera del modal
window.addEventListener("click", (event) => {
    if (event.target === modal) {
        closeModal();
    }
});

// Evento para cerrar el modal cuando se presiona la tecla "Escape"
document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
        closeModal();
    }
});
