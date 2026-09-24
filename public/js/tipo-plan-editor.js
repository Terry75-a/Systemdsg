document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('tpModal');
    const form = document.getElementById('tpForm');
    if (!modalElement || !form) return;
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    const name = document.getElementById('tpName');
    const id = document.getElementById('tpId');
    const save = document.getElementById('tpSave');
    const error = document.getElementById('tpFormError');
    const notice = document.getElementById('tpNotice');
    const token = form.querySelector('input[type="hidden"][name]:not(#tpId)');
    let saving = false;
    let animation;
    let trigger;

    function openEditor(row, button) {
        if (saving) return;
        trigger = button;
        form.reset();
        name.setCustomValidity('');
        error.hidden = true;
        notice.hidden = true;
        id.value = row ? row.id_tipo_plan : '';
        name.value = row ? row.nombre_tipo : '';
        document.getElementById('tpModalTitle').textContent = row ? 'Editar tipo de plan' : 'Añadir tipo de plan';
        save.querySelector('span').textContent = row ? 'Guardar cambios' : 'Guardar tipo de plan';
        modal.show();
    }

    document.getElementById('tpAdd').addEventListener('click', function () { openEditor(null, this); });
    $('#tablaplanes').on('click', '.tp-edit', function () {
        const row = tablaplanes.row(this.closest('tr')).data();
        if (row) openEditor(row, this);
    });
    name.addEventListener('input', function () { name.setCustomValidity(''); });
    modalElement.addEventListener('shown.bs.modal', function () {
        name.focus();
        if (typeof gsap === 'undefined' || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        animation = gsap.fromTo(modalElement.querySelector('.modal-content'),
            { opacity: 0, y: 10, scale: .98 },
            { opacity: 1, y: 0, scale: 1, duration: .24, ease: 'power2.out', clearProps: 'opacity,transform' });
    });
    modalElement.addEventListener('hide.bs.modal', function (event) {
        if (saving) event.preventDefault();
    });
    modalElement.addEventListener('hidden.bs.modal', function () {
        if (animation) animation.kill();
        if (typeof gsap !== 'undefined') gsap.set(modalElement.querySelector('.modal-content'), { clearProps: 'opacity,transform' });
        if (trigger && trigger.isConnected) trigger.focus();
        else document.getElementById('tpAdd').focus();
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (saving) return;
        name.value = name.value.trim();
        name.setCustomValidity(name.value ? '' : 'Ingrese el nombre del tipo de plan.');
        if (!form.reportValidity()) return;
        saving = true;
        save.disabled = true;
        name.readOnly = true;
        form.setAttribute('aria-busy', 'true');
        const originalLabel = save.querySelector('span').textContent;
        save.querySelector('span').textContent = 'Guardando…';
        modalElement.querySelectorAll('[data-bs-dismiss]').forEach(button => { button.disabled = true; });
        error.hidden = true;
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: new FormData(form)
            });
            const isJson = (response.headers.get('content-type') || '').includes('application/json');
            if (!isJson) throw new Error('La sesión pudo haber vencido. Recarga la página e intenta nuevamente.');
            const result = await response.json();
            if (result.csrfHash && token) {
                token.value = result.csrfHash;
                token.defaultValue = result.csrfHash;
            }
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'No se pudo guardar. Recarga la página e intenta nuevamente.');
            }
            saving = false;
            modal.hide();
            notice.textContent = result.message;
            notice.hidden = false;
            tablaplanes.ajax.reload(null, false);
        } catch (failure) {
            error.textContent = failure instanceof TypeError
                ? 'No se pudo conectar. Revisa tu conexión e intenta nuevamente.'
                : failure.message;
            error.hidden = false;
        } finally {
            saving = false;
            save.disabled = false;
            name.readOnly = false;
            form.removeAttribute('aria-busy');
            save.querySelector('span').textContent = originalLabel;
            modalElement.querySelectorAll('[data-bs-dismiss]').forEach(button => { button.disabled = false; });
        }
    });
});
