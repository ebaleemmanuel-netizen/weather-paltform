// Theme (light/dark) and contrast (normal/high) controls.
// Applied instantly to <html> via data-bs-theme (Bootstrap 5.3 color modes)
// and a .contrast-high class, and persisted in localStorage so it survives
// page navigation and future visits. The actual attribute is also set as
// early as possible in header.php (before CSS/paint) to avoid a flash.
(function () {
  function setTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
    localStorage.setItem('agri_theme', theme);
    updateButtons();
  }

  function setContrast(contrast) {
    document.documentElement.classList.toggle('contrast-high', contrast === 'high');
    localStorage.setItem('agri_contrast', contrast);
    updateButtons();
  }

  function updateButtons() {
    const theme = localStorage.getItem('agri_theme') || 'light';
    const contrast = localStorage.getItem('agri_contrast') || 'normal';
    const lightBtn = document.getElementById('themeLightBtn');
    const darkBtn = document.getElementById('themeDarkBtn');
    const normalBtn = document.getElementById('contrastNormalBtn');
    const highBtn = document.getElementById('contrastHighBtn');
    if (!lightBtn) return;
    lightBtn.classList.toggle('active', theme === 'light');
    darkBtn.classList.toggle('active', theme === 'dark');
    normalBtn.classList.toggle('active', contrast === 'normal');
    highBtn.classList.toggle('active', contrast === 'high');
  }

  document.addEventListener('DOMContentLoaded', function () {
    updateButtons();
    document.getElementById('themeLightBtn')?.addEventListener('click', () => setTheme('light'));
    document.getElementById('themeDarkBtn')?.addEventListener('click', () => setTheme('dark'));
    document.getElementById('contrastNormalBtn')?.addEventListener('click', () => setContrast('normal'));
    document.getElementById('contrastHighBtn')?.addEventListener('click', () => setContrast('high'));
  });
})();
