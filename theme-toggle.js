/*
 * theme-toggle.js — переключатель светлой/тёмной темы
 * Работает вместе с антифликер-скриптом в <head> (см. index.html).
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'theme';
    var btn = document.getElementById('themeToggle');
    if (!btn) return;

    var icon = btn.querySelector('.theme-toggle__icon');

    function apply(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        if (icon) icon.textContent = theme === 'light' ? '🌙' : '☀';
        btn.setAttribute('aria-pressed', theme === 'light' ? 'true' : 'false');
    }

    apply(document.documentElement.getAttribute('data-theme') || 'dark');

    btn.addEventListener('click', function () {
        var next = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
        try { localStorage.setItem(STORAGE_KEY, next); } catch (e) {}
        apply(next);
    });
})();
