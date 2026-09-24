<link rel="stylesheet" href="<?= base_url('css/dashboard/calendario.css?v=' . filemtime(FCPATH . 'css/dashboard/calendario.css')) ?>">
<main class="cal-page">
    <div class="pdsg-page-head">
        <div>
            <h1 class="pdsg-page-title">Calendario</h1>
            <p class="pdsg-page-sub">Planifica citas, vencimientos y eventos del panel.</p>
        </div>
        <button type="button" id="calAdd" class="btn pdsg-add-btn cal-add">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> Nuevo evento
        </button>
    </div>

    <div id="calNotice" class="alert alert-success" role="status" hidden></div>

    <section class="pdsg-card cal-card" aria-label="Calendario de eventos">
        <div class="cal-toolbar">
            <div class="cal-chip"><i class="fa-regular fa-calendar" aria-hidden="true"></i><span>Agenda</span><span class="pdsg-tab-count" id="calCount" aria-live="polite">0</span></div>
            <div class="cal-legend" aria-hidden="true">
                <span class="cal-dot cal-dot-a"></span> Reuniones
                <span class="cal-dot cal-dot-b"></span> Vencimientos
                <span class="cal-dot cal-dot-c"></span> Tareas
            </div>
        </div>
        <div id="calendar"></div>
    </section>
</main>

<!-- Modal: crear / editar evento -->
<div class="modal fade" id="evModal" tabindex="-1" aria-labelledby="evModalTitle" aria-describedby="evModalDescription" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="evForm" novalidate>
                <input type="hidden" id="evId">
                <div class="tp-modal-head">
                    <span class="tp-modal-icon"><i class="fa-regular fa-calendar-plus" aria-hidden="true"></i></span>
                    <div>
                        <h2 id="evModalTitle">Nuevo evento</h2>
                        <p id="evModalDescription">Registra una cita, vencimiento o tarea en el calendario.</p>
                    </div>
                    <button type="button" class="tp-modal-close" data-bs-dismiss="modal" aria-label="Cerrar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>
                <div class="tp-modal-body">
                    <div id="evFormError" class="alert alert-danger" role="alert" hidden></div>

                    <label for="evTitle">Título <span class="text-danger">*</span></label>
                    <input type="text" id="evTitle" name="title" class="form-control" placeholder="Ej.: Reunión con cliente" maxlength="120" required aria-describedby="evTitleHelp">
                    <p id="evTitleHelp">Nombre corto y claro del evento. Máximo 120 caracteres.</p>

                    <label for="evType" class="mt-3">Tipo</label>
                    <select id="evType" name="type" class="form-control">
                        <option value="a">Reunión</option>
                        <option value="b">Vencimiento</option>
                        <option value="c">Tarea</option>
                    </select>

                    <label for="evDate" class="mt-3">Fecha <span class="text-danger">*</span></label>
                    <input type="date" id="evDate" name="date" class="form-control" required>

                    <div class="cal-times">
                        <div>
                            <label for="evStart">Hora inicio</label>
                            <input type="time" id="evStart" name="start" class="form-control">
                        </div>
                        <div>
                            <label for="evEnd">Hora fin</label>
                            <input type="time" id="evEnd" name="end" class="form-control">
                        </div>
                    </div>

                    <label for="evDesc" class="mt-3">Descripción</label>
                    <textarea id="evDesc" name="description" class="form-control" rows="2" placeholder="Detalles opcionales…"></textarea>
                </div>
                <div class="tp-modal-footer">
                    <button type="button" class="btn tp-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn tp-modal-save" id="evSave"><i class="fa-solid fa-check" aria-hidden="true"></i> <span>Guardar evento</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: detalles del día -->
