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

function isSuccess(data) {
  return !!(data && (data.status === 'success' || data.success === true));
}

function showAlert(message, type) {
  type = type || 'info';
  var container = document.getElementById('hsToastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'hsToastContainer';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  var icons = {
    success: '<svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
    error: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
    warning: '<svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
    info: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
  };
  var toast = document.createElement('div');
  toast.className = 'toast toast-' + type;
  var icon = document.createElement('div');
  icon.className = 'toast-icon';
  icon.innerHTML = icons[type] || icons.info;
  var msg = document.createElement('div');
  msg.className = 'toast-msg';
  msg.textContent = message || '';
  var close = document.createElement('button');
  close.className = 'toast-close';
  close.setAttribute('aria-label', 'Dismiss');
  close.innerHTML = '&times;';
  toast.appendChild(icon);
  toast.appendChild(msg);
  toast.appendChild(close);
  container.appendChild(toast);
  var dismiss = function() {
    toast.classList.add('toast-hide');
    setTimeout(function() {
      if (toast.parentNode) toast.parentNode.removeChild(toast);
    }, 250);
  };
  close.addEventListener('click', dismiss);
  setTimeout(dismiss, 4000);
}

function doAction(modalId, message) {
  closeModal(modalId);
  if (message) showAlert(message, 'success');
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

function togglePassword(btn) {
  var wrapper = btn.closest('.password-wrapper');
  var input = wrapper ? wrapper.querySelector('input') : null;
  if (!input) return;
  
  if (input.type === 'password') {
    input.type = 'text';
    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19M1 1l22 22"/></svg>';
    btn.setAttribute('aria-label', 'Hide password');
  } else {
    input.type = 'password';
    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    btn.setAttribute('aria-label', 'Show password');
  }
}
