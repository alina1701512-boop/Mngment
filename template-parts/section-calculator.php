<?php
/**
 * Template Part: Блок калькулятора
 *
 * @param array $args {
 *     @type string $calculator_id  ID калькулятора (например, 'calculatorTermometr')
 *     @type string $category       Slug категории услуги
 * }
 */

if (!defined('ABSPATH')) {
    exit;
}

$calculator_id = $args['calculator_id'] ?? '';
$category = $args['category'] ?? '';

if (empty($calculator_id)) {
    return;
}
?>

<div class="calculator" id="<?php echo esc_attr($calculator_id); ?>">
    <?php
    // Подключаем нужный калькулятор
    switch ($calculator_id) {
        case 'calculatorTermometr':
            ?>
            <h3 class="calculator__title">Термометр холодной базы</h3>
            <p class="calculator__subtitle">Посчитайте, сколько вы теряете на неактуальных контактах</p>

            <div class="calculator__steps">
                <div class="calculator__step">
                    <label class="calculator__step-label">
                        Сколько холодных звонков в день делает менеджер?
                        <span class="calculator__step-value" id="callsPerDayValue">50</span>
                    </label>
                    <input type="range" class="calculator__slider" id="callsPerDay"
                           min="10" max="200" value="50" step="5" data-format="number">
                </div>

                <div class="calculator__step">
                    <label class="calculator__step-label">
                        Процент неактуальных номеров в базе?
                        <span class="calculator__step-value" id="deadPercentValue">30%</span>
                    </label>
                    <input type="range" class="calculator__slider" id="deadPercent"
                           min="5" max="70" value="30" step="5" data-format="percent">
                </div>

                <div class="calculator__step">
                    <label class="calculator__step-label">
                        Стоимость часа менеджера (с налогами)?
                        <span class="calculator__step-value" id="hourRateValue">500 ₽</span>
                    </label>
                    <input type="range" class="calculator__slider" id="hourRate"
                           min="200" max="1500" value="500" step="50" data-format="money">
                </div>
            </div>

            <div class="calculator__result">
                <div class="calculator__result-icon">🔴</div>
                <div class="calculator__result-value calculator__result-value--loss">
                    <span id="termometrLoss">0 ₽</span>/мес
                </div>
                <div class="calculator__result-desc">
                    Вы теряете на <strong><span id="termometrDeadCalls">0</span> мёртвых звонках</strong> каждый день.
                    Мы верифицируем номера и гарантируем актуальность 98%.
                </div>
                <div class="calculator__cta">
                    <a href="#serviceForm" class="btn btn--accent">Получить тестовую выгрузку</a>
                </div>
            </div>
            <?php
            break;

        case 'calculatorAiRentgen':
            ?>
            <h3 class="calculator__title">AI-Рентген</h3>
            <p class="calculator__subtitle">Узнайте, сколько сэкономит AI-агент в вашем бизнесе</p>

            <div class="calculator__steps">
                <div class="calculator__step">
                    <label class="calculator__step-label">
                        Сколько типовых обращений в месяц?
                        <span class="calculator__step-value" id="requestsPerMonthValue">1000</span>
                    </label>
                    <input type="range" class="calculator__slider" id="requestsPerMonth"
                           min="100" max="10000" value="1000" step="100" data-format="number">
                </div>

                <div class="calculator__step">
                    <label class="calculator__step-label">
                        Среднее время ответа менеджера (минут)?
                        <span class="calculator__step-value" id="responseTimeValue">10 мин</span>
                    </label>
                    <input type="range" class="calculator__slider" id="responseTime"
                           min="2" max="60" value="10" step="1" data-format="time">
                </div>

                <div class="calculator__step">
                    <label class="calculator__step-label">
                        Средняя стоимость менеджера поддержки?
                        <span class="calculator__step-value" id="managerCostValue">60 000 ₽</span>
                    </label>
                    <input type="range" class="calculator__slider" id="managerCost"
                           min="30000" max="150000" value="60000" step="5000" data-format="money">
                </div>
            </div>

            <div class="calculator__result">
                <div class="calculator__result-icon">🤖</div>
                <div class="calculator__result-value calculator__result-value--saved">
                    <span id="aiMoneySaved">0 ₽</span>/мес
                </div>
                <div class="calculator__result-desc">
                    Это эквивалентно работе <strong><span id="aiManagers">0</span> менеджеров</strong>.
                    Среднее время ответа: <strong id="aiResponseTime">3 секунды</strong>.
                </div>
                <div class="calculator__cta">
                    <a href="#serviceForm" class="btn btn--accent">Запустить пилотную неделю</a>
                </div>
            </div>
            <?php
            break;

        case 'calculatorSlivometr':
            ?>
            <h3 class="calculator__title">Сливометр</h3>
            <p class="calculator__subtitle">Узнайте, сколько заявок вы теряете из-за неоптимизированной рекламы</p>

            <div class="calculator__steps">
                <div class="calculator__step">
                    <label class="calculator__step-label">
                        Месячный бюджет на рекламу?
                        <span class="calculator__step-value" id="adBudgetValue">100 000 ₽</span>
                    </label>
                    <input type="range" class="calculator__slider" id="adBudget"
                           min="10000" max="1000000" value="100000" step="10000" data-format="money">
                </div>

                <div class="calculator__step">
                    <label class="calculator__step-label">
                        Сколько заявок получаете в месяц?
                        <span class="calculator__step-value" id="currentLeadsValue">30</span>
                    </label>
                    <input type="range" class="calculator__slider" id="currentLeads"
                           min="5" max="1000" value="30" step="5" data-format="number">
                </div>

                <div class="calculator__step">
                    <label class="calculator__step-label">
                        Примерная стоимость одной заявки?
                        <span class="calculator__step-value" id="leadCostValue">2 000 ₽</span>
                    </label>
                    <input type="range" class="calculator__slider" id="leadCost"
                           min="100" max="10000" value="2000" step="100" data-format="money">
                </div>
            </div>

            <div class="calculator__result">
                <div class="calculator__result-icon">💸</div>
                <div class="calculator__result-value calculator__result-value--loss">
                    <span id="slivLostMoney">0 ₽</span>
                </div>
                <div class="calculator__result-desc">
                    Вы недополучаете <strong><span id="slivLostLeads">0 заявок/мес</span></strong>.
                    Мы снижаем цену лида на 20-40% за счёт чистки семантики и ретаргетинга.
                </div>
                <div class="calculator__cta">
                    <a href="#serviceForm" class="btn btn--accent">Получить аудит кампании</a>
                </div>
            </div>
            <?php
            break;

        default:
            // Для неизвестного ID ничего не выводим
            break;
    }
    ?>
</div>
