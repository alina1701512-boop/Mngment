/*
 * preloader.js — экран загрузки с процентным счётчиком для главной страницы.
 * Подключается ТОЛЬКО в index.html, перед reveal.js.
 *
 * Пока считает "виртуальный" прогресс (0-100%) с небольшой случайностью
 * в скорости — так он не выглядит как ровный таймер. Реальный вес страницы
 * тут небольшой (никаких тяжёлых 3D-моделей мы не грузим), поэтому это
 * не имитация полезной работы, а просто ритуал появления, как на референсе.
 * Минимальное время показа — чтобы не мигал на быстром интернете.
 */
(function () {
    'use strict';

    var loader = document.getElementById('siteLoader');
    if (!loader) return;

    var percentEl = loader.querySelector('.js-loader-percent');
    var MIN_DURATION = 250; /* мс — короткий ритуал появления, не блокирует восприятие загрузки */
    var start = performance.now();
    var progress = 0;

    function tick(now) {
        var elapsed = now - start;
        var target = Math.min(100, Math.round((elapsed / MIN_DURATION) * 100));
        /* лёгкая "неровность", чтобы не читалось как линейный таймер */
        progress += Math.max(1, (target - progress) * 0.18);
        progress = Math.min(progress, target === 100 ? 100 : 98);

        if (percentEl) percentEl.textContent = Math.floor(progress);

        var pageReady = document.readyState === 'complete';
        if (progress >= 100 || (pageReady && elapsed >= MIN_DURATION)) {
            finish();
            return;
        }
        requestAnimationFrame(tick);
    }

    function finish() {
        if (percentEl) percentEl.textContent = 100;
        loader.classList.add('is-hidden');
        document.body.classList.remove('is-loading');
        loader.addEventListener('transitionend', function handler() {
            loader.removeEventListener('transitionend', handler);
            loader.remove();
        });
        /* подстраховка, если transitionend не сработает */
        setTimeout(function () { if (loader.parentNode) loader.remove(); }, 900);
    }

    document.body.classList.add('is-loading');
    requestAnimationFrame(tick);
})();
