document.addEventListener("DOMContentLoaded", function () {

    

    fetch("<?= base_url('ubigeo/departamentos') ?>")
        .then(res => res.json())
        .then(data => {
            let deptSelect = document.getElementById("departamento");
            data.forEach(d => {
                deptSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
            });
        });

    document.getElementById("departamento").addEventListener("change", function(){
        let departamentoId = this.value;

        fetch("<?= base_url('ubigeo/provincias') ?>/" + departamentoId)
            .then(res => res.json())
            .then(data => {
                let provSelect = document.getElementById("provincia");
                provSelect.innerHTML = `<option selected disabled>Seleccionar</option>`;

                data.forEach(p => {
                    provSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                });

                document.getElementById("distrito").innerHTML =
                    `<option selected disabled>Selecciona un distrito</option>`;
            });
    });

    document.getElementById("provincia").addEventListener("change", function(){
        let provinciaId = this.value;

        fetch("<?= base_url('ubigeo/distritos') ?>/" + provinciaId)
            .then(res => res.json())
            .then(data => {
                let distSelect = document.getElementById("distrito");
                distSelect.innerHTML = `<option selected disabled>Selecciona un distrito</option>`;

                data.forEach(d => {
                    distSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
                });
            });
    });
});