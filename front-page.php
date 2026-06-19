<?php
/**
 * Главная страница
 */

get_header();
?>

<!-- ЭКРАН 1: ОФФЕР + СХЕМА -->
<section class="section hero">
    <div class="container hero__inner">
        <div class="hero__content">
            <h1 class="hero__title">
                Превращаем хаос в digital-систему: от сбора лида до закрытия сделки в одной CRM
            </h1>
            <p class="hero__subtitle">
                Маркетинговое агентство полного цикла. Парсинг, amoCRM, чат-боты, AI-агенты, сайты — собираем в единый механизм продаж.
            </p>
            <a href="/tools/audit-voronki/" class="btn btn--accent hero__cta">
                Получить аудит текущей воронки
            </a>
        </div>

        <!-- Анимированная схема -->
        <div class="hero__chart" id="flowChart">
            <div class="flow-chart">
                <div class="flow-chart__node flow-chart__node--traffic">
                    <span class="flow-chart__icon">🔍</span>
                    <span class="flow-chart__label">Трафик и сбор</span>
                </div>
                <div class="connector"></div>
                <div class="flow-chart__node flow-chart__node--capture">
                    <span class="flow-chart__icon">📥</span>
                    <span class="flow-chart__label">Захват лида</span>
                </div>
                <div class="connector"></div>
                <div class="flow-chart__node flow-chart__node--crm">
                    <span class="flow-chart__icon">📊</span>
                    <span class="flow-chart__label">CRM</span>
                </div>
                <div class="connector"></div>
                <div class="flow-chart__node flow-chart__node--warm">
                    <span class="flow-chart__icon">🤖</span>
                    <span class="flow-chart__label">Автопрогрев</span>
                </div>
                <div class="connector"></div>
                <div class="flow-chart__node flow-chart__node--deal">
                    <span class="flow-chart__icon">💰</span>
                    <span class="flow-chart__label">Сделка</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ЭКРАН 2: УСЛУГИ -->
<section class="section section--gray services-overview">
    <div class="container">
        <h2 class="section__title">Из чего мы собираем ваш отдел продаж</h2>

        <div class="services-grid">
            <?php
            $main_services = array(
                'parsing'     => array('🎯', 'Парсинг и базы данных', 'Контакты, которые не найти вручную'),
                'crm'         => array('📊', 'Внедрение AmoCRM', 'Воронки, в которых не вязнут сделки'),
                'chat-boty'   => array('🤖', 'Чат-боты', 'Автоответы 24/7 без зарплаты'),
                'ai'          => array('🧠', 'AI-агенты', 'Нейросети, обученные на ваших регламентах'),
                'sajty'       => array('🌐', 'Разработка сайтов', 'Не просто витрина — инструмент продаж'),
                'full-cycle'  => array('🔗', 'Комплексный контракт', 'Всё выше — в единой системе'),
            );

            foreach ($main_services as $slug => $data) :
                $service_url = agency_get_service_url_by_category($slug);
            ?>
                <a href="<?php echo esc_url($service_url); ?>" class="service-card">
                    <span class="service-card__icon"><?php echo $data[0]; ?></span>
                    <h3 class="service-card__title"><?php echo $data[1]; ?></h3>
                    <p class="service-card__desc"><?php echo $data[2]; ?></p>
                    <span class="service-card__link">Подробнее →</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ЭКРАН 3: БОЛИ (ДО/ПОСЛЕ) -->
<section class="section pain-points">
    <div class="container">
        <h2 class="section__title">Знакомо?</h2>

        <div class="pain-grid">
            <div class="pain-card">
                <h3 class="pain-card__title">❌ Менеджеры жалуются, что база «дохлая»</h3>
                <p class="pain-card__desc">✅ <strong>С нами:</strong> База обновляется автоматически, актуальность 98%</p>
            </div>

            <div class="pain-card">
                <h3 class="pain-card__title">❌ CRM стоит, но никто не заполняет воронку</h3>
                <p class="pain-card__desc">✅ <strong>С нами:</strong> AmoCRM сама создаёт сделки из заявок с сайта, ботов и звонков</p>
            </div>

            <div class="pain-card">
                <h3 class="pain-card__title">❌ После 18:00 заявки уходят конкурентам</h3>
                <p class="pain-card__desc">✅ <strong>С нами:</strong> Чат-бот квалифицирует лида и записывает на звонок в 08:00 утра</p>
            </div>
        </div>

        <div class="pain-cta">
            <a href="/services/marketing-pod-kljuch/" class="btn btn--primary">Хочу так же</a>
        </div>
    </div>
