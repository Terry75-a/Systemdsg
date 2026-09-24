(function() {
        // Get saved theme from localStorage or default to 'dark'
        const savedTheme = localStorage.getItem('theme') || 'dark';
        
        // Apply the theme immediately to prevent flash
        if (savedTheme === 'dark') {
          document.documentElement.setAttribute('data-theme', 'dark');
        } else {
          document.documentElement.removeAttribute('data-theme');
        }
        
        // Add a class to html to indicate theme has been set
        document.documentElement.classList.add('theme-loaded');
      })();