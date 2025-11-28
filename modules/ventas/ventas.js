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
        didOpen: () => {
            Swal.showLoading();
        },
    });

    fetch("/modules/ventas/funciones/filtrarProductos.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `categoria=${encodeURIComponent(categoria)}`
    })
        .then((response) => response.text())
        .then((data) => {
            const contenedorProductos = document.getElementById("productosContainer");
            if (contenedorProductos) {
                contenedorProductos.innerHTML = data;
                iniciarBuscador();

                // ---- NUEVO: Agregar evento click a cada producto ----
                const productos = contenedorProductos.querySelectorAll(".producto");
                productos.forEach((producto) => {
                    producto.addEventListener("click", () => {
                        const idProducto = producto.getAttribute("data-id");
                        if (!idProducto) return;

                        fetch("/modules/ventas/funciones/agregar_carrito.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: `id_producto=${encodeURIComponent(idProducto)}`
                        })
                            .then((resp) => resp.json())
                            .then((res) => {
                                if (res.success) {
                                    // Actualizar contador del carrito
                                    // actualizarContadorCarrito(res.carrito);

                                    // Actualizar vista del carrito
                                    actualizarVistaCarrito();

                                    // Feedback visual
                                    Swal.fire({
                                        icon: "success",
                                        title: "Producto agregado",
                                        showConfirmButton: false,
                                        timer: 500
                                    });
                                } else {
                                    Swal.fire("Error", res.msg, "error");
                                }
                            })
                            .catch((err) => {
                                console.error(err);
                                Swal.fire("Error", "No se pudo agregar el producto", "error");
                            });
                    });
                });
            }

            // Pequeño delay para que se vea el loader
            setTimeout(() => {
                Swal.close();
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

function actualizarVistaCarrito() {
    fetch("/modules/ventas/funciones/obtener_carrito.php")
        .then(res => res.json())
        .then(data => {
            const contenedor = document.getElementById("productosPedido");
            const spanSubtotal = document.getElementById("subtotal");
            const spanImpuestos = document.getElementById("impuestos");
            const spanTotal = document.getElementById("total");

            contenedor.innerHTML = ""; // Limpiar

            if (data.length === 0) {
                // contenedor.innerHTML = "<p>No hay productos en el carrito</p>";
                spanSubtotal.textContent = "$0.00";
                spanImpuestos.textContent = "$0.00";
                spanTotal.textContent = "$0.00";
                return;
            }

            let subtotal = 0;

            data.forEach(producto => {
                subtotal += parseFloat(producto.total);

                const div = document.createElement("div");
                div.classList.add("producto-carrito");
                div.setAttribute("data-id", producto.id);

                div.innerHTML = `
                    <img src="${producto.imagen}" alt="${producto.nombre}" class="img-carrito">
                    <div class="info-carrito">
                        <div class="nombre-precio">
                            <h3>${producto.nombre}</h3>
                            <p>$${producto.precio.toFixed(2)}</p>
                        </div>
                        <div class="cantidad-control">
                            <button class="disminuir">-</button>
                            <span class="cantidad">${producto.cantidad}</span>
                            <button class="aumentar">+</button>
                        </div>
                        <p>$<span class="total">${producto.total.toFixed(2)}</span></p>
                    </div>
                    <img src="../../../assets/ventas/delete.svg" class="eliminar-carrito">
                `;

                contenedor.appendChild(div);

                // Eventos botones
                div.querySelector(".aumentar").addEventListener("click", () =>
                    cambiarCantidad(producto.id, producto.cantidad + 1)
                );
                div.querySelector(".disminuir").addEventListener("click", () => {
                    if (producto.cantidad > 1) cambiarCantidad(producto.id, producto.cantidad - 1);
                });
                div.querySelector(".eliminar-carrito").addEventListener("click", () =>
                    eliminarProducto(producto.id)
                );
            });

            // Calcular totales
            let impuestos = subtotal * 0.16;
            let total = subtotal + impuestos;

            // Mostrar en UI
            spanSubtotal.textContent = `$${subtotal.toFixed(2)}`;
            spanImpuestos.textContent = `$${impuestos.toFixed(2)}`;
            spanTotal.textContent = `$${total.toFixed(2)}`;
        })
        .catch(console.error);
}


function cambiarCantidad(idProducto, nuevaCantidad) {
    fetch("/modules/ventas/funciones/actualizar_carrito.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_producto=${idProducto}&cantidad=${nuevaCantidad}`
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                actualizarVistaCarrito();
            }
        });
}

function eliminarProducto(idProducto) {
    fetch("/modules/ventas/funciones/eliminar_carrito.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_producto=${idProducto}`
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                actualizarVistaCarrito();
            }
        });
}


function iniciarBuscador() {
    const buscador = document.getElementById("buscadorProductos");
    const contenedorProductos = document.getElementById("productosContainer");

    if (!buscador || !contenedorProductos) return;

    buscador.addEventListener("input", () => {
        const texto = buscador.value.toLowerCase().trim();
        const productos = contenedorProductos.querySelectorAll(".producto");

        productos.forEach((producto) => {
            // Aquí capturamos el texto del <h1>
            const nombre = producto.querySelector(".texto-producto h1")?.textContent.toLowerCase() || "";

            producto.style.display = nombre.includes(texto) || texto === "" ? "" : "none";
        });
    });
}

document.getElementById("btnVaciar").addEventListener("click", () => {
    fetch("/modules/ventas/funciones/vaciar_carrito.php")
        .then(res => res.json())
        .then(data => {
            if (data.success) actualizarVistaCarrito();
        });
});

// Ejecutar cuando el módulo se cargue
if (document.readyState !== "loading") {
    iniciarFiltros();
    iniciarBuscador();
    actualizarVistaCarrito();
} else {
    document.addEventListener("DOMContentLoaded", () => {
        iniciarFiltros();
        iniciarBuscador();
        actualizarVistaCarrito();
    });
}


