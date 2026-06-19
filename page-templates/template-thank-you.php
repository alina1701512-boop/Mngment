<?php
/**
 * Template Name: Спасибо
 */

get_header();
?>

<section class="section thank-you">
    <div class="container thank-you__inner">
        <span class="thank-you__icon">✅</span>
        <h1 class="thank-you__title">Заявка получена. Мы уже начали работу.</h1>

        <div class="thank-you__timeline">
            <div class="thank-you__step">
                <div class="thank-you__step-time">Сегодня</div>
                <div class="thank-you__step-text">Аналитик изучит вашу ситуацию и подготовит вопросы для звонка</div>
            </div>
            <div class="thank-you__step">
                <div class="thank-you__step-time">Завтра</div>
                <div class="thank-you__step-text">Созвон на 15 минут — уточним детали и покажем предварительное решение</div>
            </div>
            <div class="thank-you__step">
                <div class="thank-you__step-time">Через 2-3 дня</div>
                <div class="thank-you__step-text">Вы получите дорожную карту и коммерческое предложение</div>
            </div>
        </div>

        <p class="thank-you__cta-text">
            А пока — посмотрите наш Telegram-канал с кейсами и полезными материалами:
        </p>
        <a href="<?php echo esc_url(agency_get_telegram_url()); ?>" class="btn btn--primary" target="_blank" rel="noopener">
            Перейти в Telegram
        </a>

        <a href="<?php echo home_url(); ?>" class="thank-you__back">
            ← Вернуться на главную
        </a>
    </div>
</section>

<?php
get_footer();
