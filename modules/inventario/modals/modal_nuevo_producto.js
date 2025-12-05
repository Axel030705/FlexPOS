document.addEventListener("click", (e) => {
    if (e.target.classList.contains("cerrar-modal")) {
        const modal = e.target.closest(".fondo-modal");
        if (modal) modal.remove();
    }
});


document.addEventListener("submit", async (e) => {
    if (e.target.matches("#formNuevoProducto")) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        const res = await fetch("/modules/inventario/funciones/modals/guardar_producto.php", {
            method: "POST",
            body: formData
        });

        const data = await res.json();

        if (data.success) {
            Swal.fire("Éxito", data.message, "success");

            // Cerrar modal
            document.activeElement.blur();
            form.closest(".fondo-modal").remove();

            // Recargar tabla
            filtrarProductos("0");

        } else {
            Swal.fire("Error", data.message, "error");
        }
    }
});



