xd = document.getElementById("stas-number");
years = document.getElementById("experience-years");
uptime = document.getElementById("uptime-guaranteed");

function animateValue(element, start, end, duration , decimals = 0) {

    let range = end - start;
    let step = 100;
    let increment = range/ step;
    let current = start;
    let timer = setInterval(function() {
        current += increment;
        if (current >= end) {
            current = end;
            clearInterval(timer);
        }
        if (decimals > 0) {
            element.textContent = current.toFixed(decimals);
        }
        else {
            element.textContent = Math.floor(current);
        }
    }, duration / step);
}


window.onload = function() {
    animateValue(document.getElementById("stas-number"), 0, 500, 2000);
    animateValue(document.getElementById("experience-years"), 0, 5, 2000);
    animateValue(document.getElementById("uptime-guaranteed"), 0, 99.9, 2000, 1);
}

function textoalcostado() {
    var xd =  document.getElementById("xd").innerHTML = "500+";
}
