/*
 * Buscador de universidades e institutos del Perú (públicos y privados).
 * Según el tipo elegido, el selector muestra ciclos o semestres:
 *   Universidad → Ciclo I … XIV   ·   Instituto → Semestre I … VI
 *
 * Uso en el HTML:
 *   <div data-inst>
 *     <input data-inst-input name="emp_institucion">
 *     <div data-inst-tipo> (botones Universidad / Instituto) </div>
 *     <select data-inst-semestre name="emp_semestre"></select>
 *   </div>
 * Se puede escribir cualquier otro nombre: el tipo se elige a mano.
 */
(function () {
    'use strict';

    // t: U = universidad, I = instituto · g: Pública / Privada
    var LISTA = [
        // ── Universidades públicas ──
        ['Universidad Nacional Mayor de San Marcos', 'UNMSM', 'U', 'Pública'],
        ['Universidad Nacional de Ingeniería', 'UNI', 'U', 'Pública'],
        ['Universidad Nacional Agraria La Molina', 'UNALM', 'U', 'Pública'],
        ['Universidad Nacional Federico Villarreal', 'UNFV', 'U', 'Pública'],
        ['Universidad Nacional del Callao', 'UNAC', 'U', 'Pública'],
        ['Universidad Nacional de Educación Enrique Guzmán y Valle', 'UNE La Cantuta', 'U', 'Pública'],
        ['Universidad Nacional Tecnológica de Lima Sur', 'UNTELS', 'U', 'Pública'],
        ['Universidad Nacional José Faustino Sánchez Carrión', 'UNJFSC', 'U', 'Pública'],
        ['Universidad Nacional de San Agustín de Arequipa', 'UNSA', 'U', 'Pública'],
        ['Universidad Nacional de Trujillo', 'UNT', 'U', 'Pública'],
        ['Universidad Nacional de San Antonio Abad del Cusco', 'UNSAAC', 'U', 'Pública'],
        ['Universidad Nacional de Piura', 'UNP', 'U', 'Pública'],
        ['Universidad Nacional Pedro Ruiz Gallo', 'UNPRG', 'U', 'Pública'],
        ['Universidad Nacional del Centro del Perú', 'UNCP', 'U', 'Pública'],
        ['Universidad Nacional del Altiplano', 'UNA Puno', 'U', 'Pública'],
        ['Universidad Nacional de la Amazonía Peruana', 'UNAP', 'U', 'Pública'],
        ['Universidad Nacional de Cajamarca', 'UNC', 'U', 'Pública'],
        ['Universidad Nacional San Luis Gonzaga', 'UNICA', 'U', 'Pública'],
        ['Universidad Nacional Hermilio Valdizán', 'UNHEVAL', 'U', 'Pública'],
        ['Universidad Nacional de San Cristóbal de Huamanga', 'UNSCH', 'U', 'Pública'],
        ['Universidad Nacional Jorge Basadre Grohmann', 'UNJBG', 'U', 'Pública'],
        ['Universidad Nacional Santiago Antúnez de Mayolo', 'UNASAM', 'U', 'Pública'],
        ['Universidad Nacional del Santa', 'UNS', 'U', 'Pública'],
        ['Universidad Nacional de Ucayali', 'UNU', 'U', 'Pública'],
        ['Universidad Nacional de Tumbes', 'UNTUMBES', 'U', 'Pública'],
        ['Universidad Nacional de San Martín', 'UNSM', 'U', 'Pública'],
        ['Universidad Nacional Toribio Rodríguez de Mendoza de Amazonas', 'UNTRM', 'U', 'Pública'],
        ['Universidad Nacional Daniel Alcides Carrión', 'UNDAC', 'U', 'Pública'],
        ['Universidad Nacional de Huancavelica', 'UNH', 'U', 'Pública'],
        ['Universidad Nacional Micaela Bastidas de Apurímac', 'UNAMBA', 'U', 'Pública'],
        ['Universidad Nacional Amazónica de Madre de Dios', 'UNAMAD', 'U', 'Pública'],
        ['Universidad Nacional de Moquegua', 'UNAM', 'U', 'Pública'],
        ['Universidad Nacional de Frontera', 'UNF', 'U', 'Pública'],
        ['Universidad Nacional de Barranca', 'UNAB', 'U', 'Pública'],
        ['Universidad Nacional de Cañete', 'UNDC', 'U', 'Pública'],
        ['Universidad Nacional Intercultural de la Amazonía', 'UNIA', 'U', 'Pública'],
        ['Universidad Nacional de Juliaca', 'UNAJ', 'U', 'Pública'],
        ['Universidad Nacional de Jaén', 'UNJ', 'U', 'Pública'],
        ['Universidad Nacional José María Arguedas', 'UNAJMA', 'U', 'Pública'],
        ['Universidad Nacional Autónoma de Chota', 'UNACH', 'U', 'Pública'],
        ['Universidad Nacional Autónoma de Huanta', 'UNAH', 'U', 'Pública'],
        ['Universidad Nacional Autónoma Altoandina de Tarma', 'UNAAT', 'U', 'Pública'],

        // ── Universidades privadas ──
        ['Pontificia Universidad Católica del Perú', 'PUCP', 'U', 'Privada'],
        ['Universidad Peruana Cayetano Heredia', 'UPCH', 'U', 'Privada'],
        ['Universidad de Lima', 'ULima', 'U', 'Privada'],
        ['Universidad del Pacífico', 'UP', 'U', 'Privada'],
        ['Universidad de Piura', 'UDEP', 'U', 'Privada'],
        ['Universidad Peruana de Ciencias Aplicadas', 'UPC', 'U', 'Privada'],
        ['Universidad de San Martín de Porres', 'USMP', 'U', 'Privada'],
        ['Universidad Ricardo Palma', 'URP', 'U', 'Privada'],
        ['Universidad San Ignacio de Loyola', 'USIL', 'U', 'Privada'],
        ['Universidad Tecnológica del Perú', 'UTP', 'U', 'Privada'],
        ['Universidad César Vallejo', 'UCV', 'U', 'Privada'],
        ['Universidad Privada del Norte', 'UPN', 'U', 'Privada'],
        ['Universidad Continental', 'UC', 'U', 'Privada'],
        ['Universidad Científica del Sur', 'UCSUR', 'U', 'Privada'],
        ['Universidad ESAN', 'ESAN', 'U', 'Privada'],
        ['Universidad de Ingeniería y Tecnología', 'UTEC', 'U', 'Privada'],
        ['Universidad Peruana Unión', 'UPeU', 'U', 'Privada'],
        ['Universidad Femenina del Sagrado Corazón', 'UNIFÉ', 'U', 'Privada'],
        ['Universidad Antonio Ruiz de Montoya', 'UARM', 'U', 'Privada'],
        ['Universidad Católica San Pablo', 'UCSP', 'U', 'Privada'],
        ['Universidad Católica de Santa María', 'UCSM', 'U', 'Privada'],
        ['Universidad Católica Santo Toribio de Mogrovejo', 'USAT', 'U', 'Privada'],
        ['Universidad Católica Sedes Sapientiae', 'UCSS', 'U', 'Privada'],
        ['Universidad Privada Antenor Orrego', 'UPAO', 'U', 'Privada'],
        ['Universidad Señor de Sipán', 'USS', 'U', 'Privada'],
        ['Universidad Andina del Cusco', 'UAC', 'U', 'Privada'],
        ['Universidad Peruana Los Andes', 'UPLA', 'U', 'Privada'],
        ['Universidad Privada de Tacna', 'UPT', 'U', 'Privada'],
        ['Universidad Inca Garcilaso de la Vega', 'UIGV', 'U', 'Privada'],
        ['Universidad Autónoma del Perú', 'UA', 'U', 'Privada'],
        ['Universidad Privada San Juan Bautista', 'UPSJB', 'U', 'Privada'],
        ['Universidad Norbert Wiener', 'UWiener', 'U', 'Privada'],
        ['Universidad de Ciencias y Humanidades', 'UCH', 'U', 'Privada'],
        ['Universidad Privada Telesup', 'Telesup', 'U', 'Privada'],
        ['Universidad Le Cordon Bleu', 'ULCB', 'U', 'Privada'],
        ['Universidad de Ciencias y Artes de América Latina', 'UCAL', 'U', 'Privada'],
        ['Universidad Marcelino Champagnat', 'UMCH', 'U', 'Privada'],
        ['Universidad Peruana de Ciencias e Informática', 'UPCI', 'U', 'Privada'],
        ['Universidad María Auxiliadora', 'UMA', 'U', 'Privada'],
        ['Universidad Jaime Bausate y Meza', 'UJBM', 'U', 'Privada'],
        ['Universidad Privada Peruano Alemana', 'UPAL', 'U', 'Privada'],

        // ── Institutos privados ──
        ['SENATI', 'SENATI', 'I', 'Privada'],
        ['TECSUP', 'TECSUP', 'I', 'Privada'],
        ['Instituto Cibertec', 'CIBERTEC', 'I', 'Privada'],
        ['Instituto IDAT', 'IDAT', 'I', 'Privada'],
        ['Instituto San Ignacio de Loyola (ISIL)', 'ISIL', 'I', 'Privada'],
        ['Toulouse Lautrec', 'Toulouse Lautrec', 'I', 'Privada'],
        ['Instituto Certus', 'CERTUS', 'I', 'Privada'],
        ['Zegel (ex IPAE)', 'ZEGEL', 'I', 'Privada'],
        ['Instituto SISE', 'SISE', 'I', 'Privada'],
        ['Instituto Avansys', 'AVANSYS', 'I', 'Privada'],
        ['Instituto Columbia', 'COLUMBIA', 'I', 'Privada'],
        ['Instituto Khipu', 'KHIPU', 'I', 'Privada'],
        ['Instituto Daniel Alcides Carrión', 'IDAC', 'I', 'Privada'],
        ['Instituto Arzobispo Loayza', 'Arzobispo Loayza', 'I', 'Privada'],
        ['Instituto Euroidiomas', 'EUROIDIOMAS', 'I', 'Privada'],
        ['SENCICO', 'SENCICO', 'I', 'Pública'],

        // ── Institutos públicos ──
        ['IESTP Carlos Cueto Fernandini', 'Carlos Cueto', 'I', 'Pública'],
        ['IESTP Manuel Seoane Corrales', 'Manuel Seoane', 'I', 'Pública'],
        ['IESTP Julio César Tello', 'Julio C. Tello', 'I', 'Pública'],
        ['IESTP José Pardo', 'José Pardo', 'I', 'Pública'],
        ['IESTP Argentina', 'Argentina', 'I', 'Pública'],
        ['IESTP Luis Negreiros Vega', 'Luis Negreiros', 'I', 'Pública'],
        ['IESTP Gilda Liliana Ballivián Rosado', 'Gilda Ballivián', 'I', 'Pública'],
        ['IESTP Huaycán', 'Huaycán', 'I', 'Pública'],
        ['IESTP Villa María', 'Villa María', 'I', 'Pública'],
        ['Escuela Nacional Superior de Bellas Artes', 'ENSABAP', 'I', 'Pública'],
        ['Instituto Pedagógico Nacional Monterrico', 'IPNM', 'I', 'Pública']
    ];

    var ROMANOS = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII', 'XIII', 'XIV'];

    function norm(s) {
        return String(s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').trim();
    }
    function buscar(q) {
        q = norm(q);
        if (!q) return LISTA.slice(0, 8);
        var palabras = q.split(/\s+/);
        return LISTA.filter(function (it) {
            var txt = norm(it[0] + ' ' + it[1]);
            return palabras.every(function (p) { return txt.indexOf(p) !== -1; });
        }).sort(function (a, b) {
            // Primero las que empiezan por la sigla o el nombre buscado
            var sa = norm(a[1]).indexOf(q) === 0 || norm(a[0]).indexOf(q) === 0 ? 0 : 1;
            var sb = norm(b[1]).indexOf(q) === 0 || norm(b[0]).indexOf(q) === 0 ? 0 : 1;
            return sa - sb;
        }).slice(0, 8);
    }
    function tipoDe(nombre) {
        var n = norm(nombre);
        if (!n) return '';
        for (var i = 0; i < LISTA.length; i++) {
            if (norm(LISTA[i][0]) === n || norm(LISTA[i][1]) === n) return LISTA[i][2];
        }
        if (/universidad|universitari/.test(n)) return 'U';
        if (/instituto|iestp|iest|escuela|senati|tecsup|cibertec|idat|isil|certus|sise|zegel/.test(n)) return 'I';
        return '';
    }

    function esc(s) {
        return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; });
    }

    function montar(box) {
        if (box.__inst) return box.__inst;
        var input = box.querySelector('[data-inst-input]');
        var sel = box.querySelector('[data-inst-semestre]');
        var tipoBox = box.querySelector('[data-inst-tipo]');
        if (!input || !sel) return null;

        input.setAttribute('autocomplete', 'off');
        input.setAttribute('role', 'combobox');
        input.setAttribute('aria-autocomplete', 'list');
        var wrap = input.parentNode;
        wrap.style.position = 'relative';
        var lista = document.createElement('div');
        lista.className = 'inst-lista';
        lista.setAttribute('role', 'listbox');
        wrap.appendChild(lista);

        var tipo = '';
        var activo = -1;
        var items = [];

        function opciones(t, actual) {
            var pref = t === 'I' ? 'Semestre ' : 'Ciclo ';
            var max = t === 'I' ? 6 : 14;
            var html = '<option value="">' + (t ? 'Elegir…' : 'Elige el tipo primero…') + '</option>';
            var hay = false;
            if (t) {
                for (var i = 0; i < max; i++) {
                    var v = pref + ROMANOS[i];
                    if (v === actual) hay = true;
                    html += '<option value="' + v + '">' + v + '</option>';
                }
            }
            // Un valor antiguo que no está en la lista se conserva
            if (actual && !hay) html += '<option value="' + esc(actual) + '">' + esc(actual) + '</option>';
            sel.innerHTML = html;
            sel.value = actual || '';
        }
        function ponerTipo(t, conservar) {
            tipo = t;
            if (tipoBox) {
                tipoBox.querySelectorAll('button[data-t]').forEach(function (b) {
                    var on = b.getAttribute('data-t') === t;
                    b.classList.toggle('is-on', on);
                    b.setAttribute('aria-pressed', on ? 'true' : 'false');
                });
            }
            var actual = conservar ? sel.value : '';
            // Al cambiar de tipo, un «Ciclo» no sirve como «Semestre» y viceversa
            if (actual && t && actual.indexOf(t === 'I' ? 'Semestre' : 'Ciclo') !== 0) actual = '';
            opciones(t, actual);
        }
        function cerrar() { lista.classList.remove('is-open'); activo = -1; input.setAttribute('aria-expanded', 'false'); }
        function pintar() {
            items = buscar(input.value);
            if (!items.length) {
                lista.innerHTML = '<div class="inst-vacio">Sin resultados: puedes dejar el nombre escrito y elegir el tipo.</div>';
            } else {
                lista.innerHTML = items.map(function (it, i) {
                    return '<button type="button" role="option" class="inst-op' + (i === activo ? ' is-act' : '') + '" data-i="' + i + '">'
                        + '<span class="inst-n">' + esc(it[0]) + '</span>'
                        + '<span class="inst-m">' + esc(it[1]) + ' · ' + (it[2] === 'U' ? 'Universidad ' + it[3].toLowerCase() : 'Instituto ' + (it[3] === 'Pública' ? 'público' : 'privado')) + '</span>'
                        + '</button>';
                }).join('');
            }
            lista.classList.add('is-open');
            input.setAttribute('aria-expanded', 'true');
        }
        function elegir(i) {
            var it = items[i];
            if (!it) return;
            input.value = it[0];
            ponerTipo(it[2], false);
            cerrar();
            sel.focus();
        }

        input.addEventListener('focus', pintar);
        input.addEventListener('input', function () {
            activo = -1;
            pintar();
            var t = tipoDe(input.value);
            if (t && t !== tipo) ponerTipo(t, true);
        });
        input.addEventListener('keydown', function (e) {
            if (!lista.classList.contains('is-open')) return;
            if (e.key === 'ArrowDown') { e.preventDefault(); activo = Math.min(activo + 1, items.length - 1); pintar(); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); activo = Math.max(activo - 1, 0); pintar(); }
            else if (e.key === 'Enter' && activo >= 0) { e.preventDefault(); elegir(activo); }
            else if (e.key === 'Escape') { e.stopPropagation(); cerrar(); }
        });
        lista.addEventListener('mousedown', function (e) { e.preventDefault(); });
        lista.addEventListener('click', function (e) {
            var b = e.target.closest('.inst-op');
            if (b) elegir(+b.getAttribute('data-i'));
        });
        input.addEventListener('blur', function () { setTimeout(cerrar, 120); });
        if (tipoBox) {
            tipoBox.addEventListener('click', function (e) {
                var b = e.target.closest('button[data-t]');
                if (b) ponerTipo(b.getAttribute('data-t'), true);
            });
        }

        var api = {
            /** Carga los valores guardados (al editar). */
            set: function (nombre, semestre) {
                input.value = nombre || '';
                var t = tipoDe(nombre);
                if (!t && semestre) t = /^semestre/i.test(semestre) ? 'I' : 'U';
                tipo = t;
                if (tipoBox) {
                    tipoBox.querySelectorAll('button[data-t]').forEach(function (b) {
                        var on = b.getAttribute('data-t') === t;
                        b.classList.toggle('is-on', on);
                        b.setAttribute('aria-pressed', on ? 'true' : 'false');
                    });
                }
                opciones(t, semestre || '');
            }
        };
        box.__inst = api;
        api.set(input.value, sel.value);
        return api;
    }

    window.dsgInstituciones = {
        montar: montar,
        montarTodo: function () { document.querySelectorAll('[data-inst]').forEach(montar); }
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.dsgInstituciones.montarTodo);
    } else {
        window.dsgInstituciones.montarTodo();
    }
})();
