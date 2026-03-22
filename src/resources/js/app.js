import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Aplica tema imediatamente (evita flash)
(function(){
    const t = localStorage.getItem('fitpulse-theme');
    const preferLight = window.matchMedia('(prefers-color-scheme: light)').matches;
    const theme = t ?? (preferLight ? 'light' : 'dark');
    if (theme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
        document.documentElement.classList.remove('dark');
    } else {
        document.documentElement.removeAttribute('data-theme');
        document.documentElement.classList.add('dark');
    }
})();

// Toggle do botão
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btnTheme');
    if (!btn) return;
    const icon = btn.querySelector('i');

    function applyTheme(theme) {
        if (theme === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
            document.documentElement.classList.remove('dark');
            if (icon) icon.className = 'fa-solid fa-sun';
        } else {
            document.documentElement.removeAttribute('data-theme');
            document.documentElement.classList.add('dark');
            if (icon) icon.className = 'fa-solid fa-moon';
        }
        localStorage.setItem('fitpulse-theme', theme);
    }

    const saved = localStorage.getItem('fitpulse-theme');
    const preferLight = window.matchMedia('(prefers-color-scheme: light)').matches;
    applyTheme(saved ?? (preferLight ? 'light' : 'dark'));

    btn.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme');
        applyTheme(current === 'light' ? 'dark' : 'light');
    });
});