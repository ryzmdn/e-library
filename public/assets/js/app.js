document.addEventListener('DOMContentLoaded', function () {
  localStorage.removeItem('elibrary_theme');
  document.documentElement.removeAttribute('data-theme');
  initModals();
  initAlerts();
  initShortcuts();
  initQuickSearch();
  initDropdowns();
});

function initModals() {
  var openTriggers = document.querySelectorAll('[data-modal-open]');
  openTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      var targetId = trigger.getAttribute('data-modal-open');
      openModalById(targetId);
    });
  });

  var closeTriggers = document.querySelectorAll('[data-modal-close]');
  closeTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      var modal = trigger.closest('.modal-backdrop');
      if (modal) {
        closeModal(modal);
      }
    });
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      var openModals = document.querySelectorAll('.modal-backdrop.is-open');
      openModals.forEach(function (modal) {
        closeModal(modal);
      });
    }
  });

  var backdrops = document.querySelectorAll('.modal-backdrop');
  backdrops.forEach(function (backdrop) {
    backdrop.addEventListener('click', function (event) {
      if (event.target === backdrop) {
        closeModal(backdrop);
      }
    });
  });
}

function openModalById(id) {
  var modal = document.getElementById(id);
  if (modal) {
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    var autofocusInput = modal.querySelector('input');
    if (autofocusInput) {
      setTimeout(function () {
        autofocusInput.focus();
        autofocusInput.select();
      }, 100);
    }
  }
}

function closeModal(modal) {
  modal.classList.remove('is-open');
  document.body.style.overflow = '';
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
    var isCmdK = (event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k';
    if (isCmdK) {
      event.preventDefault();
      var quickModal = document.getElementById('quick-search-modal');
      if (quickModal && !quickModal.classList.contains('is-open')) {
        openModalById('quick-search-modal');
      } else if (quickModal) {
        closeModal(quickModal);
      }
      return;
    }

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

function initQuickSearch() {
  var input = document.getElementById('quick-search-input');
  var resultsContainer = document.getElementById('quick-search-results');
  if (!input || !resultsContainer) {
    return;
  }

  var debounceTimer = null;
  var defaultHtml = resultsContainer.innerHTML;

  input.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    var query = input.value.trim();

    if (query.length < 2) {
      resultsContainer.innerHTML = defaultHtml;
      return;
    }

    debounceTimer = setTimeout(function () {
      fetch('/api/catalog/suggest?q=' + encodeURIComponent(query))
        .then(function (res) {
          if (!res.ok) {
            throw new Error('Network error');
          }
          return res.json();
        })
        .then(function (data) {
          var items = (data && data.data) ? data.data : (Array.isArray(data) ? data : []);
          if (items.length === 0) {
            resultsContainer.innerHTML = '<div style="padding:1.5rem; text-align:center; font-size:0.8125rem; color:var(--muted-foreground);">Tidak ada judul buku yang cocok dengan kata kunci "' + escapeHtml(query) + '".</div>';
            return;
          }

          var html = '<div style="padding:0.75rem 0.75rem 0.5rem 0.75rem; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; color:var(--muted-foreground);">Hasil Pencarian</div>';
          html += '<div style="display:flex; flex-direction:column; gap:0.25rem;">';
          items.forEach(function (book) {
            var slug = book.slug || book.id;
            html += '<a href="/catalog/' + encodeURIComponent(slug) + '" class="dropdown-item" style="border-radius:var(--radius-md); padding:0.55rem 0.75rem;">';
            html += '<div style="flex:1;">';
            html += '<div style="color:var(--primary); font-weight:600; font-size:0.875rem;">' + escapeHtml(book.title || '') + '</div>';
            html += '<div style="color:var(--muted-foreground); font-size:0.75rem;">' + escapeHtml(book.authors_text || book.author || '-') + '</div>';
            html += '</div>';
            html += '<span class="badge badge-subtle" style="font-size:0.6875rem;">' + escapeHtml(book.call_number_base || 'Buku') + '</span>';
            html += '</a>';
          });
          html += '</div>';
          resultsContainer.innerHTML = html;
        })
        .catch(function () {
          resultsContainer.innerHTML = defaultHtml;
        });
    }, 250);
  });
}

function initDropdowns() {
  var dropdownTriggers = document.querySelectorAll('.dropdown-trigger');
  dropdownTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function (e) {
      e.stopPropagation();
      var parent = trigger.closest('.dropdown');
      if (parent) {
        parent.classList.toggle('is-active');
      }
    });
  });

  document.addEventListener('click', function () {
    var activeDropdowns = document.querySelectorAll('.dropdown.is-active');
    activeDropdowns.forEach(function (dd) {
      dd.classList.remove('is-active');
    });
  });
}

function escapeHtml(text) {
  var div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}
