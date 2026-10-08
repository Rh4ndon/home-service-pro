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
        '<p class="otp-spam" id="otpModalSpam">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M4 6l8 6 8-6"/></svg>' +
          '<span>No code yet? It may have landed in your spam or junk folder.</span>' +
        '</p>' +
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

// ==========================================
// GLOBAL INCOMING CALL WATCHER
// Works on every logged-in customer/repairman page, so the callee receives the
// ring even when they are not on the chat page. Chat pages carry their own modal
// markup and logic, so this skips any page that already has #incomingCallModal.
// ==========================================
(function() {
  if (!localStorage.getItem('is_logged_in')) return;
  var role = localStorage.getItem('role');
  if (role !== 'customer' && role !== 'repairman') return;
  var userId = localStorage.getItem('id');
  if (!userId) return;
  var path = window.location.pathname || '';
  if (/call\.html/.test(path)) return;
  if (/chat\.html/.test(path)) return;
  if (document.getElementById('incomingCallModal')) return;

  var modalShown = false;
  var callData = null;
  var overlay = null;

  function ensureModal() {
    if (overlay) return overlay;
    var ov = document.createElement('div');
    ov.className = 'modal-overlay';
    ov.id = 'incomingCallModal';
    ov.setAttribute('data-modal-sticky', '1');
    ov.innerHTML =
      '<div class="modal">' +
        '<div class="incoming-modal">' +
          '<div class="incoming-avatar" id="incomingCallerInitials">?</div>' +
          '<div class="incoming-title" id="incomingCallerName">Incoming Call...</div>' +
          '<div class="incoming-sub">is calling you...</div>' +
          '<div class="incoming-actions">' +
            '<div>' +
              '<button type="button" class="incoming-btn incoming-btn-decline" id="declineCallBtn" title="Decline">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>' +
              '</button>' +
              '<div class="incoming-label">Decline</div>' +
            '</div>' +
            '<div>' +
              '<button type="button" class="incoming-btn incoming-btn-accept" id="acceptCallBtn" title="Accept">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>' +
              '</button>' +
              '<div class="incoming-label">Accept</div>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>';
    document.body.appendChild(ov);
    document.getElementById('acceptCallBtn').onclick = acceptCall;
    document.getElementById('declineCallBtn').onclick = declineCall;
    overlay = ov;
    return ov;
  }

  function hideModal() {
    modalShown = false;
    callData = null;
    if (overlay) closeModal('incomingCallModal');
  }

  function showIncomingCall(call) {
    if (modalShown) return;
    modalShown = true;
    callData = call;
    ensureModal();
    var name = call.caller_name || 'Unknown';
    document.getElementById('incomingCallerName').textContent = name;
    var initials = name.split(' ').map(function(w){ return w[0]; }).join('').substring(0,2).toUpperCase();
    document.getElementById('incomingCallerInitials').textContent = initials;
    openModal('incomingCallModal');
  }

  function declineCall() {
    if (callData) {
      var fd = new FormData();
      fd.append('call_id', callData.call_id);
      fd.append('status', 'declined');
      fetch('../../controllers/call/update.php', { method: 'POST', body: fd }).catch(function(){});
    }
    hideModal();
  }

  function acceptCall() {
    if (!callData) return;
    var call = callData;
    var fd = new FormData();
    fd.append('call_id', call.call_id);
    fd.append('status', 'answered');
    fetch('../../controllers/call/update.php', { method: 'POST', body: fd }).catch(function(){});

    var tfd = new FormData();
    tfd.append('room_name', call.room_name);
    tfd.append('user_name', localStorage.getItem('name') || '');
    tfd.append('user_id', userId);
    tfd.append('role', role);
    tfd.append('call_id', call.call_id);

    fetch('../../controllers/call/token.php', { method: 'POST', body: tfd })
      .then(function(r) { return r.json(); })
      .then(function(d) {
        if (d.success) {
          sessionStorage.setItem('daily_call_token', d.token);
          sessionStorage.setItem('daily_room_url', d.room_url);
          hideModal();
          window.location.href = 'call.html?room=' + encodeURIComponent(call.room_name) +
            '&call_id=' + call.call_id + '&mode=callee&contact_name=' + encodeURIComponent(call.caller_name || '');
        } else {
          hideModal();
          showAlert(d.message || 'Could not join the call.', 'error');
        }
      })
      .catch(function() {
        hideModal();
        showAlert('Could not join the call.', 'error');
      });
  }

  function pollIncoming() {
    if (modalShown) return;
    fetch('../../controllers/call/incoming.php?user_id=' + userId + '&user_role=' + role)
      .then(function(r) { return r.json(); })
      .then(function(d) {
        if (d.success && d.has_incoming && d.call) showIncomingCall(d.call);
      })
      .catch(function(){});
  }

  setInterval(pollIncoming, 3000);
})();

// ==========================================
// CHAT LAYOUT VIEWPORT FITTING
// On iOS Safari 100vh is the "large" viewport height: it ignores the URL bar and
// the on-screen keyboard, so a fixed-height chat layout can extend below the
// visible area (bottom input / last messages become unreachable). The visual
// viewport reflects what is actually on screen, so keep the chat sized to it.
// Applies to any page that renders the .chat-layout container.
// ==========================================
(function() {
  var layout = document.querySelector('.chat-layout');
  if (!layout) return;

  function fitChatHeight() {
    var vv = window.visualViewport;
    if (!vv || !vv.height) return;
    var h = (vv.offsetTop + vv.height) - layout.getBoundingClientRect().top;
    if (h < 200) h = 200;
    if (Math.abs(layout.offsetHeight - h) > 1) {
      layout.style.height = Math.round(h) + 'px';
    }
  }

  window.addEventListener('resize', fitChatHeight);
  window.addEventListener('orientationchange', function() { setTimeout(fitChatHeight, 250); });
  if (window.visualViewport) {
    window.visualViewport.addEventListener('resize', fitChatHeight);
    window.visualViewport.addEventListener('scroll', fitChatHeight);
  }
  fitChatHeight();
})();

