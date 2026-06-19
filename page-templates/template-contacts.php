<?php
/**
 * Template Name: Контакты
 */

get_header();
?>

<section class="section">
    <div class="container">
        <?php agency_breadcrumbs(); ?>

        <h1 class="section__title">Напишите, что болит — подскажем, чем помочь</h1>

        <div class="contacts-layout">
            <div class="contacts-info">
                <h3>Свяжитесь с нами</h3>
                <ul class="contacts-list">
                    <li>
                        <strong>Telegram:</strong>
                        <a href="<?php echo esc_url(agency_get_telegram_url()); ?>" target="_blank" rel="noopener">
                            <?php echo esc_html(agency_get_telegram_url()); ?>
                        </a>
                    </li>
                    <li>
                        <strong>Телефон:</strong>
                        <a href="tel:<?php echo esc_attr(agency_get_phone()); ?>">
                            <?php echo esc_html(agency_get_phone()); ?>
                        </a>
                    </li>
                    <?php if (get_theme_mod('agency_email')) : ?>
                    <li>
                        <strong>Email:</strong>
                        <a href="mailto:<?php echo esc_attr(get_theme_mod('agency_email')); ?>">
                            <?php echo esc_html(get_theme_mod('agency_email')); ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="contacts-form">
                <form class="form lead-form" data-ajax="true" data-redirect="/thank-you/" method="POST"
                      action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">

                    <?php wp_nonce_field('agency_lead_nonce', '_wpnonce'); ?>
                    <input type="hidden" name="action" value="agency_send_lead">
                    <input type="hidden" name="service" value="Заявка с контактов">

                    <div class="form__grid">
                        <div class="form__group">
                            <label for="contactName" class="form__label">Имя *</label>
                            <input type="text" id="contactName" name="name" class="form__input" required>
                        </div>

                        <div class="form__group">
                            <label for="contactPhone" class="form__label">Телефон / Telegram *</label>
                            <input type="tel" id="contactPhone" name="phone" class="form__input" required>
                        </div>

                        <div class="form__group form__group--full">
                            <label for="contactTask" class="form__label">Какая задача стоит?</label>
                            <select id="contactTask" name="task" class="form__select">
                                <option value="">Выберите...</option>
                                <option value="Нет лидов">Нет лидов</option>
                                <option value="Не настроена CRM">Не настроена CRM</option>
                                <option value="Нужен сайт">Нужен сайт</option>
                                <option value="Хочу автоматизировать поддержку">Хочу автоматизировать поддержку</option>
                                <option value="Нужен комплекс">Нужен комплекс</option>
                                <option value="Другое">Другое</option>
                            </select>
                        </div>

                        <div class="form__group form__group--full">
                            <label for="contactMessage" class="form__label">Опишите ситуацию</label>
                            <textarea id="contactMessage" name="message" class="form__input" rows="4"></textarea>
                        </div>

                        <div class="form__group form__group--full">
                            <label class="form__label">Бюджетная вилка</label>
                            <div class="form__radio-group">
                                <label><input type="radio" name="budget" value="До 50 000"> До 50 000 ₽</label>
                                <label><input type="radio" name="budget" value="50-150 тыс"> 50 000 – 150 000 ₽</label>
                                <label><input type="radio" name="budget" value="150-500 тыс"> 150 000 – 500 000 ₽</label>
                                <label><input type="radio" name="budget" value="500 тыс+"> 500 000+ ₽</label>
                                <label><input type="radio" name="budget" value="Не знаю"> Пока не знаю</label>
                            </div>
                        </div>
                    </div>

                    <div class="form__submit">
                        <button type="submit" class="btn btn--accent">Отправить заявку</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
