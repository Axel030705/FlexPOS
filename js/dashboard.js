document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll('#menu-modulos a');
    const contenido = document.getElementById('contenido-modulo');
    const modulosExtra = document.querySelectorAll('.container_conf a');
    const todosLosLinks = document.querySelectorAll('#menu-modulos a, .container_conf a');

    // Evento para módulos principales
    links.forEach(link => {
        link.addEventListener('click', (e) => cargarModuloDesdeLink(e, link));
    });

    // Evento para módulo Configuración
    modulosExtra.forEach(link => {
        link.addEventListener('click', (e) => cargarModuloDesdeLink(e, link));
    });

    function cargarModuloDesdeLink(e, link) {
        e.preventDefault();
        const archivo = link.getAttribute('data-archivo');
        if (!archivo) return;

        const partes = archivo.split('/');
        const nombreModulo = partes[partes.length - 2] || null;
        if (!nombreModulo) return;

        // Quitar activo de todos y asignarlo al nuevo
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
