/*
 * Repairman profile modal (customer side).
 * Shows the trust signals a customer needs before booking or while chatting:
 * rating, completed repairs, skills, education, certificates and recent reviews.
 *
 * Usage: openRepairmanProfile({ profileId: 7 })  or  openRepairmanProfile({ userId: 8 })
 */
(function () {
  var MODAL_ID = 'repairmanProfileModal';

  function escHtml(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str == null ? '' : String(str)));
    return d.innerHTML;
  }

  function stars(rating) {
    var r = Math.round(parseFloat(rating) || 0);
    var s = '';
    for (var i = 1; i <= 5; i++) s += i <= r ? '&#9733;' : '&#9734;';
    return s;
  }

  function fmtDate(v) {
    if (!v) return '';
    var d = new Date(String(v).replace(' ', 'T'));
    return isNaN(d.getTime()) ? '' : d.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
  }

  var SKILL_LABELS = {
    'refrigerator': 'Refrigerator Repair',
    'air-conditioner': 'Air Conditioner Repair',
    'washing-machine': 'Washing Machine Repair',
    'dishwasher': 'Dishwasher Repair',
    'oven': 'Oven / Stove Repair',
    'microwave': 'Microwave Repair',
    'television': 'Television Repair',
    'water-heater': 'Water Heater Repair',
    'electric-fan': 'Electric Fan Repair',
    'dryer': 'Dryer Repair',
    'other': 'Other'
  };

  function ensureModal() {
    var el = document.getElementById(MODAL_ID);
    if (el) return el;

    var style = document.createElement('style');
    style.textContent =
      '#' + MODAL_ID + ' .modal{width:min(640px,calc(100% - 32px));}' +
      '#' + MODAL_ID + ' .modal-body{max-height:70vh;overflow-y:auto;}' +
      '.rp-head{display:flex;gap:16px;align-items:center;margin-bottom:16px;}' +
      '.rp-avatar{width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#1b8de8,#0d71c7);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.3rem;flex-shrink:0;}' +
      '.rp-name{font-weight:800;font-size:1.15rem;}' +
      '.rp-stars{color:#f59e0b;font-size:1rem;}' +
      '.rp-muted{color:var(--muted);font-size:0.85rem;}' +
      '.rp-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:10px;margin-bottom:16px;}' +
      '.rp-stat{background:var(--panel);border:1px solid var(--line);border-radius:10px;padding:10px 12px;}' +
      '.rp-stat b{display:block;font-size:1.1rem;}' +
      '.rp-section{margin-bottom:16px;}' +
      '.rp-section h4{margin:0 0 8px;font-size:0.9rem;text-transform:uppercase;letter-spacing:.04em;color:var(--muted);}' +
      '.rp-chips{display:flex;gap:6px;flex-wrap:wrap;}' +
      '.rp-chip{background:#eff6ff;color:#1e40af;border-radius:999px;padding:4px 10px;font-size:0.8rem;font-weight:600;}' +
      '.rp-cert{display:flex;justify-content:space-between;gap:10px;padding:8px 0;border-bottom:1px solid var(--line);font-size:0.88rem;}' +
      '.rp-cert:last-child{border-bottom:0;}' +
      '.rp-cert a{color:var(--primary);font-weight:600;white-space:nowrap;}' +
      '.rp-review{padding:10px 0;border-bottom:1px solid var(--line);}' +
      '.rp-review:last-child{border-bottom:0;}' +
      '.rp-review-top{display:flex;justify-content:space-between;gap:10px;font-size:0.85rem;}' +
      '.rp-review p{margin:4px 0 0;font-size:0.88rem;}' +
      '.rp-verified{display:inline-flex;align-items:center;gap:4px;color:#15803d;font-size:0.8rem;font-weight:600;}';
    document.head.appendChild(style);

    el = document.createElement('div');
    el.className = 'modal-overlay';
    el.id = MODAL_ID;
    el.innerHTML =
      '<div class="modal">' +
        '<div class="modal-header">' +
          '<h3>Repairman Profile</h3>' +
          '<button class="modal-close" type="button" aria-label="Close">&times;</button>' +
        '</div>' +
        '<div class="modal-body" id="' + MODAL_ID + 'Body"></div>' +
        '<div class="modal-footer">' +
          '<button class="btn btn-secondary btn-cancel" type="button">Close</button>' +
        '</div>' +
      '</div>';
    document.body.appendChild(el);
    el.querySelector('.modal-close').onclick = function () { closeModal(MODAL_ID); };
    el.querySelector('.btn-cancel').onclick = function () { closeModal(MODAL_ID); };
    return el;
  }

  function render(data) {
    var r = data.repairman;
    var reviews = data.reviews || [];
    var initials = String(r.name || 'R').split(' ').map(function (w) { return w.charAt(0); }).join('').substring(0, 2).toUpperCase();

    var html =
      '<div class="rp-head">' +
        '<div class="rp-avatar">' + escHtml(initials) + '</div>' +
        '<div>' +
          '<div class="rp-name">' + escHtml(r.name) + '</div>' +
          '<div><span class="rp-stars">' + stars(r.rating) + '</span> <span class="rp-muted">' + Number(r.rating || 0).toFixed(1) + ' (' + r.review_count + ' review' + (r.review_count === 1 ? '' : 's') + ')</span></div>' +
          '<div class="rp-verified"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Verified by HomeServicePro admin</div>' +
        '</div>' +
      '</div>' +
      '<div class="rp-stats">' +
        '<div class="rp-stat"><b>' + r.completed_repairs + '</b><span class="rp-muted">Completed repairs</span></div>' +
        '<div class="rp-stat"><b>' + r.review_count + '</b><span class="rp-muted">Customer reviews</span></div>' +
        '<div class="rp-stat"><b>' + (r.certifications ? r.certifications.length : 0) + '</b><span class="rp-muted">Certificates</span></div>' +
        (r.member_since ? '<div class="rp-stat"><b>' + escHtml(fmtDate(r.member_since)) + '</b><span class="rp-muted">Member since</span></div>' : '') +
      '</div>';

    html += '<div class="rp-section"><h4>Skills</h4>';
    if (r.skills && r.skills.length) {
      html += '<div class="rp-chips">' + r.skills.map(function (s) { return '<span class="rp-chip">' + escHtml(SKILL_LABELS[s] || s) + '</span>'; }).join('') + '</div>';
    } else {
      html += '<div class="rp-muted">No skills listed.</div>';
    }
    html += '</div>';

    html += '<div class="rp-section"><h4>Education & training</h4>' +
      (r.education ? '<div style="font-size:0.9rem;white-space:pre-line;">' + escHtml(r.education) + '</div>' : '<div class="rp-muted">Not provided.</div>') +
      '</div>';

    html += '<div class="rp-section"><h4>Certificates</h4>';
    if (r.certifications && r.certifications.length) {
      html += r.certifications.map(function (c) {
        return '<div class="rp-cert"><span>' + escHtml(c.name) + '</span>' +
          (c.file ? '<a href="../../' + escHtml(c.file) + '" target="_blank" rel="noopener">View file</a>' : '<span class="rp-muted">No file</span>') +
          '</div>';
      }).join('');
    } else {
      html += '<div class="rp-muted">No certificates uploaded.</div>';
    }
    html += '</div>';

    html += '<div class="rp-section"><h4>Recent reviews</h4>';
    if (reviews.length) {
      html += reviews.map(function (rv) {
        return '<div class="rp-review">' +
          '<div class="rp-review-top"><span><span class="rp-stars">' + stars(rv.rating) + '</span> <strong>' + escHtml(rv.customer_name) + '</strong></span>' +
          '<span class="rp-muted">' + escHtml(fmtDate(rv.created_at)) + '</span></div>' +
          (rv.comments ? '<p>' + escHtml(rv.comments) + '</p>' : '') +
          '</div>';
      }).join('');
    } else {
      html += '<div class="rp-muted">No reviews yet.</div>';
    }
    html += '</div>';

    if (r.facebook_page) {
      var fb = /^https?:\/\//i.test(r.facebook_page) ? r.facebook_page : 'https://' + r.facebook_page;
      html += '<div class="rp-section"><h4>Facebook page</h4><a href="' + escHtml(fb) + '" target="_blank" rel="noopener" style="color:var(--primary);font-weight:600;font-size:0.9rem;word-break:break-all;">' + escHtml(r.facebook_page) + '</a></div>';
    }

    return html;
  }

  window.openRepairmanProfile = function (opts) {
    opts = opts || {};
    var myId = localStorage.getItem('id');
    var qs = 'user_id=' + encodeURIComponent(myId);
    if (opts.profileId) qs += '&profile_id=' + encodeURIComponent(opts.profileId);
    else if (opts.userId) qs += '&repairman_user_id=' + encodeURIComponent(opts.userId);
    else return;

    ensureModal();
    var body = document.getElementById(MODAL_ID + 'Body');
    body.innerHTML = '<div style="text-align:center;padding:30px;color:var(--muted);">Loading profile...</div>';
    openModal(MODAL_ID);

    fetch('../../controllers/customer/repairman-profile.php?' + qs)
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (!d.success) {
          body.innerHTML = '<div style="text-align:center;padding:30px;color:var(--muted);">' + escHtml(d.message || 'Could not load this profile.') + '</div>';
          return;
        }
        body.innerHTML = render(d);
      })
      .catch(function () {
        body.innerHTML = '<div style="text-align:center;padding:30px;color:var(--muted);">Network error. Please try again.</div>';
      });
  };
})();
