document.getElementById("cajaForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let fondo = document.getElementById("fondo_inicial").value.trim();

    if (fondo === "" || isNaN(fondo) || parseFloat(fondo) < 0) {
        Swal.fire({
            icon: "warning",
            title: "Fondo inválido",
            text: "Introduce un fondo inicial válido."
        });
        return;
    }

    Swal.fire({
        title: 'Abriendo caja...',
        didOpen: () => Swal.showLoading(),
        allowOutsideClick: false
    });

    fetch('db/abrir_caja.php', {
        method: 'POST',
        body: new FormData(document.getElementById("cajaForm"))
    })
    .then(res => res.json())
    .then(data => {
        console.log(data);

        if (data.status === 'ok') {
            Swal.fire({
                icon: 'success',
                title: 'Caja abierta correctamente'
            }).then(() => {
                window.location.href = "ventas.php";
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.msg || 'No se pudo abrir la caja'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error en el servidor',
            text: error
        });
    });
});

// Observa cambios en SweetAlert para evitar salto visual
const observer = new MutationObserver(() => {
    if (document.body.classList.contains('swal2-height-auto')) {
        document.body.classList.remove('swal2-height-auto');
    }
});
observer.observe(document.body, { attributes: true });