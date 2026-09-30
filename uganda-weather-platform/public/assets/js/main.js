// Shared front-end behaviors
document.addEventListener('DOMContentLoaded', function () {
  // Auto-dismiss alerts after 6s
  document.querySelectorAll('.alert').forEach(function (el) {
    setTimeout(function () { el.style.transition = 'opacity .5s'; el.style.opacity = 0; }, 6000);
  });
});
