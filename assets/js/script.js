document.addEventListener('DOMContentLoaded', function () {
  // Mobile sidebar toggle
  var toggle = document.getElementById('sidebarToggle');
  var sidebar = document.getElementById('emsSidebar');
  if (toggle && sidebar) {
    toggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });
  }

  // Auto-open edit modal if the URL carries ?edit=ID and the modal exists
  var editModalEl = document.getElementById('editModal');
  if (editModalEl && editModalEl.dataset.autoOpen === '1' && window.bootstrap) {
    new bootstrap.Modal(editModalEl).show();
  }

  // Live client-side filter for simple search inputs marked data-live-filter
  document.querySelectorAll('[data-live-filter]').forEach(function (input) {
    input.addEventListener('keyup', function () {
      var targetSelector = input.getAttribute('data-live-filter');
      var rows = document.querySelectorAll(targetSelector);
      var term = input.value.toLowerCase();
      rows.forEach(function (row) {
        row.style.display = row.textContent.toLowerCase().indexOf(term) > -1 ? '' : 'none';
      });
    });
  });

  // Photo upload preview
  document.querySelectorAll('[data-photo-input]').forEach(function (input) {
    input.addEventListener('change', function () {
      var previewSel = input.getAttribute('data-photo-input');
      var preview = document.querySelector(previewSel);
      if (preview && input.files && input.files[0]) {
        preview.src = URL.createObjectURL(input.files[0]);
      }
    });
  });

  // Auto-dismiss alerts after a few seconds
  document.querySelectorAll('.alert').forEach(function (alertEl) {
    setTimeout(function () {
      if (window.bootstrap) {
        var instance = bootstrap.Alert.getOrCreateInstance(alertEl);
        instance.close();
      }
    }, 5000);
  });
});

function confirmDelete(message) {
  return confirm(message || 'Are you sure you want to delete this record? This cannot be undone.');
}

function confirmLogout() {
  return confirm('Are you sure you want to log out?');
}
