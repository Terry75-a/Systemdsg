/* ================================================
   THEME FUNCTIONALITY
   ================================================ */

/**
 * Toggle between dark and light theme
 */
function toggleTheme() {
  const html = document.documentElement;
  const themeIcons = document.querySelectorAll('.theme-icon');
  const isDark = html.getAttribute('data-theme') === 'dark';
  
  if (isDark) {
    // Cambiar a modo claro
    html.removeAttribute('data-theme');
    localStorage.setItem('theme', 'light');
    themeIcons.forEach(icon => {
      icon.textContent = 'wb_sunny';
    });
  } else {
    // Cambiar a modo oscuro
    html.setAttribute('data-theme', 'dark');
    localStorage.setItem('theme', 'dark');
    themeIcons.forEach(icon => {
      icon.textContent = 'nightlight_round';
    });
  }
  
  // Update navbar text color based on background
  updateNavbarTextColor();
}

/**
 * Initialize theme on page load
 */
function initializeTheme() {
  const html = document.documentElement;
  const themeIcons = document.querySelectorAll('.theme-icon');
  const savedTheme = localStorage.getItem('theme');
  
  // If no saved theme, default to dark theme
  if (!savedTheme) {
    html.setAttribute('data-theme', 'dark');
    localStorage.setItem('theme', 'dark');
    themeIcons.forEach(icon => {
      icon.textContent = 'nightlight_round';
    });
    updateNavbarTextColor();
    // Mark theme as loaded
    html.classList.add('theme-loaded');
    return;
  }
  
  // Apply saved theme
  if (savedTheme === 'dark') {
    html.setAttribute('data-theme', 'dark');
    themeIcons.forEach(icon => {
      icon.textContent = 'nightlight_round';
    });
  } else {
    html.removeAttribute('data-theme');
    themeIcons.forEach(icon => {
      icon.textContent = 'wb_sunny';
    });
  }
  
  // Mark theme as loaded
  html.classList.add('theme-loaded');
  
  updateNavbarTextColor();
}

/**
 * Update navbar text color based on background
 */
function updateNavbarTextColor() {
  const navbar = document.querySelector('.navbar');
  const navLinks = document.querySelectorAll('.nav-link');
  const logoText = document.querySelector('.logo-text');
  const themeIcon = document.querySelector('.theme-icon');
  
  // Check if dark theme is active
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  
  // Set text colors based on theme
  if (isDark) {
    // Dark theme - use light text
    navLinks.forEach(link => {
      link.style.color = 'white';
    });
    if (logoText) logoText.style.color = 'white';
    if (themeIcon) themeIcon.style.color = 'white';
  } else {
    // Light theme - use dark text
    navLinks.forEach(link => {
      link.style.color = 'black';
    });
    if (logoText) logoText.style.color = 'black';
    if (themeIcon) themeIcon.style.color = 'black';
  }
}

// Initialize theme when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  initializeTheme();
  // Add scroll event listener to adjust navbar text color based on background
  window.addEventListener('scroll', updateNavbarOnScroll);
});

/**
 * Update navbar text color based on scroll position and background
 */
function updateNavbarOnScroll() {
  const navbar = document.querySelector('.navbar');
  const navLinks = document.querySelectorAll('.nav-link');
  const logoText = document.querySelector('.logo-text');
  const themeIcon = document.querySelector('.theme-icon');
  
  // Get navbar position and background
  const rect = navbar.getBoundingClientRect();
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  
  // For now, we'll just use the theme-based coloring
  // In a more advanced implementation, we could check the actual background color
  // at the navbar position and adjust text color accordingly
  if (isDark) {
    navLinks.forEach(link => {
      link.style.color = 'white';
    });
    if (logoText) logoText.style.color = 'white';
    if (themeIcon) themeIcon.style.color = 'white';
  } else {
    navLinks.forEach(link => {
      link.style.color = 'black';
    });
    if (logoText) logoText.style.color = 'black';
    if (themeIcon) themeIcon.style.color = 'black';
  }
}

   