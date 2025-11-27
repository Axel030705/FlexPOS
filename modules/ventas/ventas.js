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

// Ejecutar cuando el módulo se cargue (compatible con carga dinámica en dashboard.php)
if (document.readyState !== "loading") {
    iniciarFiltros();
} else {
    document.addEventListener("DOMContentLoaded", iniciarFiltros);
}
