  // ─── Department Select Helper (exported for role.js and position.js) ──────────

  export function getFreshDepartmentSelect() {
      const departmentSelect = document.getElementById('department');
      if (!departmentSelect) return null;

      if (departmentSelect.dataset.init) return departmentSelect;

      const fresh = departmentSelect.cloneNode(true);
      fresh.dataset.init = 'true';
      departmentSelect.parentNode.replaceChild(fresh, departmentSelect);
      return fresh;
  }

  // ─── Main ─────────────────────────────────────────────────────────────────────

  document.addEventListener('DOMContentLoaded', function () {

    // ─── Page Fetching ───────────────────────────────────────────────────────────

    function fetchPage(page, push = true) {
      const container = document.querySelector('.container');
      if (!container) return;

      fetch(`page-loader.php?page=${encodeURIComponent(page)}`, { credentials: 'same-origin' })
        .then(function (response) {
          if (!response.ok) throw new Error('Network error');
          const rendered = response.headers.get('X-Rendered-Page') || page;
          return response.text().then(function (html) {
            return { html: html, rendered: rendered };
          });
        })
        .then(function (result) {
          container.innerHTML = result.html;
          updateActiveLink(result.rendered);

          if (push) {
            history.pushState({ page: result.rendered }, '', '?page=' + encodeURIComponent(result.rendered));
          }

          reinitPage(result.rendered);
        })
        .catch(function (err) {
          console.error('Page switch failed', err);
        });
    }

    // ─── Active Link ─────────────────────────────────────────────────────────────

    function updateActiveLink(page) {
      document.querySelectorAll('.menu-link, .active-menu-link').forEach(function (el) {
        el.className = 'menu-link';
      });
      var a = document.querySelector('[data-page="' + page + '"]');
      if (a) a.className = 'active-menu-link';
    }

    // ─── Reinitialize Page Scripts ────────────────────────────────────────────────

    function reinitPage(page) {
      initTabs();
      initForms();

      window.dispatchEvent(new CustomEvent('page:loaded', { detail: { page: page } }));
    }

    // ─── Tab Switcher ─────────────────────────────────────────────────────────────

    function initTabs() {
      const tabItems = document.querySelectorAll('.tab-item');
      const tabContents = document.querySelectorAll('.tab-content');

      if (!tabItems.length) return;

      tabItems.forEach(function (tab) {
        tab.addEventListener('click', function () {
          tabItems.forEach(function (t) { t.classList.remove('active'); });
          tabContents.forEach(function (c) { c.classList.remove('active'); });

          tab.classList.add('active');
          const target = document.getElementById(tab.getAttribute('data-tab'));
          if (target) target.classList.add('active');
        });
      });
    }

    // ─── Form Submissions ─────────────────────────────────────────────────────────

    function initForms() {
      const forms = document.querySelectorAll('form');

      forms.forEach(function (form) {
        const fresh = form.cloneNode(true);
        form.parentNode.replaceChild(fresh, form);

        fresh.addEventListener('submit', function (e) {
          e.preventDefault();
          const formData = new FormData(fresh);
          const action = fresh.getAttribute('action') || window.location.href;

          fetch(action, {
            method: fresh.getAttribute('method') || 'POST',
            body: formData,
            credentials: 'same-origin'
          })
            .then(function (response) {
              if (!response.ok) throw new Error('Form submission failed');
              return response.text();
            })
            .then(function (result) {
              console.log('Form submitted successfully', result);
              const current = new URL(location).searchParams.get('page') || 'dashboard-overview';
              fetchPage(current, false);
            })
            .catch(function (err) {
              console.error('Form error', err);
            });
        });
      });
    }

    // ─── Sidebar Click Intercept ──────────────────────────────────────────────────

    document.body.addEventListener('click', function (e) {
      var a = e.target.closest('a[data-page]');
      if (!a) return;
      e.preventDefault();
      fetchPage(a.getAttribute('data-page'));
    });

    // ─── Back / Forward ───────────────────────────────────────────────────────────

    window.addEventListener('popstate', function (e) {
      var page = (e.state && e.state.page) || new URL(location).searchParams.get('page') || 'dashboard-overview';
      fetchPage(page, false);
    });

    // ─── Initial Load ─────────────────────────────────────────────────────────────

    var initial = new URL(location).searchParams.get('page') || 'dashboard-overview';
    updateActiveLink(initial);
    initTabs();
    initForms();

  });