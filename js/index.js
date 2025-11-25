document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let usuario = document.getElementById("usuario").value.trim();
    let password = document.getElementById("password").value.trim();

    if (usuario === "" || password === "") {
        Swal.fire({
            icon: "warning",
            title: "Campos vacíos",
            text: "Por favor completa todos los campos."
        });
        return;
    }

    // Mostrar loading indefinido
    Swal.fire({
        title: 'Verificando...',
        didOpen: () => {
            Swal.showLoading();
        },
        allowOutsideClick: false,
        allowEscapeKey: false
    });

    // Petición fetch o AJAX
    fetch('db/login.php', {
        method: 'POST',
        body: new FormData(document.getElementById("loginForm"))
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'ok') {
            Swal.fire({ icon: 'success', title: 'Bienvenido' }).then(() => {
                window.location.href = "dashboard.php";
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.msg || 'Credenciales incorrectas'
            });
        }
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error en la conexión con el servidor'
        });
    });
});

function togglePassword() {
    const password = document.getElementById("password");
    password.type = password.type === "password" ? "text" : "password";
}

// Observa cambios en el body y elimina swal2-height-auto automáticamente
const observer = new MutationObserver(() => {
    if (document.body.classList.contains('swal2-height-auto')) {
        document.body.classList.remove('swal2-height-auto');
    }
});
observer.observe(document.body, { attributes: true });
