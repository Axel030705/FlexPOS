function iniciarFiltros() {
    const slider = document.getElementById("sliderCategorias");
    if (!slider) return; // Evitar errores si no existe aún

    const botones = slider.querySelectorAll("button");

    // Activar deslizado con mouse (solo para PC)
    let isDown = false;
    let startX;
    let scrollLeft;

    slider.addEventListener("mousedown", (e) => {
        isDown = true;
        slider.classList.add("dragging");
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
    });

    slider.addEventListener("mouseup", () => {
        isDown = false;
        slider.classList.remove("dragging");
    });

    slider.addEventListener("mouseleave", () => {
        isDown = false;
        slider.classList.remove("dragging");
    });

    slider.addEventListener("mousemove", (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - slider.offsetLeft;
        const walk = (x - startX) * 1; // velocidad
        slider.scrollLeft = scrollLeft - walk;
    });

    // Seleccionar "Todos" automáticamente (valor "0")
    if (botones.length > 0) {
        botones.forEach((b) => b.classList.remove("active"));
        const botonTodos = slider.querySelector("button[data-cat='0']");
        if (botonTodos) botonTodos.classList.add("active");
        filtrarProductos("0");
    }

    // Eventos de botones
    botones.forEach((btn) => {
        btn.addEventListener("click", () => {
            botones.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");
            filtrarProductos(btn.getAttribute("data-cat"));
        });
    });
}


function filtrarProductos(categoria) {
    Swal.fire({
        title: "Cargando productos...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    fetch("/modules/inventario/funciones/filtrarProductos.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `categoria=${encodeURIComponent(categoria)}`
    })
        .then((response) => response.json()) // JSON
        .then((productos) => {
            const tbody = document.querySelector("#tablaProductos tbody");

            if (!tbody) return;
            tbody.innerHTML = ""; // Limpiar antes de insertar

            productos.forEach((p) => {
                const tr = document.createElement("tr");
                tr.setAttribute("data-id", p.id);

                tr.innerHTML = `
                    <td>
                        <img src="${p.imagen}" alt="${p.nombre}" width="40" style="vertical-align:middle;margin-right:5px;">
                        ${p.nombre}
                    </td>
                    <td class="${p.stock < p.stock_min ? 'stock-bajo' : ''}">
                        <img src="/assets/inventario/${p.stock < p.stock_min ? 'stock_alert.svg' : 'stock_ok.svg'}" 
                        alt="${p.stock < p.stock_min ? 'Stock bajo' : 'Stock suficiente'}" 
                        style="width:14px; vertical-align:middle;">
                        ${p.stock} Unidades
                    </td>
                    <td>$${parseFloat(p.costo).toFixed(2)}</td>
                    <td>$${parseFloat(p.precio).toFixed(2)}</td>
                    <td>${p.proveedor || "—"}</td>
                    <td>
                        <button class="btn-editar" data-id="${p.id}">
                            <img src="/assets/inventario/editarP.svg" alt="Editar">
                        </button>
                        <button class="btn-eliminar" data-id="${p.id}">
                            <img src="/assets/inventario/deleteP.svg" alt="Eliminar">
                        </button>
                        <button class="btn-ocultar" data-id="${p.id}" data-activo="${p.activo}">
                            <img src="/assets/inventario/${p.activo == 1 ? 'visible.svg' : 'oculto.svg'}" alt="Ocultar">
                        </button>
                    </td>
                `;

                tbody.appendChild(tr);
            });

            // Eventos para los botones
            document.querySelectorAll(".btn-editar").forEach((btn) => {
                btn.addEventListener("click", () => editarProducto(btn.dataset.id));
            });

            document.querySelectorAll(".btn-eliminar").forEach((btn) => {
                btn.addEventListener("click", () => eliminarProducto(btn.dataset.id));
            });

            document.querySelectorAll(".btn-ocultar").forEach((btn) => {
                btn.addEventListener("click", () => ocultarProducto(btn.dataset.id));
            });


            // Cerrar loader
            setTimeout(() => {
                Swal.close();
                iniciarBuscador();
            }, 400);

        })
        .catch((error) => {
            console.error("Error:", error);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "No se pudo cargar los productos.",
            });
        });
}


function iniciarBuscador() {
    const buscador = document.getElementById("buscadorProductos");
    const tabla = document.getElementById("tablaProductos");

    if (!buscador || !tabla) return;

    buscador.addEventListener("input", () => {
        const texto = buscador.value.toLowerCase().trim();
        const filas = tabla.querySelectorAll("tbody tr");

        filas.forEach((fila) => {
            // Buscamos por nombre del producto (columna 1)
            const nombreProducto = fila.querySelector("td")?.textContent.toLowerCase() || "";
            fila.style.display = nombreProducto.includes(texto) || texto === "" ? "" : "none";
        });
    });
}


function editarProducto(id) {
    console.log("Editar producto", id);
    // Aquí abres modal o haces tu lógica
}

function eliminarProducto(id) {
    console.log("Eliminar producto", id);
    // Swal de confirmación o lógica
}

function ocultarProducto(id) {
    const btn = document.querySelector(`.btn-ocultar[data-id="${id}"]`);
    const activo = btn.getAttribute("data-activo") === "1";

    Swal.fire({
        title: activo ? "¿Ocultar producto?" : "¿Hacer visible el producto?",
        text: activo ? "No se eliminará, solo dejará de mostrarse." : "Volverá a mostrarse en el inventario.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: activo ? "Sí, ocultar" : "Sí, mostrar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (!result.isConfirmed) return;

        fetch('/modules/inventario/funciones/ocultarProducto.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `accion=toggleProducto&id=${id}`
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const fila = document.querySelector(`tr[data-id="${id}"]`);

                    if (data.nuevoEstado == 0) {
                        // Si se ocultó, lo quitamos de la tabla
                        fila.remove();
                        Swal.fire("Listo", "Producto ocultado", "success");
                    } else {
                        // 👇 Si se hizo visible, cambiamos icono
                        btn.setAttribute("data-activo", "1");
                        btn.querySelector("img").src = "/assets/inventario/visible.svg";
                        Swal.fire("Listo", "Producto visible", "success");
                    }

                    // Si estamos filtrando "ocultos", recargamos la vista
                    const btnActivo = document.querySelector("button.active");
                    if (btnActivo && btnActivo.getAttribute("data-cat") === "ocultos") {
                        filtrarProductos("ocultos");
                    }
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            });

    });
}




// Ejecutar cuando el módulo se cargue
if (document.readyState !== "loading") {
    iniciarFiltros();
    iniciarBuscador();
} else {
    document.addEventListener("DOMContentLoaded", () => {
        iniciarFiltros();
        iniciarBuscador();
    });
}


