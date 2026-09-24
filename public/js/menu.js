/* ================================================
   MOBILE MODAL FUNCTIONALITY
   ================================================ */

/**
 * Toggle mobile modal visibility
 */
function toggleMobileModal() {
  const mobileContainer = document.getElementById('mobile-container');
  const menuToggle = document.getElementById('mobile-menu-toggle');
  
  if (mobileContainer && menuToggle) {
    const isActive = mobileContainer.classList.contains('active');
    
    if (isActive) {
      // Hide modal
      mobileContainer.classList.remove('active');
      menuToggle.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    } else {
      // Show modal
      mobileContainer.classList.add('active');
      menuToggle.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
    }
  }
}

/**
 * Close mobile modal
 */
function closeMobileModal() {
  const mobileContainer = document.getElementById('mobile-container');
  const menuToggle = document.getElementById('mobile-menu-toggle');
  
  if (mobileContainer && menuToggle) {
    mobileContainer.classList.remove('active');
    menuToggle.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }
}

/**
 * Legacy functions for backward compatibility
 */
function toggleMobileMenuContent() {
  toggleMobileModal();
}

function closeMobileMenuContent() {
  closeMobileModal();
}

function toggleMobileMenu() {
  toggleMobileModal();
}

function closeMobileMenu() {
  closeMobileModal();
}

function toggleMenu() {
  toggleMobileModal();
}

function closeMenu() {
  closeMobileModal();
}

/**
 * Handle clicks outside mobile menu to close it
 */
function handleMobileMenuOutsideClick(event) {
  const mobileContainer = document.getElementById('mobile-container');
  const menuToggle = document.getElementById('mobile-menu-toggle');
  
  if (mobileContainer && menuToggle &&
      mobileContainer.classList.contains('active') &&
      !mobileContainer.contains(event.target) &&
      !menuToggle.contains(event.target)) {
    closeMobileModal();
  }
}