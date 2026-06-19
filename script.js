document.addEventListener('DOMContentLoaded', function () {

    // ============================================
    // МОДУЛЬ 1: АНИМАЦИЯ СХЕМЫ НА ГЛАВНОЙ
    // ============================================
    const flowChart = document.getElementById('flowChart');
    if (flowChart) {
        const connectors = flowChart.querySelectorAll('.connector');
        const chartObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    connectors.forEach((conn, index) => {
                        setTimeout(() => {
                            conn.classList.add('active');
                        }, index * 400);
                    });
                    chartObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });
        chartObserver.observe(flowChart);
    }

    // ============================================
    // МОДУЛЬ 2: МОБИЛЬНОЕ МЕНЮ (БУРГЕР)
    // ============================================
    const burgerBtn = document.getElementById('burgerBtn');
    const mainNav = document.getElementById('mainNav');
    const body = document.body;

    if (burgerBtn && mainNav) {
        burgerBtn.addEventListener('click', function () {
            const isOpen = mainNav.classList.toggle('nav--open');
            burgerBtn.classList.toggle('burger--active');
            burgerBtn.setAttribute('aria-expanded', isOpen);
            body.style.overflow = isOpen ? 'hidden' : '';
        });

        // Закрытие меню при клике на ссылку
        mainNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mainNav.classList.remove('nav--open');
                burgerBtn.classList.remove('burger--active');
                burgerBtn.setAttribute('aria-expanded', 'false');
                body.style.overflow = '';
            });
        });

        // Закрытие меню при клике вне его
        document.addEventListener('click', (e) => {
            if (!mainNav.contains(e.target) && !burgerBtn.contains(e.target)) {
                mainNav.classList.remove('nav--open');
                burgerBtn.classList.remove('burger--active');
                burgerBtn.setAttribute('aria-expanded', 'false');
                body.style.overflow = '';
            }
        });
    }

    // ============================================
    // МОДУЛЬ 3: ОБЩАЯ ЛОГИКА ДЛЯ ВСЕХ ПОЛЗУНКОВ (RANGE SLIDERS)
    // ============================================
    const allSliders = document.querySelectorAll('input[type="range"]');

    allSliders.forEach(slider => {
        const output = document.getElementById(slider.id + 'Value');
        if (output) {
            output.textContent = formatSliderValue(slider.value, slider.dataset.format);
            slider.addEventListener('input', function () {
                output.textContent = formatSliderValue(this.value, this.dataset.format);
                const calculator = this.closest('.calculator');
                if (calculator) {
                    recalculateCalculator(calculator.id);
                }
            });
        }
    });

    function formatSliderValue(value, format) {
        switch (format) {
            case 'money':
                return parseInt(value).toLocaleString('ru-RU') + ' ₽';
            case 'percent':
                return value + '%';
            case 'time':
                return value + ' мин';
            case 'number':
            default:
                return value;
        }
    }

    // ============================================
    // МОДУЛЬ 4: КАЛЬКУЛЯТОРЫ
    // ============================================
    function recalculateCalculator(calculatorId) {
        switch (calculatorId) {
            case 'calculatorTermometr':
                updateTermometr();
                break;
            case 'calculatorAiRentgen':
                updateAiRentgen();
                break;
            case 'calculatorSlivometr':
                updateSlivometr();
                break;
            case 'calculatorSeoChance':
                updateSeoChance();
                break;
            case 'calculatorContentDeficit':
                updateContentDeficit();
                break;
            case 'calculatorSlepyeZony':
                updateSlepyeZony();
                break;
            default:
                break;
        }
    }

    // --- 4.1. Термометр холодной базы ---
    function updateTermometr() {
        const calls = parseInt(document.getElementById('callsPerDay')?.value || 50);
        const deadPercent = parseInt(document.getElementById('deadPercent')?.value || 30);
        const hourRate = parseInt(document.getElementById('hourRate')?.value || 500);

        const deadCalls = Math.round(calls * (deadPercent / 100));
        const timeWastedPerDay = deadCalls * 3;
        const lossPerDay = Math.round((timeWastedPerDay / 60) * hourRate);
        const lossPerMonth = lossPerDay * 22;

        const lossEl = document.getElementById('termometrLoss');
        const deadCallsEl = document.getElementById('termometrDeadCalls');

        if (lossEl) lossEl.textContent = lossPerMonth.toLocaleString('ru-RU') + ' ₽';
        if (deadCallsEl) deadCallsEl.textContent = deadCalls;
    }

    // --- 4.2. AI-Рентген ---
    function updateAiRentgen() {
        const requests = parseInt(document.getElementById('requestsPerMonth')?.value || 1000);
        const responseTime = parseInt(document.getElementById('responseTime')?.value || 10);
        const managerCost = parseInt(document.getElementById('managerCost')?.value || 60000);

        const aiRequests = Math.round(requests * 0.8);
        const hoursSaved = (responseTime / 60) * aiRequests;
        const managersEquivalent = (hoursSaved / 160).toFixed(1);
        const moneySaved = Math.round(managerCost * parseFloat(managersEquivalent));

        const moneyEl = document.getElementById('aiMoneySaved');
        const managersEl = document.getElementById('aiManagers');
        const timeEl = document.getElementById('aiResponseTime');

        if (moneyEl) moneyEl.textContent = moneySaved.toLocaleString('ru-RU') + ' ₽/мес';
        if (managersEl) managersEl.textContent = managersEquivalent;
        if (timeEl) timeEl.textContent = '3 секунды (вместо ' + responseTime + ' мин)';
    }

    // --- 4.3. Сливометр ---
    function updateSlivometr() {
        const budget = parseInt(document.getElementById('adBudget')?.value || 100000);
        const currentLeads = parseInt(document.getElementById('currentLeads')?.value || 30);
        const leadCost = parseInt(document.getElementById('leadCost')?.value || 2000);

        const possibleLeads = Math.round(budget / leadCost);
        const lostLeads = Math.max(0, possibleLeads - currentLeads);
        const lostMoney = lostLeads * leadCost;

        const lostLeadsEl = document.getElementById('slivLostLeads');
        const lostMoneyEl = document.getElementById('slivLostMoney');

        if (lostLeadsEl) lostLeadsEl.textContent = lostLeads + ' заявок/мес';
        if (lostMoneyEl) lostMoneyEl.textContent = lostMoney.toLocaleString('ru-RU') + ' ₽';
    }

    // --- 4.4. SEO-шанс ---
    function updateSeoChance() {
        const siteUrl = document.getElementById('siteUrl')?.value || 'вашсайт.рф';
        const industry = document.getElementById('seoIndustry')?.value || 'B2B-услуги';
        const targetLeads = parseInt(document.getElementById('seoDesiredLeads')?.value || 50);

        const seed = hashCode(siteUrl + industry);
        const pagesFound = 10 + (seed % 90);
        const errorsFound = 5 + (seed % 30);
        const trafficLoss = 100 + (seed % 900);
        const growthPercent = 100 + (seed % 400);

        const pagesEl = document.getElementById('seoPages');
        const errorsEl = document.getElementById('seoErrors');
        const trafficEl = document.getElementById('seoTrafficLoss');
        const growthEl = document.getElementById('seoGrowth');

        if (pagesEl) pagesEl.textContent = pagesFound;
        if (errorsEl) errorsEl.textContent = errorsFound;
        if (trafficEl) trafficEl.textContent = trafficLoss;
        if (growthEl) growthEl.textContent = '+' + growthPercent + '% за 4-6 месяцев';
    }

    // --- 4.5. Контент-дефицит ---
    function updateContentDeficit() {
        const siteUrl = document.getElementById('contentSiteUrl')?.value || 'вашсайт.рф';
        const industry = document.getElementById('contentIndustry')?.value || 'B2B-услуги';

        const seed = hashCode(siteUrl + industry + 'content');
        const yourPages = 10 + (seed % 50);
        const competitorPages = 100 + (seed % 400);
        const deficit = Math.max(0, competitorPages - yourPages);
        const trafficLoss = deficit * 3;

        const yourEl = document.getElementById('contentYourPages');
        const competitorEl = document.getElementById('contentCompetitorPages');
        const deficitEl = document.getElementById('contentDeficit');
        const trafficEl = document.getElementById('contentTrafficLoss');

        if (yourEl) yourEl.textContent = yourPages;
        if (competitorEl) competitorEl.textContent = competitorPages;
        if (deficitEl) deficitEl.textContent = deficit;
        if (trafficEl) trafficEl.textContent = '~' + trafficLoss + ' визитов/мес';
    }

    // --- 4.6. Слепые зоны ---
    function updateSlepyeZony() {
        const q1 = document.querySelector('input[name="slepQ1"]:checked')?.value === 'yes' ? 1 : 0;
        const q2 = document.querySelector('input[name="slepQ2"]:checked')?.value === 'yes' ? 1 : 0;
        const q3 = document.querySelector('input[name="slepQ3"]:checked')?.value === 'yes' ? 1 : 0;
        const q4 = document.querySelector('input[name="slepQ4"]:checked')?.value === 'yes' ? 1 : 0;

        const totalYes = q1 + q2 + q3 + q4;
        const resultEl = document.getElementById('slepResult');

        if (resultEl) {
            if (totalYes === 4) {
                resultEl.innerHTML = '✅ У вас хорошая аналитика. Мы можем помочь с тонкой настройкой и дашбордами для планёрок.';
            } else if (totalYes >= 2) {
                resultEl.innerHTML = '⚠️ У вас есть база, но ' + (4 - totalYes) + ' слепые зоны. Теряете до ' + ((4 - totalYes) * 15) + '% данных о клиентах.';
            } else {
                resultEl.innerHTML = '🔴 Вы принимаете решения вслепую. Сквозная аналитика окупается за 1 месяц. Давайте покажем, как это работает?';
            }
        }
    }

    // ============================================
    // МОДУЛЬ 5: ОТПРАВКА ФОРМ (ДВА РЕЖИМА)
    // ============================================

    // Режим 1: Формы с data-ajax="true" — отправка через fetch (Formspree)
    const ajaxForms = document.querySelectorAll('form[data-ajax="true"]');

    ajaxForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn?.textContent;

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Отправляем...';
            }

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                },
            })
                .then(response => {
                    if (response.ok) {
                        const redirectUrl = form.dataset.redirect || '/Mngment/thank-you/';
                        window.location.href = redirectUrl;
                    } else {
                        throw new Error('Ошибка отправки');
                    }
                })
                .catch(() => {
                    // Даже при ошибке редиректим, чтобы не терять лида
                    const redirectUrl = form.dataset.redirect || '/Mngment/thank-you/';
                    window.location.href = redirectUrl;
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalBtnText;
                    }
                });
        });
    });

    // Режим 2: Обычные формы (GET/POST без data-ajax) — не перехватываем,
    // они работают стандартным способом. Для GitHub Pages это основной режим.

    // ============================================
    // МОДУЛЬ 6: ПЛАВНЫЙ СКРОЛЛ К ЯКОРЯМ
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            }
        });
    });

    // ============================================
    // МОДУЛЬ 7: КНОПКА «НАВЕРХ»
    // ============================================
    const scrollToTopBtn = document.getElementById('scrollToTop');

    if (scrollToTopBtn) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 600) {
                scrollToTopBtn.classList.add('visible');
            } else {
                scrollToTopBtn.classList.remove('visible');
            }
        });

        scrollToTopBtn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth',
            });
        });
    }

    // ============================================
    // МОДУЛЬ 8: МАСКА ДЛЯ ТЕЛЕФОНА
    // ============================================
    const phoneInputs = document.querySelectorAll('input[type="tel"]');

    phoneInputs.forEach(input => {
        input.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');

            if (value.startsWith('7') || value.startsWith('8')) {
                value = value.substring(1);
            }

            let formatted = '+7 (';
            if (value.length > 0) formatted += value.substring(0, 3);
            if (value.length > 3) formatted += ') ' + value.substring(3, 6);
            if (value.length > 6) formatted += '-' + value.substring(6, 8);
            if (value.length > 8) formatted += '-' + value.substring(8, 10);

            this.value = formatted;
        });

        if (!input.value) {
            input.value = '+7 (';
        }

        input.addEventListener('focus', function () {
            if (this.value === '+7 (') {
                this.setSelectionRange(4, 4);
            }
        });
    });

    // ============================================
    // МОДУЛЬ 9: ТАЙМЕР ОБРАТНОГО ОТСЧЁТА
    // ============================================
    const countdownEl = document.getElementById('countdown');

    if (countdownEl) {
        function startCountdown(minutes) {
            const endTime = new Date().getTime() + minutes * 60 * 1000;

            function updateTimer() {
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance < 0) {
                    countdownEl.textContent = '00:00';
                    return;
                }

                const mins = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const secs = Math.floor((distance % (1000 * 60)) / 1000);

                countdownEl.textContent =
                    String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');

                requestAnimationFrame(() => {
                    setTimeout(updateTimer, 1000);
                });
            }

            updateTimer();
        }

        const minutes = parseInt(countdownEl.dataset.minutes) || 15;
        startCountdown(minutes);
    }

    // ============================================
    // МОДУЛЬ 10: ИНИЦИАЛИЗАЦИЯ КАЛЬКУЛЯТОРОВ
    // ============================================
    const calculatorIds = [
        'calculatorTermometr',
        'calculatorAiRentgen',
        'calculatorSlivometr',
        'calculatorSeoChance',
        'calculatorContentDeficit',
        'calculatorSlepyeZony',
    ];

    calculatorIds.forEach(id => {
        if (document.getElementById(id)) {
            recalculateCalculator(id);
        }
    });

    // ============================================
    // ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
    // ============================================
    function hashCode(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash >>> 0;
        }
        return Math.abs(hash);
    }

    // ============================================
    // ЛОГИРОВАНИЕ (для разработки)
    // ============================================
    if (window.location.hostname === 'localhost' ||
        window.location.hostname === '127.0.0.1' ||
        window.location.hostname.includes('github.io')) {
        console.log('🚀 script.js загружен');
        console.log('📊 Калькуляторов:', calculatorIds.filter(id => document.getElementById(id)).length);
        console.log('📝 Ajax-форм:', ajaxForms.length);
        console.log('📱 Бургер-меню:', burgerBtn ? 'готово' : 'не найдено');
    }

});
