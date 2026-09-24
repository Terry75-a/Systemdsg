const btnBuscarDni = document.getElementById('btnBuscarDni');
    const inputDni     = document.getElementById('dni');
    const inputNombre  = document.getElementById('nombre');
    const inputApellido= document.getElementById('apellido');
    const inputApellidoMaterno = document.getElementById('apellido_materno');
    const inputApellidoPaterno = document.getElementById('apellido_paterno');

    btnBuscarDni.addEventListener('click', function () {

        const dni = inputDni.value.trim();

        if (dni.length !== 8 || isNaN(dni)) {
            alert("El DNI debe tener 8 dígitos numéricos");
            return;
        }

        fetch('<?= base_url("clientes/buscar-dni") ?>', {
            method: 'POST',
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "dni=" + encodeURIComponent(dni)
        })
        .then(res => res.json())
        .then(data => {

            if (data.success) {
                inputNombre.value   = data.nombres;
                inputApellidoMaterno.value = data.apellido_materno;
                inputApellidoPaterno.value = data.apellido_paterno;

            } else {
                alert(data.message || "No se encontró el DNI");
                inputNombre.value = "";
                inputApellido.value = "";
            }

        })
        .catch(err => {
            console.error(err);
            alert("Error al consultar el DNI");
        });

    });