function openModal(id) {
  var m = document.getElementById(id);
  if (m) m.classList.add('show');
}

function closeModal(id) {
  var m = document.getElementById(id);
  if (m) m.classList.remove('show');
}

document.addEventListener('click', function(e) {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.remove('show');
  }
});

function confirmAction(modalId) {
  openModal(modalId);
}

function doAction(modalId, message) {
  closeModal(modalId);
  if (message) alert(message);
}

function toggleAvailability(el) {
  var label = el.closest('.avail-row').querySelector('.avail-label');
  if (el.checked) {
    label.textContent = 'Available';
    label.style.color = '#22c55e';
  } else {
    label.textContent = 'Unavailable';
    label.style.color = '#ef4444';
  }
}

function filterTable(inputId, tableId) {
  var query = document.getElementById(inputId).value.toLowerCase();
  var rows = document.getElementById(tableId).querySelectorAll('tbody tr');
  rows.forEach(function(row) {
    var text = row.textContent.toLowerCase();
    row.style.display = text.includes(query) ? '' : 'none';
  });
}
