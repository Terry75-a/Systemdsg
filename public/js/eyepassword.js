(function () {
    var toggle = document.querySelector('#togglePassword');
    var input = document.querySelector('#password');
    if (!toggle || !input) return;

    var icon = toggle.querySelector('i, svg') || toggle;

    toggle.addEventListener('click', function () {
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        toggle.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
        toggle.setAttribute('title', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
        if (icon.classList) {
            icon.classList.toggle('fa-eye', !show);
            icon.classList.toggle('fa-eye-slash', show);
        }
        input.focus({ preventScroll: true });
    });
})();
