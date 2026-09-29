function openModal(id) {
  var m = document.getElementById(id);
  if (m) m.classList.add('show');
}

function closeModal(id) {
  var m = document.getElementById(id);
  if (m) m.classList.remove('show');
}

document.addEventListener('click', function(e) {
  if (e.target.classList.contains('modal-overlay') && !e.target.hasAttribute('data-modal-sticky')) {
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

// ==========================================
// OTP VERIFICATION
// ==========================================

var OTP_STORAGE_KEY = 'hs_pending_otp';

function otpClearStored() {
  try {
    localStorage.removeItem(OTP_STORAGE_KEY);
  } catch (e) {}
}

function otpGetStored() {
  try {
    return localStorage.getItem(OTP_STORAGE_KEY);
  } catch (e) {
    return null;
  }
}

function buildOtpModal() {
  var overlay = document.getElementById('otpModal');
  if (overlay) return overlay;

  overlay = document.createElement('div');
  overlay.className = 'modal-overlay';
  overlay.id = 'otpModal';
  overlay.setAttribute('data-modal-sticky', 'true');
  overlay.innerHTML =
    '<div class="modal">' +
      '<div class="modal-header">' +
        '<h3 id="otpModalTitle">Verify your email</h3>' +
        '<button class="modal-close" id="otpModalClose" aria-label="Close">&times;</button>' +
      '</div>' +
      '<div class="modal-body">' +
        '<p class="otp-intro" id="otpModalIntro"></p>' +
        '<div class="otp-inputs" id="otpModalInputs"></div>' +
        '<div class="otp-error" id="otpModalError" role="alert"></div>' +
        '<p class="otp-hint" id="otpModalHint"></p>' +
      '</div>' +
      '<div class="modal-footer">' +
        '<button class="btn btn-secondary btn-cancel" id="otpModalCancel">Cancel</button>' +
        '<button class="btn btn-primary" id="otpModalVerify">Verify</button>' +
      '</div>' +
    '</div>';

  document.body.appendChild(overlay);
  return overlay;
}

/**
 * Start an OTP challenge.
 * opts: { otp, email, title, message, hint, verifyLabel, onSuccess(code), onCancel(), onFail(msg) }
 */
function startOtpChallenge(opts) {
  opts = opts || {};

  var code = opts.otp === undefined || opts.otp === null ? '' : String(opts.otp);
  if (!code) {
    if (opts.onFail) opts.onFail('We could not send a verification code. Please try again.');
    return;
  }

  try {
    localStorage.setItem(OTP_STORAGE_KEY, code);
  } catch (e) {
    if (opts.onFail) opts.onFail('Your browser blocked local storage, so the code cannot be verified.');
    return;
  }

  openOtpModal(opts);
}

function openOtpModal(opts) {
  opts = opts || {};
  var length = opts.length || 6;

  var overlay = buildOtpModal();
  var inputsWrap = document.getElementById('otpModalInputs');
  var errorEl = document.getElementById('otpModalError');
  var titleEl = document.getElementById('otpModalTitle');
  var introEl = document.getElementById('otpModalIntro');
  var hintEl = document.getElementById('otpModalHint');
  var verifyBtn = document.getElementById('otpModalVerify');

  titleEl.textContent = opts.title || 'Verify your email';
  introEl.innerHTML = (opts.message || 'We sent a verification code to') +
    (opts.email ? ' <strong></strong>' : '');
  if (opts.email) introEl.querySelector('strong').textContent = opts.email;
  hintEl.textContent = opts.hint || 'Enter the code exactly as it appears in the email.';
  verifyBtn.textContent = opts.verifyLabel || 'Verify';

  errorEl.textContent = '';
  inputsWrap.classList.remove('invalid');
  inputsWrap.innerHTML = '';
  for (var i = 0; i < length; i++) {
    var input = document.createElement('input');
    input.type = 'text';
    input.inputMode = 'numeric';
    input.setAttribute('pattern', '[0-9]*');
    input.setAttribute('maxlength', '1');
    input.setAttribute('aria-label', 'Digit ' + (i + 1) + ' of ' + length);
    input.autocomplete = i === 0 ? 'one-time-code' : 'off';
    inputsWrap.appendChild(input);
  }

  var inputs = inputsWrap.querySelectorAll('input');

  function clearError() {
    errorEl.textContent = '';
    inputsWrap.classList.remove('invalid');
  }

  function fail(message) {
    errorEl.textContent = message;
    inputsWrap.classList.add('invalid');
    inputsWrap.classList.remove('shake');
    void inputsWrap.offsetWidth;
    inputsWrap.classList.add('shake');
    inputs[0].focus();
    inputs[0].select();
  }

  function gather() {
    var out = '';
    for (var i = 0; i < inputs.length; i++) out += inputs[i].value;
    return out;
  }

  function fill(digits) {
    for (var i = 0; i < inputs.length; i++) {
      inputs[i].value = digits.charAt(i) || '';
    }
    clearError();
  }

  function teardown() {
    overlay.classList.remove('show');
    inputsWrap.innerHTML = '';
  }

  function attemptVerify() {
    var entered = gather();
    if (entered.length !== length) {
      fail('Please enter all ' + length + ' digits of the code.');
      return;
    }
    var expected = otpGetStored();
    if (!expected) {
      teardown();
      otpClearStored();
      if (opts.onFail) opts.onFail('This verification session expired. Please try again.');
      return;
    }
    if (expected !== entered) {
      fail('That code is not correct. Please check the email and try again.');
      return;
    }
    otpClearStored();
    teardown();
    if (opts.onSuccess) opts.onSuccess(entered);
  }

  function cancel() {
    teardown();
    otpClearStored();
    if (opts.onCancel) opts.onCancel();
  }

  inputsWrap.addEventListener('input', function(e) {
    var el = e.target;
    if (el.tagName !== 'INPUT') return;
    var index = Array.prototype.indexOf.call(inputs, el);

    // Pasting (or one-time-code autofill) can drop several digits into one box:
    // spread them out, keeping whatever was already typed before this box.
    if (el.value.length > 1) {
      var digits = el.value.replace(/\D/g, '');
      if (digits) {
        var combined = gather().slice(0, index) + digits;
        fill(combined);
        var next = Math.min(combined.length, inputs.length - 1);
        inputs[next].focus();
        if (combined.length >= length) attemptVerify();
        return;
      }
      el.value = '';
      return;
    }

    el.value = el.value.replace(/\D/g, '');
    clearError();

    if (el.value && index < inputs.length - 1) inputs[index + 1].focus();
    if (gather().length === length) attemptVerify();
  });

  inputsWrap.addEventListener('keydown', function(e) {
    var el = e.target;
    if (el.tagName !== 'INPUT') return;
    var index = Array.prototype.indexOf.call(inputs, el);

    if (e.key === 'Backspace' && !el.value && index > 0) {
      e.preventDefault();
      inputs[index - 1].value = '';
      inputs[index - 1].focus();
      clearError();
    } else if (e.key === 'ArrowLeft' && index > 0) {
      e.preventDefault();
      inputs[index - 1].focus();
    } else if (e.key === 'ArrowRight' && index < inputs.length - 1) {
      e.preventDefault();
      inputs[index + 1].focus();
    } else if (e.key === 'Enter') {
      e.preventDefault();
      attemptVerify();
    }
  });

  inputsWrap.addEventListener('paste', function(e) {
    e.preventDefault();
    var text = (e.clipboardData || window.clipboardData).getData('text') || '';
    var digits = text.replace(/\D/g, '');
    if (!digits) return;
    var index = Array.prototype.indexOf.call(inputs, e.target);
    var combined = gather().slice(0, index) + digits;
    fill(combined);
    var next = Math.min(combined.length, inputs.length - 1);
    inputs[next].focus();
    if (combined.length >= length) attemptVerify();
  });

  document.getElementById('otpModalClose').onclick = cancel;
  document.getElementById('otpModalCancel').onclick = cancel;
  verifyBtn.onclick = attemptVerify;

  overlay.classList.add('show');
  setTimeout(function() { inputs[0].focus(); }, 50);
}

// A pending code is only meaningful while the challenge modal is open, so any
// leftover from an abandoned attempt is dead on a fresh page load.
otpClearStored();

