// =========================================
// CONTADOR PARA INDEX.HTML - HERO 2
// =========================================

class CounterAnimation {
    constructor(element, target, duration = 2000, suffix = '', decimals = 0) {
        this.element = element;
        this.target = target;
        this.duration = duration;
        this.suffix = suffix;
        this.decimals = decimals;
        this.startTime = null;
        this.startValue = 0;
    }

    easeOutQuart(t) {
        return 1 - Math.pow(1 - t, 4);
    }

    formatNumber(num) {
        if (this.decimals > 0) {
            return num.toFixed(this.decimals);
        }
        return Math.floor(num).toString();
    }

    animate(currentTime) {
        if (!this.startTime) this.startTime = currentTime;
        const elapsed = currentTime - this.startTime;
        const progress = Math.min(elapsed / this.duration, 1);
        const easedProgress = this.easeOutQuart(progress);
        
        const currentValue = this.startValue + (this.target - this.startValue) * easedProgress;
        this.element.textContent = this.formatNumber(currentValue) + this.suffix;
        
        if (progress < 1) {
            requestAnimationFrame((time) => this.animate(time));
        } else {
            this.element.textContent = this.formatNumber(this.target) + this.suffix;
        }
    }

    start() {
        requestAnimationFrame((time) => this.animate(time));
    }
}

// Inicializar contadores del index
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter[data-count]');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                entry.target.classList.add('counted');
                
                const target = parseFloat(entry.target.dataset.count);
                const suffix = entry.target.dataset.suffix || '';
                const duration = parseInt(entry.target.dataset.duration) || 2000;
                const decimals = parseInt(entry.target.dataset.decimals) || 0;
                
                const counter = new CounterAnimation(entry.target, target, duration, suffix, decimals);
                counter.start();
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
});
