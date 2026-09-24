document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('plModal');
    const form = document.getElementById('plForm');
    if (!modalElement || !form) return;
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    const name = document.getElementById('plName');
    const id = document.getElementById('plId');
    const precio = document.getElementById('plPrecio');
    const desc = document.getElementById('plDesc');
    const inicio = document.getElementById('plInicio');
    const venc = document.getElementById('plVenc');
    const save = document.getElementById('plSave');
    const error = document.getElementById('plFormError');
    const notice = document.getElementById('plNotice');
    const token = form.querySelector('input[type="hidden"][name]:not(#plId)');
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
        id.value = row ? row.id_plan : '';
        name.value = row ? row.nombre_plan : '';
        desc.value = row ? (row.descripcion || '') : '';
        precio.value = row ? (row.precio != null ? Number(row.precio).toFixed(2) : '0.00') : '';
        inicio.value = row && row.fecha_de_inicio ? String(row.fecha_de_inicio).slice(0, 10) : '';
        venc.value = row && row.fecha_de_vencimiento ? String(row.fecha_de_vencimiento).slice(0, 10) : '';
        document.getElementById('plModalTitle').textContent = row ? 'Editar plan' : 'Nuevo Plan';
        save.querySelector('span').textContent = row ? 'Guardar cambios' : 'Guardar plan';
        modal.show();
    }

    document.getElementById('plAdd').addEventListener('click', function () { openEditor(null, this); });
    $('#tablplanes').on('click', '.pl-edit', function () {
        const row = tablplanes.row(this.closest('tr')).data();
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
        else document.getElementById('plAdd').focus();
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (saving) return;
        name.value = name.value.trim();
        name.setCustomValidity(name.value ? '' : 'Ingrese el nombre del plan.');
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
            tablplanes.ajax.reload(null, false);
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
