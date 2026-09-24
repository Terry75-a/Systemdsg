
  // dialog-1

  var dialog = document.querySelector('dialog');
  var overlay = document.querySelector('.dialog-overlay');
  
  document.querySelector('#show').onclick = function() {
    dialog.showModal();
    overlay.classList.add('active');
  };
  
  document.querySelector('#close').onclick = function() {
    dialog.close();
    overlay.classList.remove('active');
  };
  
  overlay.addEventListener('click', function() {
    dialog.close();
    overlay.classList.remove('active');
  });
  
  // dialog-2
  
  var dialog2 = document.querySelector('#dialog-2');
  var overlay2 = document.querySelector('.dialog-2-overlay');
  
  document.querySelector('#show-dialog-2').onclick = function() {
    dialog2.showModal();
    overlay2.classList.add('active');
  };
  
  document.querySelector('.close-dialog-2').onclick = function() {
    dialog2.close();
    overlay2.classList.remove('active');
  };
  
  overlay2.addEventListener('click', function() {
    dialog2.close();
    overlay2.classList.remove('active');
  });
  
  // Map Modal Functionality
  const mapLinks = document.querySelectorAll('.map-link');
  const mapModal = document.getElementById('map-modal');
  const mapIframe = document.getElementById('map-iframe');
  const closeMapModal = document.querySelector('.close-map-modal');
  
  mapLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const mapUrl = this.href;
      mapIframe.src = mapUrl;
      mapModal.style.display = 'flex';
      
      // Close dialog-2 when opening map
      dialog2.close();
      overlay2.classList.remove('active');
    });
  });
  
  closeMapModal.addEventListener('click', function() {
    mapModal.style.display = 'none';
    mapIframe.src = '';
  });
  
  mapModal.addEventListener('click', function(e) {
    if (e.target === mapModal) {
      mapModal.style.display = 'none';
      mapIframe.src = '';
    }
  });
  
  // dialog-3
  var dialog3 = document.querySelector('#dialog-3');
  var overlay3 = document.querySelector('.dialog-3-overlay');
  
  document.querySelector('#show-dialog-3').onclick = function() {
    dialog3.showModal();
    overlay3.classList.add('active');
  };
  
  document.querySelector('.close-dialog-3').onclick = function() {
    dialog3.close();
    overlay3.classList.remove('active');
  };
  
  overlay3.addEventListener('click', function() {
    dialog3.close();
    overlay3.classList.remove('active');
  });
