/*
 * reveal.js — декларативный движок появления элементов при скролле.
 * Никаких сборщиков и внешних библиотек: вешаете атрибуты в HTML,
 * скрипт сам добавляет класс "is-visible" в нужный момент через IntersectionObserver.
 *
 * Подключается ОДИН РАЗ на страницу (после script.js), работает на любых страницах сайта.
 *
 * ---------------------------------------------------------------------------
 * СИНТАКСИС АТРИБУТОВ
 * ---------------------------------------------------------------------------
 * data-reveal="fade-up"   — элемент всплывает снизу с затуханием (по умолчанию)
 * data-reveal="fade"      — просто проявляется, без сдвига
 * data-reveal="split"     — текст внутри разбивается на слова и "выезжает"
 *                            построчно из-под маски (для заголовков)
 *
 * data-reveal-delay="300"     — задержка перед стартом, мс
 * data-reveal-stagger="80"    — если на элементе есть дочерние [data-reveal-child],
 *                                каждый следующий появляется на N мс позже предыдущего
 * data-reveal-trigger="load"  — анимировать сразу при загрузке страницы,
 *                                а не при попадании в область видимости (для hero)
 * data-reveal-once="false"    — по умолчанию анимация проигрывается один раз;
 *                                поставьте "false", чтобы повторялась при каждом
 *                                входе/выходе из вьюпорта
 */
(function () {
    'use strict';

    var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Разбивка заголовков на слова для "выезжающего" текста ---------- */
    function splitIntoWords(el) {
        if (el.dataset.revealSplitDone) return;
        var words = el.textContent.trim().split(/\s+/);
        el.textContent = '';
        words.forEach(function (word, i) {
            var mask = document.createElement('span');
            mask.className = 'reveal-word';
            var inner = document.createElement('span');
            inner.className = 'reveal-word__inner';
            inner.textContent = word;
            mask.appendChild(inner);
            el.appendChild(mask);
            if (i < words.length - 1) el.appendChild(document.createTextNode(' '));
        });
        el.dataset.revealSplitDone = 'true';
    }

    /* ---------- Группы со stagger-эффектом ---------- */
    /* Контейнер с data-reveal-stagger необязательно сам анимируется —
       он просто назначает нарастающую задержку своим прямым детям
       с атрибутом data-reveal-child. */
    var staggerGroups = Array.prototype.slice.call(document.querySelectorAll('[data-reveal-stagger]'));
    staggerGroups.forEach(function (group) {
        var stagger = parseInt(group.dataset.revealStagger || '80', 10);
        var children = group.querySelectorAll('[data-reveal-child]');
        children.forEach(function (child, i) {
            child.classList.add('reveal', 'reveal--' + (child.dataset.revealChild || 'fade-up'));
            child.dataset.reveal = child.dataset.reveal || child.dataset.revealChild || 'fade-up';
            child.style.transitionDelay = (i * stagger) + 'ms';
        });
    });

    /* ---------- Подготовка обычных элементов (включая только что размеченные stagger-дети) ---------- */
    var items = Array.prototype.slice.call(document.querySelectorAll('[data-reveal]'));

    items.forEach(function (el) {
        var type = el.dataset.reveal || 'fade-up';
        el.classList.add('reveal', 'reveal--' + type);

        if (type === 'split') {
            splitIntoWords(el);
            var words = el.querySelectorAll('.reveal-word__inner');
            words.forEach(function (w, i) {
                w.style.transitionDelay = (i * 45) + 'ms';
            });
        }

        var delay = parseInt(el.dataset.revealDelay || '0', 10);
        if (delay && !el.hasAttribute('data-reveal-child')) el.style.transitionDelay = delay + 'ms';
    });

    function activate(el) {
        el.classList.add('is-visible');
    }

    if (prefersReduced) {
        /* без анимации — сразу показываем всё как есть */
        items.forEach(activate);
        return;
    }

    /* ---------- Элементы, которые должны появиться сразу при загрузке ---------- */
    var loadTriggered = items.filter(function (el) { return el.dataset.revealTrigger === 'load'; });
    var scrollTriggered = items.filter(function (el) { return el.dataset.revealTrigger !== 'load'; });

    window.addEventListener('DOMContentLoaded', function () {
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                loadTriggered.forEach(activate);
            });
        });
    });

    /* ---------- Остальное — по достижению области видимости ---------- */
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            var el = entry.target;
            var once = el.dataset.revealOnce !== 'false';
            if (entry.isIntersecting) {
                activate(el);
                if (once) observer.unobserve(el);
            } else if (!once) {
                el.classList.remove('is-visible');
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });

    scrollTriggered.forEach(function (el) { observer.observe(el); });
})();
