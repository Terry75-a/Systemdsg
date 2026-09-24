// Add to js/components/contact.js
document.addEventListener('DOMContentLoaded', function() {
  // Dropdown functionality
  const subjectDropdown = document.querySelector('.subject-dropdown');
  const dropdownOptions = document.querySelector('.dropdown-options');
  const input = document.getElementById('Asuntos');

  if (subjectDropdown && dropdownOptions && input) {
    subjectDropdown.addEventListener('click', function(e) {
      // Prevent closing when clicking on options
      if (e.target.classList.contains('option')) return;
      
      this.classList.toggle('active');
      dropdownOptions.style.display = dropdownOptions.style.display === 'block' ? 'none' : 'block';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (subjectDropdown && !subjectDropdown.contains(e.target)) {
        subjectDropdown.classList.remove('active');
        if (dropdownOptions) {
          dropdownOptions.style.display = 'none';
        }
      }
    });

    // Select option
    const options = document.querySelectorAll('.option');
    options.forEach(option => {
      option.addEventListener('click', function() {
        input.value = this.getAttribute('data-value');
        subjectDropdown.classList.remove('active');
        if (dropdownOptions) {
          dropdownOptions.style.display = 'none';
        }
      });
    });
  }
});