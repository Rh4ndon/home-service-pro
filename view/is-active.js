document.addEventListener('DOMContentLoaded', function() {
 fetch('../../controllers/is-active.php?user_id=' + localStorage.getItem('id'))
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.is_active) {
        showAlert('You are deactivated', 'error');
        setTimeout(function() {
            localStorage.clear();
            window.location.href = 'login.html';
        }, 3000);
      }
        
     
    })
    .catch(function(err) { console.error('Is active error:', err); });
});