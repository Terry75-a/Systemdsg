class BasicCarousel {
  constructor(container) {
    this.container = container;
    this.track = container.querySelector('.carousel-track');
    this.slides = Array.from(container.querySelectorAll('.carousel-slide'));
    this.currentIndex = 0;
    this.slideCount = this.slides.length;
    
    // Duplicar slides para crear efecto continuo
    this.duplicateSlides();
    
    this.init();
  }
  
  duplicateSlides() {
    // Crear copias de los slides al final para efecto continuo
    this.slides.forEach(slide => {
      const clone = slide.cloneNode(true);
      this.track.appendChild(clone);
    });
    
    // Actualizar la lista de slides
    this.slides = Array.from(this.container.querySelectorAll('.carousel-slide'));
    this.slideCount = this.slides.length;
  }
  
  init() {
    // Iniciar el carrusel automático
    this.startAutoPlay();
    
    // Pausar al pasar el mouse
    this.container.addEventListener('mouseenter', () => {
      this.stopAutoPlay();
    });
    
    this.container.addEventListener('mouseleave', () => {
      this.startAutoPlay();
    });
  }
  
  nextSlide() {
    this.currentIndex = (this.currentIndex + 1);
    this.updateSlidePosition();
    
    // Reiniciar sin salto brusco cuando llegamos al final
    if (this.currentIndex >= this.slideCount / 2) {
      setTimeout(() => {
        this.currentIndex = 0;
        this.track.style.transition = 'none';
        this.updateSlidePosition();
        // Forzar reflow
        this.track.offsetHeight;
        this.track.style.transition = 'transform 0.5s ease-in-out';
      }, 500);
    }
  }
  
  updateSlidePosition() {
    const translateX = -this.currentIndex * 100;
    this.track.style.transform = `translateX(${translateX}%)`;
  }
  
  startAutoPlay() {
    this.autoPlayInterval = setInterval(() => {
      this.nextSlide();
    }, 1200); // Cambiar cada 1.5 segundos
  }
  
  stopAutoPlay() {
    if (this.autoPlayInterval) {
      clearInterval(this.autoPlayInterval);
    }
  }
}

// Inicializar el carrusel cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
  const carouselElement = document.querySelector('.carousel-basic');
  if (carouselElement) {
    new BasicCarousel(carouselElement);
  }
});