<div class="modal fade" id="dayModal" tabindex="-1" aria-labelledby="dayModalTitle" aria-describedby="dayModalDescription" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="tp-modal-head">
                <span class="tp-modal-icon"><i class="fa-regular fa-calendar-day" aria-hidden="true"></i></span>
                <div>
                    <h2 id="dayModalTitle">Detalles del día</h2>
                    <p id="dayModalDescription">—</p>
                </div>
                <button type="button" class="tp-modal-close" data-bs-dismiss="modal" aria-label="Cerrar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
            </div>
            <div class="tp-modal-body cal-day-body">
                <div id="dayModalList" class="cal-day-list" aria-live="polite"></div>
            </div>
            <div class="tp-modal-footer">
                <button type="button" class="btn tp-modal-cancel" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn tp-modal-save" id="dayAddEvent"><i class="fa-solid fa-plus" aria-hidden="true"></i> <span>Añadir evento</span></button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var notice = document.getElementById('calNotice');
    var countEl = document.getElementById('calCount');
    var noticeTimer;

    var evModalEl = document.getElementById('evModal');
    var dayModalEl = document.getElementById('dayModal');
    var evModal = bootstrap.Modal.getOrCreateInstance(evModalEl);
    var dayModal = bootstrap.Modal.getOrCreateInstance(dayModalEl);
    var evForm = document.getElementById('evForm');
    var evTitle = document.getElementById('evTitle');
    var evType = document.getElementById('evType');
    var evDate = document.getElementById('evDate');
    var evStart = document.getElementById('evStart');
    var evEnd = document.getElementById('evEnd');
    var evDesc = document.getElementById('evDesc');
    var evId = document.getElementById('evId');
    var evSave = document.getElementById('evSave');
    var evError = document.getElementById('evFormError');
    var dayTitle = document.getElementById('dayModalTitle');
    var dayDesc = document.getElementById('dayModalDescription');
    var dayList = document.getElementById('dayModalList');
    var dayAdd = document.getElementById('dayAddEvent');
    var currentDay = null;
    var seq = 100;
    var evForceTimer = null;

    var TYPE_META = {
        a: { label: 'Reunión', cls: 'cal-ev-a', chip: 'cal-type-a', icon: 'fa-users' },
        b: { label: 'Vencimiento', cls: 'cal-ev-b', chip: 'cal-type-b', icon: 'fa-triangle-exclamation' },
        c: { label: 'Tarea', cls: 'cal-ev-c', chip: 'cal-type-c', icon: 'fa-list-check' }
    };

    function showNotice(message, isError) {
        if (!notice) return;
        notice.textContent = message;
        notice.hidden = false;
        notice.classList.toggle('alert-danger', !!isError);
        notice.classList.toggle('alert-success', !isError);
        clearTimeout(noticeTimer);
        noticeTimer = setTimeout(function () { notice.hidden = true; }, 3200);
    }

    function updateCount() {
        if (!countEl || !calendar) return;
        countEl.textContent = calendar.getEvents().length;
    }

    function pad(n) { return n < 10 ? '0' + n : String(n); }

    function toDateInput(d) {
        if (!d) return '';
        var dt = (d instanceof Date) ? d : new Date(d);
        if (isNaN(dt)) return '';
        return dt.getFullYear() + '-' + pad(dt.getMonth() + 1) + '-' + pad(dt.getDate());
    }

    function formatDayLabel(iso) {
        if (!iso) return '—';
        var parts = String(iso).slice(0, 10).split('-');
        var dt = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
        if (isNaN(dt)) return iso;
        try {
            return dt.toLocaleDateString('es', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        } catch (e) {
            return iso;
        }
    }

    function formatTime(date) {
        if (!date) return '';
        var d = (date instanceof Date) ? date : new Date(date);
        if (isNaN(d)) return '';
        return pad(d.getHours()) + ':' + pad(d.getMinutes());
    }

    function isAllDay(event) {
        var s = event.start;
        if (!s) return true;
        var hours = s.getHours(), mins = s.getMinutes();
        return hours === 0 && mins === 0 && !event.extendedProps.hadTime;
    }

    function buildEvent(def) {
        var meta = TYPE_META[def.type] || TYPE_META.a;
        var start = def.date;
        if (def.start) start = def.date + 'T' + def.start;
        var end;
        if (def.end) end = def.date + 'T' + def.end;
        return {
            id: def.id || ('ev' + (seq++)),
            title: def.title,
            start: start,
            end: end,
            classNames: [meta.cls],
            extendedProps: {
                type: def.type || 'a',
                description: def.description || '',
                hadTime: !!(def.start)
            }
        };
    }

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto',
        firstDay: 1,
        nowIndicator: true,
        dayMaxEvents: 3,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            list: 'Lista'
        },
        selectable: true,
        editable: true,
        selectMirror: true,
        events: [
            buildEvent({ id: 'e1', title: 'Reunión con cliente', type: 'a', date: '2026-09-03', start: '10:00', end: '11:00', description: 'Revisar propuesta AsistDSG.' }),
            buildEvent({ id: 'e2', title: 'Corte de caja', type: 'b', date: '2026-09-10', description: 'Cierre contable del día.' }),
            buildEvent({ id: 'e3', title: 'Actualizar inventario', type: 'c', date: '2026-09-17', description: 'Revisar stock mínimo.' }),
            buildEvent({ id: 'e4', title: 'Demo AsistDSG', type: 'a', date: '2026-09-24', start: '15:00', end: '16:00', description: 'Demo en línea con prospecto.' }),
            buildEvent({ id: 'e5', title: 'Pago proveedor', type: 'b', date: '2026-09-30', description: 'Transferencia programada.' })
        ],
        dateClick: function (info) {
            if (info.jsEvent && info.jsEvent.target && info.jsEvent.target.closest && info.jsEvent.target.closest('.fc-event')) {
                return;
            }
            openDayModal(info.dateStr.slice(0, 10));
        },
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            info.jsEvent.stopPropagation();
            var props = info.event.extendedProps || {};
            openEventModal({
                id: info.event.id,
                title: info.event.title,
                type: props.type || 'a',
                date: toDateInput(info.event.start),
                start: info.event.start && props.hadTime ? formatTime(info.event.start) : '',
                end: info.event.end && props.hadTime ? formatTime(info.event.end) : '',
                description: props.description || '',
                ref: info.event
            });
        },
        eventDrop: function (info) {
            if (!confirm('¿Mover “' + info.event.title + '” a ' + (info.event.start ? toDateInput(info.event.start) : '') + '?')) {
                info.revert();
            } else {
                showNotice('Evento actualizado.');
                updateCount();
            }
        },
        eventResize: function (info) {
            showNotice('Duración actualizada: ' + info.event.title);
            updateCount();
        },
        datesSet: function () {
            updateCount();
        }
    });

    function forceHide(modalEl) {
        var inst = bootstrap.Modal.getInstance(modalEl);
        if (inst) {
            try {
                inst._isTransitioning = false;
                if (inst._isShown) inst.hide();
            } catch (e) {}
        }
        modalEl.classList.remove('show');
        modalEl.style.display = 'none';
        modalEl.setAttribute('aria-hidden', 'true');
        modalEl.removeAttribute('aria-modal');
        modalEl.style.removeProperty('padding-right');
        modalEl.style.removeProperty('padding-left');
        document.querySelectorAll('body > .modal-backdrop').forEach(function (b) { b.remove(); });
        if (!document.querySelector('.modal.show')) {
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }
        if (inst) {
            inst._isShown = false;
            inst._isTransitioning = false;
        }
    }

    function forceShow(modalEl) {
        document.querySelectorAll('body > .modal.show').forEach(function (m) {
            if (m !== modalEl) forceHide(m);
        });
        if (evForceTimer) {
            clearTimeout(evForceTimer);
            evForceTimer = null;
        }
        var wasShown = modalEl.classList.contains('show');
        var inst = bootstrap.Modal.getInstance(modalEl);
        if (inst) {
            inst._isShown = false;
            inst._isTransitioning = false;
        }
        if (wasShown) {
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            document.querySelectorAll('body > .modal-backdrop').forEach(function (b) { b.remove(); });
        }
        modalEl.style.display = '';
        modalEl.removeAttribute('aria-hidden');
        inst = bootstrap.Modal.getOrCreateInstance(modalEl);
        inst._isShown = false;
        inst._isTransitioning = false;
        inst.show();
        return inst;
    }

    function openEventModal(data) {
        evForm.reset();
        evError.hidden = true;
        evId.value = data && data.id ? data.id : '';
        evTitle.value = data && data.title ? data.title : '';
        evType.value = data && data.type ? data.type : 'a';
        evDate.value = data && data.date ? data.date : toDateInput(new Date());
        evStart.value = data && data.start ? data.start : '';
        evEnd.value = data && data.end ? data.end : '';
        evDesc.value = data && data.description ? data.description : '';
        document.getElementById('evModalTitle').textContent = data && data.id ? 'Editar evento' : 'Nuevo evento';
        evSave.querySelector('span').textContent = data && data.id ? 'Guardar cambios' : 'Guardar evento';
        evModal._calRef = data && data.ref ? data.ref : null;
        evModal = forceShow(evModalEl);
        setTimeout(function () {
            if (evTitle) evTitle.focus();
        }, 220);
    }

    function openDayModal(iso) {
        currentDay = iso;
        dayTitle.textContent = 'Detalles del día';
        dayDesc.textContent = formatDayLabel(iso);
        renderDayList(iso);
        dayModal = forceShow(dayModalEl);
    }

    function getEventsForDay(iso) {
        return calendar.getEvents().filter(function (ev) {
            return ev.start && toDateInput(ev.start) === iso;
        }).sort(function (a, b) {
            if (!a.start) return 1;
            if (!b.start) return -1;
            return a.start - b.start;
        });
    }

    function renderDayList(iso) {
        var events = getEventsForDay(iso);
        dayList.innerHTML = '';

        if (!events.length) {
            dayList.innerHTML =
                '<div class="tp-empty cal-day-empty">' +
                '<i class="fa-regular fa-folder-open" aria-hidden="true"></i>' +
                '<strong>Sin eventos</strong>' +
                '<span>No hay nada programado para este día.</span>' +
                '</div>';
            return;
        }

        events.forEach(function (ev) {
            var props = ev.extendedProps || {};
            var meta = TYPE_META[props.type] || TYPE_META.a;
            var time = (props.hadTime && ev.start)
                ? formatTime(ev.start) + (ev.end ? ' – ' + formatTime(ev.end) : '')
                : 'Todo el día';

            var item = document.createElement('div');
            item.className = 'cal-day-item';
            item.innerHTML =
                '<span class="cal-type-chip ' + meta.chip + '"><i class="fa-solid ' + meta.icon + '" aria-hidden="true"></i> ' + meta.label + '</span>' +
                '<div class="cal-day-item-main">' +
                '<div class="cal-day-item-title">' + escapeHtml(ev.title) + '</div>' +
                '<div class="cal-day-item-meta"><i class="fa-regular fa-clock" aria-hidden="true"></i> ' + escapeHtml(time) + '</div>' +
                (props.description ? '<div class="cal-day-item-desc">' + escapeHtml(props.description) + '</div>' : '') +
                '</div>' +
                '<div class="cal-day-item-actions">' +
                '<button type="button" class="pdsg-action pdsg-action-edit" title="Editar" aria-label="Editar evento"><i class="fa-solid fa-pen" aria-hidden="true"></i></button>' +
                '<button type="button" class="pdsg-action pdsg-action-danger" title="Eliminar" aria-label="Eliminar evento"><i class="fa-regular fa-trash-can" aria-hidden="true"></i></button>' +
                '</div>';

            item.querySelector('.pdsg-action-edit').addEventListener('click', function () {
                forceHide(dayModalEl);
                setTimeout(function () {
                    openEventModal({
                        id: ev.id,
                        title: ev.title,
                        type: props.type || 'a',
                        date: toDateInput(ev.start),
                        start: props.hadTime && ev.start ? formatTime(ev.start) : '',
                        end: props.hadTime && ev.end ? formatTime(ev.end) : '',
                        description: props.description || '',
                        ref: ev
                    });
                }, 50);
            });

            item.querySelector('.pdsg-action-danger').addEventListener('click', function () {
                if (!confirm('¿Eliminar “' + ev.title + '”?')) return;
                ev.remove();
                showNotice('Evento eliminado.');
                updateCount();
                renderDayList(iso);
            });

            dayList.appendChild(item);
        });
    }

    function escapeHtml(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function closeEvModal() {
        forceHide(evModalEl);
        evModal._calRef = null;
        evTitle.setCustomValidity('');
        evError.hidden = true;
    }

    document.getElementById('calAdd').addEventListener('click', function () {
        openEventModal(null);
    });

    dayAdd.addEventListener('click', function () {
        var day = currentDay;
        forceHide(dayModalEl);
        setTimeout(function () {
            openEventModal({ date: day || toDateInput(new Date()) });
        }, 50);
    });

    evForm.addEventListener('submit', function (event) {
        event.preventDefault();
        evTitle.value = evTitle.value.trim();
        evTitle.setCustomValidity(evTitle.value ? '' : 'Ingrese un título.');
        if (!evForm.reportValidity()) return;
        if (!evDate.value) {
            evError.textContent = 'Seleccione la fecha del evento.';
            evError.hidden = false;
            return;
        }
        if (evStart.value && evEnd.value && evEnd.value < evStart.value) {
            evError.textContent = 'La hora de fin debe ser posterior a la de inicio.';
            evError.hidden = false;
            return;
        }

        evError.hidden = true;
        var type = evType.value;
        var meta = TYPE_META[type] || TYPE_META.a;
        var start = evStart.value ? evDate.value + 'T' + evStart.value : evDate.value;
        var end = evEnd.value ? evDate.value + 'T' + evEnd.value : undefined;
        var existing = evModal._calRef;

        if (existing) {
            existing.setProp('title', evTitle.value);
            existing.setStart(start);
            if (end) existing.setEnd(end); else existing.setEnd(undefined);
            existing.setProp('classNames', [meta.cls]);
            existing.setExtendedProp('type', type);
            existing.setExtendedProp('description', evDesc.value.trim());
            existing.setExtendedProp('hadTime', !!evStart.value);
            showNotice('Evento actualizado.');
        } else {
            calendar.addEvent(buildEvent({
                title: evTitle.value,
                type: type,
                date: evDate.value,
                start: evStart.value || undefined,
                end: evEnd.value || undefined,
                description: evDesc.value.trim()
            }));
            showNotice('Evento creado.');
        }

        closeEvModal();
        updateCount();
        if (currentDay) renderDayList(currentDay);
    });

    evModalEl.addEventListener('hidden.bs.modal', function () {
        evModal._calRef = null;
        if (evTitle) evTitle.setCustomValidity('');
        evError.hidden = true;
    });

    document.querySelectorAll('#evModal [data-bs-dismiss="modal"], #dayModal [data-bs-dismiss="modal"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var modal = btn.closest('.modal');
            if (modal) forceHide(modal);
        });
    });

    calendar.render();
    updateCount();

    if (typeof gsap !== 'undefined' && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        gsap.fromTo('.cal-page > *', { opacity: 0, y: 10 }, {
            opacity: 1, y: 0, duration: .35, stagger: .06, ease: 'power2.out', clearProps: 'opacity,transform'
        });
    }
});
</script>
