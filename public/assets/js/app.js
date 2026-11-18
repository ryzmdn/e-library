document.addEventListener('DOMContentLoaded', function () {
  initTheme();
  initModals();
  initAlerts();
  initShortcuts();
});

function initTheme() {
  var savedTheme = localStorage.getItem('elibrary_theme') || 'light';
  applyTheme(savedTheme);

  var themeToggles = document.querySelectorAll('[data-theme-switcher]');
  themeToggles.forEach(function (button) {
    button.addEventListener('click', function () {
      var nextTheme = button.getAttribute('data-theme-switcher');
      applyTheme(nextTheme);
      localStorage.setItem('elibrary_theme', nextTheme);
    });
  });
}

function applyTheme(theme) {
  if (theme === 'light') {
    document.documentElement.removeAttribute('data-theme');
  } else {
    document.documentElement.setAttribute('data-theme', theme);
  }
}

function initModals() {
  var openTriggers = document.querySelectorAll('[data-modal-open]');
  openTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      var targetId = trigger.getAttribute('data-modal-open');
      var modal = document.getElementById(targetId);
      if (modal) {
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
      }
    });
  });

  var closeTriggers = document.querySelectorAll('[data-modal-close]');
  closeTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      var modal = trigger.closest('.modal-backdrop');
      if (modal) {
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
      }
    });
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      var openModals = document.querySelectorAll('.modal-backdrop.is-open');
      openModals.forEach(function (modal) {
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
      });
    }
  });
}

function initAlerts() {
  var alertCloses = document.querySelectorAll('[data-alert-close]');
  alertCloses.forEach(function (button) {
    button.addEventListener('click', function () {
      var alertBox = button.closest('.alert');
      if (alertBox) {
        alertBox.remove();
      }
    });
  });
}

function initShortcuts() {
  document.addEventListener('keydown', function (event) {
    if (event.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
      var searchInput = document.querySelector('input[type="text"][name="q"], input[type="search"]');
      if (searchInput) {
        event.preventDefault();
        searchInput.focus();
        searchInput.select();
      }
    }
  });
}
