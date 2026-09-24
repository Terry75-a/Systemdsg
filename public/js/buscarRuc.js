const btnBuscarRuc = document.getElementById('btnBuscarRuc');
    const inputRuc     = document.getElementById('ruc');
    const inputRazon  = document.getElementById('razon_social');
    const inputDireccion = document.getElementById('direccion');

    btnBuscarRuc.addEventListener('click' , function (){
        const ruc = inputRuc.value.trim();

        if(ruc.length !== 11 || isNaN(ruc)){
          alert('EL RUC DEBE SER DE 12  DIGITOS')
          return;
        }

        fetch('<?= base_url("clientes/buscar-ruc") ?>', {
            method: 'POST',
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "ruc=" + encodeURIComponent(ruc)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                inputRazon.value = data.razon;
                inputDireccion.value = data.direccion;
            }else{
                alert(data.message || "Nose encontre la RUC");
                inputRazon.value = "";
                inputDireccion.value = "";
            }
        })
        .catch(err => {
            console.error(err);
            alert("Nose encontre el RUC");
        });
    });