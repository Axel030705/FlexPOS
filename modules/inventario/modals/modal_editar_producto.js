async function cargarDatosEditar(id) {
    try {
        const res = await fetch("/modules/inventario/funciones/obtenerProducto.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${id}`
        });

        const data = await res.json();

        if (!data.success) {
            Swal.fire("Error", data.message, "error");
            return;
        }

        const p = data.producto;

        // Rellenar campos del formulario
        document.querySelector('input[name="id_producto"]').value = id;
        document.querySelector('input[name="nombre"]').value = p.nombre;
        document.querySelector('input[name="codigo"]').value = p.codigo;
        document.querySelector('select[name="id_categoria"]').value = p.id_categoria;
        document.querySelector('select[name="id_proveedor"]').value = p.id_proveedor ?? "";
        document.querySelector('input[name="costo"]').value = p.costo;
        document.querySelector('input[name="precio"]').value = p.precio;
        document.querySelector('input[name="stock"]').value = p.stock;
        document.querySelector('select[name="activo"]').value = p.activo;
        document.querySelector('textarea[name="descripcion"]').value = p.descripcion;

        // Mostrar imagen actual (si existe)
        const contImg = document.getElementById("previewImagen");

        // Ruta por defecto
        const imgDefault = "../../../uploads/imagenes/productos/default.svg";

        let url = p.url_imagen && p.url_imagen.trim() !== ""
            ? p.url_imagen
            : imgDefault;

        // Insertar imagen y aplicar fallback si falla
        if (contImg) {
            contImg.innerHTML = ` <img src="${url}" 
             style="width:120px;" 
             onerror="this.onerror=null; this.src='${imgDefault}';"> `;
        }


    } catch (error) {
        console.error(error);
        Swal.fire("Error", "No se pudo obtener los datos del producto.", "error");
    }
}

document.addEventListener("submit", async (e) => {
    if (e.target.matches("#formEditarProducto")) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        const res = await fetch("/modules/inventario/funciones/editar_producto.php", {
            method: "POST",
            body: formData
        });

        const data = await res.json();

        if (data.success) {
            Swal.fire("Éxito", data.message, "success");

            form.closest(".fondo-modal").remove();
            filtrarProductos("0");

        } else {
            Swal.fire("Error", data.message, "error");
        }
    }
});

