/*
 * Repairman subscription guard.
 *
 * Loaded on every repairman page.
 *  - Trial or paid subscriber: page works normally.
 *  - Locked (trial ended, subscription expired, or revoked by admin): the
 *    repairman is sent to subscription.html, and that page is the only one
 *    they can use. The menu shows only the Subscription link.
 *
 * The server enforces the same rule (blockIfRepairmanLocked) for the APIs,
 * so this script is only the UI side.
 */
(function () {
  var root = document.documentElement;

  var style = document.createElement('style');
  style.textContent =
    'html.hs-sub-pending .main-content{visibility:hidden;}' +
    'html.hs-locked .sidebar-link:not([data-subscription-link]){display:none !important;}' +
    'html.hs-locked .sidebar-section-label{display:none !important;}';
  document.head.appendChild(style);

  function reveal() {
    root.classList.remove('hs-sub-pending');
  }

  if (!localStorage.getItem('is_logged_in') || localStorage.getItem('role') !== 'repairman') {
    reveal();
    return;
  }
  var userId = localStorage.getItem('id');
  if (!userId) {
    reveal();
    return;
  }

  var isSubscriptionPage = /subscription\.html$/i.test(window.location.pathname);
  root.classList.add('hs-sub-pending');

  fetch('../../controllers/repairman/subscription.php?user_id=' + encodeURIComponent(userId))
    .then(function (r) { return r.json(); })
    .then(function (d) {
      if (!d || !d.success || !d.subscription) {
        reveal();
        return;
      }
      window.hsRepairmanSubscription = d.subscription;
      if (d.subscription.locked) {
        if (!isSubscriptionPage) {
          window.location.replace('subscription.html');
          return;
        }
        root.classList.add('hs-locked');
      }
      reveal();
    })
    .catch(reveal);
})();
