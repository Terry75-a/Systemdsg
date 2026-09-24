/* ================================================
   ANIMATED COUNTER COMPONENT
   ================================================ */

class AnimatedCounter {
  constructor(element, finalValue, duration = 2000) {
    this.element = element;
    this.finalValue = finalValue;
    this.duration = duration;
    this.hasAnimated = false;
  }

  // Extract number from text (handles +, %, etc.)
  extractNumber(text) {
    const match = text.match(/[\d.]+/);
    return match ? parseFloat(match[0]) : 0;
  }

  // Get suffix from text (+, %, etc.)
  getSuffix(text) {
    return text.replace(/[\d.]+/, '');
  }

  // Animate the counter
  animate() {
    if (this.hasAnimated) return;
    
    const finalNumber = this.extractNumber(this.finalValue);
    const suffix = this.getSuffix(this.finalValue);
    const startTime = Date.now();
    
    const updateCounter = () => {
      const elapsed = Date.now() - startTime;
      const progress = Math.min(elapsed / this.duration, 1);
      
      // Easing function for smooth animation
      const easeOutQuart = 1 - Math.pow(1 - progress, 4);
      const currentValue = finalNumber * easeOutQuart;
      
      // Format number based on original format
      let displayValue;
      if (this.finalValue.includes('.')) {
        displayValue = currentValue.toFixed(1);
      } else {
        displayValue = Math.floor(currentValue);
      }
      
      this.element.textContent = displayValue + suffix;
      
      if (progress < 1) {
        requestAnimationFrame(updateCounter);
      } else {
        this.element.textContent = this.finalValue;
        this.hasAnimated = true;
      }
    };
    
    requestAnimationFrame(updateCounter);
  }

  // Check if element is in viewport
  isInViewport() {
    const rect = this.element.getBoundingClientRect();
    return (
      rect.top >= 0 &&
      rect.left >= 0 &&
      rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
  }
}

// Initialize counters when DOM is loaded
function initializeCounters() {
  const counterElements = document.querySelectorAll('.stat-number');
  const counters = [];
  
  counterElements.forEach(element => {
    const originalValue = element.textContent;
    const counter = new AnimatedCounter(element, originalValue, 2500);
    counters.push(counter);
    
    // Set initial value to 0
    const suffix = counter.getSuffix(originalValue);
    element.textContent = '0' + suffix;
  });
  
  // Function to check and animate visible counters
  function checkAndAnimateCounters() {
    counters.forEach(counter => {
      if (!counter.hasAnimated && counter.isInViewport()) {
        // Add small delay for staggered effect
        setTimeout(() => {
          counter.animate();
        }, Math.random() * 500);
      }
    });
  }
  
  // Check on scroll and resize
  window.addEventListener('scroll', checkAndAnimateCounters);
  window.addEventListener('resize', checkAndAnimateCounters);
  
  // Initial check after a short delay
  setTimeout(() => {
    checkAndAnimateCounters();
  }, 500);
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeCounters);
} else {
  initializeCounters();
}

// Export for manual initialization if needed
window.initializeCounters = initializeCounters;