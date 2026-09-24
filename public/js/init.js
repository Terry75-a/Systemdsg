/* ================================================
   INITIALIZATION AND EVENT LISTENERS
   ================================================ */

/**
 * Initialize all components when DOM is loaded
 */
function initializeApp() {
  // Initialize theme first
  initializeTheme();
  
  // Add event listeners
  setupEventListeners();
}

/**
 * Setup all event listeners
 */
function setupEventListeners() {
  // Mobile menu toggle
  const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener('click', toggleMobileModal);
  }
  
  // Close mobile modal when clicking outside
  document.addEventListener('click', handleMobileMenuOutsideClick);
  
  // Close mobile modal on escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeMobileModal();
    }
  });
}

/**
 * Initialize the application when DOM content is loaded
 */
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeApp);
} else {
  // DOM is already loaded
  initializeApp();
}