</section>

<!-- ЭКРАН 4: КЕЙСЫ (ТИЗЕРЫ) -->
<section class="section section--gray cases-overview">
    <div class="container">
        <h2 class="section__title">Цифры, за которые не стыдно</h2>

        <div class="cases-grid">
            <?php
            $featured_cases = get_posts(array(
                'post_type'      => 'case',
                'posts_per_page' => 3,
                'meta_key'       => '_featured',
                'meta_value'     => '1',
            ));

            // Если нет отмеченных кейсов — берём последние
            if (empty($featured_cases)) {
                $featured_cases = get_posts(array(
                    'post_type'      => 'case',
                    'posts_per_page' => 3,
                ));
            }

            foreach ($featured_cases as $case) :
                $case_result = get_post_meta($case->ID, '_case_result', true);
            ?>
                <article class="case-teaser">
                    <?php if (has_post_thumbnail($case)) : ?>
                        <div class="case-teaser__image">
                            <?php echo get_the_post_thumbnail($case, 'case-thumbnail'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="case-teaser__content">
                        <h3 class="case-teaser__title">
                            <a href="<?php echo esc_url(get_permalink($case)); ?>">
                                <?php echo esc_html($case->post_title); ?>
                            </a>
                        </h3>
                        <?php if ($case_result) : ?>
                            <p class="case-teaser__result"><?php echo esc_html($case_result); ?></p>
                        <?php endif; ?>
                        <a href="<?php echo esc_url(get_permalink($case)); ?>" class="case-teaser__link">
                            Читать кейс →
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="cases-cta">
            <a href="/cases/" class="btn btn--outline">Все кейсы</a>
        </div>
    </div>
</section>

<!-- ЭКРАН 5: ЛИД-МАГНИТ -->
<section class="section lead-magnet" id="auditForm">
    <div class="container">
        <h2 class="section__title">Узнайте, где теряете деньги прямо сейчас</h2>
        <p class="section__subtitle">
            Ответьте на 5 вопросов — получите персональный аудит воронки и чек-лист «7 точек утечки лидов»
        </p>

        <form class="form lead-form" data-ajax="true" data-redirect="/thank-you/" method="POST"
              action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">

            <?php wp_nonce_field('agency_lead_nonce', '_wpnonce'); ?>
            <input type="hidden" name="action" value="agency_send_lead">
            <input type="hidden" name="service" value="Аудит воронки">

            <div class="form__grid">
                <div class="form__group">
                    <label for="leadName" class="form__label">Имя</label>
                    <input type="text" id="leadName" name="name" class="form__input" required
                           placeholder="Александр">
                </div>

                <div class="form__group">
                    <label for="leadPhone" class="form__label">Телефон / Telegram</label>
                    <input type="tel" id="leadPhone" name="phone" class="form__input" required
                           placeholder="+7 (XXX) XXX-XX-XX">
                </div>

                <div class="form__group">
                    <label for="leadIndustry" class="form__label">Сфера бизнеса</label>
                    <select id="leadIndustry" name="industry" class="form__select">
                        <option value="">Выберите...</option>
                        <option value="B2B-услуги">B2B-услуги</option>
                        <option value="B2C-товары">B2C-товары</option>
                        <option value="EdTech">EdTech</option>
                        <option value="Производство">Производство</option>
                        <option value="Строительство">Строительство</option>
                        <option value="Логистика">Логистика</option>
                        <option value="Другое">Другое</option>
                    </select>
                </div>

                <div class="form__group">
                    <label for="leadVolume" class="form__label">
                        Сколько заявок в месяц обрабатываете? (<span id="sliderValue">50</span>)
                    </label>
                    <input type="range" id="leadsSlider" name="leads_volume" class="form__slider"
                           min="10" max="5000" value="50" step="10" data-format="number">
                </div>

                <div class="form__group">
                    <label for="leadCrm" class="form__label">Какая CRM используется?</label>
                    <select id="leadCrm" name="crm" class="form__select">
                        <option value="">Выберите...</option>
                        <option value="AmoCRM">AmoCRM</option>
                        <option value="Bitrix24">Bitrix24</option>
                        <option value="Другая">Другая</option>
                        <option value="Нет CRM">Нет CRM</option>
                    </select>
                </div>
            </div>

            <div class="form__submit">
                <button type="submit" class="btn btn--accent btn--full">
                    Получить аудит и чек-лист
                </button>
            </div>
        </form>
    </div>
</section>

<?php
get_footer();
