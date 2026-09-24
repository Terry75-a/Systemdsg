function Most(event) {
    const archivo = event.target.files[0];
    if (!archivo) return;

    const reader = new FileReader();

    reader.onload = function(e) {
        const base64 = e.target.result;

        // 1. Cambiar foto en el perfil
        document.getElementById('fotoPerfil').src = base64;

        // 2. Guardar en localStorage para mantenerla en todo el dashboard
        localStorage.setItem('fotoUsuario', base64);

        // 3. Cambiar también en el topbar si existe
        const topbarFoto = document.getElementById('fotoTopbar');
        if (topbarFoto) {
            topbarFoto.src = base64;
        }
    };

    reader.readAsDataURL(archivo);
}

// ==========================
// Cargar imagen guardada al entrar al dashboard
// ==========================
window.addEventListener('DOMContentLoaded', () => {
    const guardada = localStorage.getItem('fotoUsuario');

    if (guardada) {
        const perfilFoto = document.getElementById('fotoPerfil');
        const topbarFoto = document.getElementById('fotoTopbar');

        if (perfilFoto) perfilFoto.src = guardada;
        if (topbarFoto) topbarFoto.src = guardada;
    }
});
