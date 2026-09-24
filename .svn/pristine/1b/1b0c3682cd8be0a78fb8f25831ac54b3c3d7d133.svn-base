/* ================================================
   NAVBAR SCROLL ANIMATIONS
   ================================================ */

// Navbar scroll animation class
class NavbarScrollAnimation {
  constructor() {
    this.navbar = document.querySelector('.navbar');
    this.isScrolled = false;
    this.scrollThreshold = 50; // Pixels to start animation
    this.sections = [];
    this.init();
  }

  init() {
    if (!this.navbar) return;
    
    // Get all sections
    this.sections = [
      { element: document.querySelector('.hero-section'), bgColor: 'dark' },
      { element: document.querySelector('.services-section'), bgColor: 'light' },
      { element: document.querySelector('.features-section'), bgColor: 'dark' },
      { element: document.querySelector('.testimonial-section'), bgColor: 'light' }
    ];
    
    // Add scroll event listener with throttle
    this.addScrollListener();
    
    // Check initial scroll position
    this.checkScrollPosition();
    
    // Initialize text color
    this.updateNavbarTextColor();
  }

  addScrollListener() {
    let ticking = false;
    
    const updateNavbar = () => {
      this.checkScrollPosition();
      this.updateNavbarTextColor();
      ticking = false;
    };

    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(updateNavbar);
        ticking = true;
      }
    });
  }

  checkScrollPosition() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const shouldBeScrolled = scrollTop > this.scrollThreshold;

    if (shouldBeScrolled !== this.isScrolled) {
      this.isScrolled = shouldBeScrolled;
      this.updateNavbarState();
    }
  }

  updateNavbarState() {
    if (this.isScrolled) {
      this.navbar.classList.add('navbar-scrolled');
    } else {
      this.navbar.classList.remove('navbar-scrolled');
    }
  }

  updateNavbarTextColor() {
    // Check if dark theme is active
    const isDarkTheme = document.documentElement.getAttribute('data-theme') === 'dark';
    
    // Get mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenuIcon = mobileMenuToggle ? mobileMenuToggle.querySelector('.material-icons') : null;
    
    // In dark mode, keep everything white - no section-based changes
    if (isDarkTheme) {
      this.setNavbarColors('white');
      // Ensure mobile menu icon is white in dark mode
      if (mobileMenuIcon) {
        mobileMenuIcon.style.color = 'white';
      }
      return;
    }
    
    // In light mode, change colors based on section background
    // Get navbar position
    const navbarRect = this.navbar.getBoundingClientRect();
    const navbarCenterY = navbarRect.top + navbarRect.height / 2;
    
    // Check which section is behind the navbar
    let currentSectionBg = 'light'; // default to light
    
    for (const section of this.sections) {
      if (!section.element) continue;
      
      const sectionRect = section.element.getBoundingClientRect();
      
      // Check if navbar is over this section
      if (navbarCenterY >= sectionRect.top && navbarCenterY <= sectionRect.bottom) {
        currentSectionBg = section.bgColor;
        break;
      }
    }
    
    // Update navbar text color based on section background
    if (currentSectionBg === 'dark') {
      // Dark background - use light text
      this.setNavbarColors('white');
      if (mobileMenuIcon) {
        mobileMenuIcon.style.color = 'white';
      }
    } else {
      // Light background - use dark text
      this.setNavbarColors('black');
      if (mobileMenuIcon) {
        mobileMenuIcon.style.color = 'black';
      }
    }
  }
  
  setNavbarColors(color) {
    const navLinks = this.navbar.querySelectorAll('.nav-link');
    const logoText = this.navbar.querySelector('.logo-text');
    const themeIcon = this.navbar.querySelector('.theme-icon');
    
    navLinks.forEach(link => {
      link.style.color = color;
    });
    if (logoText) logoText.style.color = color;
    if (themeIcon) themeIcon.style.color = color;
  }
}

// Initialize when DOM is ready
function initNavbarScrollAnimation() {
  new NavbarScrollAnimation();
}

// Auto-initialize
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initNavbarScrollAnimation);
} else {
  initNavbarScrollAnimation();
}

// Export for manual initialization
window.initNavbarScrollAnimation = initNavbarScrollAnimation;