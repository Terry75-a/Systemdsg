/* Presentación del editor. No modifica campos ni interviene en el guardado. */
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formPersona');
    if (!form) return;
    const scroll = form.querySelector('.pe-scroll');
    const links = Array.from(form.querySelectorAll('.pe-nav-link'));
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const sections = links.map(function (link) { return form.querySelector(link.getAttribute('href')); });
    const framed = document.body.classList.contains('pe-frame');
    function updateNavigation() {
        const top = framed ? scroll.getBoundingClientRect().top : 0;
        let current = sections[0];
        sections.forEach(function (section) {
            if (section.getBoundingClientRect().top <= top + 90) current = section;
        });
        links.forEach(function (link, index) {
            if (sections[index] === current) link.setAttribute('aria-current', 'location');
            else link.removeAttribute('aria-current');
        });
    }
    links.forEach(function (link, index) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            sections[index].scrollIntoView({ behavior: reduced.matches ? 'instant' : 'smooth', block: 'start' });
        });
    });
    (framed ? scroll : window).addEventListener('scroll', updateNavigation, { passive: true });
    updateNavigation();
    // Bootstrap vive en la página padre; Escape dentro del iframe no burbujea hacia ella.
    if (framed && window.parent !== window) {
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !document.querySelector('.modal.show')) {
                window.parent.postMessage({ tipo: 'pdsg:cerrarModalPersona' }, window.location.origin);
            }
        });
    }
    if (typeof gsap !== 'undefined' && !reduced.matches) {
        gsap.timeline({ defaults: { ease: 'power2.out', clearProps: 'opacity,transform' } })
            .fromTo(form.querySelector('.pdsg-add-head'), { opacity: 0, y: 8 }, { opacity: 1, y: 0, duration: .3 })
            .fromTo(form.querySelector('.pe-sidebar'), { opacity: 0, x: -10 }, { opacity: 1, x: 0, duration: .3 }, '-=.2')
            .fromTo(sections, { opacity: 0, y: 14 }, { opacity: 1, y: 0, duration: .35, stagger: .06 }, '-=.18');
    }
});
