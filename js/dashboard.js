document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll('#menu-modulos a');
    const contenido = document.getElementById('contenido-modulo');
    const modulosExtra = document.querySelectorAll('.container_conf a');
    const todosLosLinks = document.querySelectorAll('#menu-modulos a, .container_conf a');

    const logo = document.querySelector('.container_logo');
    const sidebar = document.querySelector('.sidebar');

    // Toggle del sidebar con el logo
    if (logo && sidebar) {
        logo.addEventListener('click', () => {

            // 🔹 Volver a buscar elementos actuales del módulo cargado
            const img_menu = document.querySelector("#contenido-modulo .imagen_menu");
            const sidebar_ventas = document.querySelector(".div2");
            const container_products_ventas = document.querySelector(".div1");

            sidebar.classList.toggle('oculto');
            contenido.classList.toggle('contenido-expandido');

            if (img_menu) img_menu.classList.toggle("icono_visible");
            if (sidebar_ventas) sidebar_ventas.classList.toggle("contenido-expandido");
            if (container_products_ventas) container_products_ventas.classList.toggle("contenido-expandido");
        });
    }

    // Eventos
    links.forEach(link => link.addEventListener('click', (e) => cargarModuloDesdeLink(e, link)));
    modulosExtra.forEach(link => link.addEventListener('click', (e) => cargarModuloDesdeLink(e, link)));

    function cargarModuloDesdeLink(e, link) {
        e.preventDefault();
        const archivo = link.getAttribute('data-archivo');
        if (!archivo) return;

        const partes = archivo.split('/');
        const nombreModulo = partes[partes.length - 2] || null;
        if (!nombreModulo) return;

        todosLosLinks.forEach(l => l.classList.remove('activo'));
        link.classList.add('activo');

        Swal.fire({
            title: 'Cargando módulo...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        fetch(archivo)
            .then(response => {
                if (!response.ok) throw new Error("Error HTTP " + response.status);
                return response.text();
            })
            .then(data => {
                setTimeout(() => {
                    Swal.close();
                    contenido.innerHTML = data;

                    cargarCSSModulo(nombreModulo);
                    cargarJSModulo(nombreModulo);

                    // 🔹 Reset después de cargar Módulo
                    resetUI();

                    // Activar botón del módulo cargado
                    const img_menu = document.querySelector("#contenido-modulo .imagen_menu");
                    if (img_menu) {
                        img_menu.addEventListener('click', () => {
                            sidebar.classList.remove('oculto');
                            contenido.classList.remove('contenido-expandido');

                            const sidebar_ventas = document.querySelector(".div2");
                            const container_products_ventas = document.querySelector(".div1");

                            img_menu.classList.remove("icono_visible");
                            if (sidebar_ventas) sidebar_ventas.classList.remove("contenido-expandido");
                            if (container_products_ventas) container_products_ventas.classList.remove("contenido-expandido");
                        });
                    }

                }, 300);
            })
            .catch(error => {
                console.error('Error cargando módulo:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cargar módulo',
                    text: 'Intenta nuevamente.'
                });
            });
    }

    // 🔹 Siempre obtener elementos actuales del módulo
    function resetUI() {
        sidebar.classList.remove('oculto');
        contenido.classList.remove('contenido-expandido');

        const img_menu = document.querySelector("#contenido-modulo .imagen_menu");
        const sidebar_ventas = document.querySelector(".div2");
        const container_products_ventas = document.querySelector(".div1");

        if (img_menu) img_menu.classList.remove("icono_visible");
        if (sidebar_ventas) sidebar_ventas.classList.remove("contenido-expandido");
        if (container_products_ventas) container_products_ventas.classList.remove("contenido-expandido");
    }

    function cargarCSSModulo(nombreModulo) {
        document.querySelectorAll('.css-modulo').forEach(el => el.remove());
        const cssLink = document.createElement('link');
        cssLink.rel = 'stylesheet';
        cssLink.href = `/modules/${nombreModulo}/${nombreModulo}.css`;
        cssLink.classList.add('css-modulo');
        cssLink.onerror = () => console.warn(`⚠️ No se encontró CSS para el módulo "${nombreModulo}".`);
        document.head.appendChild(cssLink);
    }

    function cargarJSModulo(nombreModulo) {
        document.querySelectorAll('.js-modulo').forEach(el => el.remove());
        const script = document.createElement('script');
        script.src = `/modules/${nombreModulo}/${nombreModulo}.js`;
        script.classList.add('js-modulo');
        script.defer = true;
        script.onerror = () => console.warn(`⚠️ No se encontró JS para el módulo "${nombreModulo}".`);
        document.body.appendChild(script);
    }
});
