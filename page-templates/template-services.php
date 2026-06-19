<?php
/**
 * Template Name: Шаблон страницы услуги
 * Используется для всех страниц услуг (CPT "service")
 */

get_header();

while (have_posts()) : the_post();
    // Определяем категорию услуги для FAQ и связанных блоков
    $categories = get_the_terms(get_the_ID(), 'service_category');
    $category_slug = ($categories && !is_wp_error($categories)) ? $categories[0]->slug : '';
?>

<article class="service-page">
    <!-- ЭКРАН 1: ОФФЕР + КАЛЬКУЛЯТОР -->
    <section class="section service-hero">
        <div class="container service-hero__inner">
            <div class="service-hero__content">
                <?php agency_breadcrumbs(); ?>
                <h1 class="service-hero__title"><?php the_title(); ?></h1>
                <div class="service-hero__subtitle">
                    <?php echo get_the_excerpt(); ?>
                </div>
                <?php if (have_rows('hero_features')) : ?>
                    <ul class="service-hero__features">
                        <?php while (have_rows('hero_features')) : the_row(); ?>
                            <li><?php the_sub_field('feature'); ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="service-hero__calculator">
                <?php
                // Вставляем калькулятор в зависимости от категории
                $calculator_map = array(
                    'parsing'  => 'calculatorTermometr',
                    'ai'       => 'calculatorAiRentgen',
                    'ppc'      => 'calculatorSlivometr',
                    'seo'      => 'calculatorSeoChance',
                    'kontent'  => 'calculatorContentDeficit',
                    'analitika'=> 'calculatorSlepyeZony',
                );

                if (isset($calculator_map[$category_slug])) :
                    get_template_part('template-parts/section', 'calculator', array(
                        'calculator_id' => $calculator_map[$category_slug],
                        'category'      => $category_slug,
                    ));
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- ЭКРАН 2: ДО/ПОСЛЕ -->
    <?php if (have_rows('before_after')) : ?>
    <section class="section section--gray service-before-after">
        <div class="container">
            <h2 class="section__title">Что меняется после внедрения</h2>
            <div class="before-after-table">
                <table>
                    <thead>
                        <tr>
                            <th>Критерий</th>
                            <th>Было</th>
                            <th>Стало</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while (have_rows('before_after')) : the_row(); ?>
                        <tr>
                            <td><?php the_sub_field('criterion'); ?></td>
                            <td class="before-after__was"><?php the_sub_field('before'); ?></td>
                            <td class="before-after__became"><?php the_sub_field('after'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ЭКРАН 3: ПРОЦЕСС РАБОТЫ -->
    <?php if (have_rows('process_steps')) : ?>
    <section class="section service-process">
        <div class="container">
            <h2 class="section__title">Как мы работаем</h2>
            <div class="process-grid">
                <?php
                $step_number = 1;
                while (have_rows('process_steps')) : the_row();
                ?>
                <div class="process-card">
                    <div class="process-card__number"><?php echo $step_number++; ?></div>
                    <h3 class="process-card__title"><?php the_sub_field('step_title'); ?></h3>
                    <p class="process-card__desc"><?php the_sub_field('step_description'); ?></p>
                    <?php if (get_sub_field('step_result')) : ?>
                        <p class="process-card__result"><strong>Результат:</strong> <?php the_sub_field('step_result'); ?></p>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ЭКРАН 4: КЕЙС -->
    <?php
    $related_case = get_field('related_case');
    if ($related_case) :
        $case_result = get_post_meta($related_case->ID, '_case_result', true);
    ?>
    <section class="section section--gray service-case">
        <div class="container">
            <h2 class="section__title">Кейс: <?php echo esc_html($related_case->post_title); ?></h2>
            <div class="case-full">
                <?php if (has_post_thumbnail($related_case)) : ?>
                    <div class="case-full__image">
                        <?php echo get_the_post_thumbnail($related_case, 'large'); ?>
                    </div>
                <?php endif; ?>
                <div class="case-full__content">
                    <?php echo apply_filters('the_content', $related_case->post_content); ?>
                </div>
                <?php if ($case_result) : ?>
                    <div class="case-full__result">
                        <strong>Результат:</strong> <?php echo esc_html($case_result); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="case-cta">
                <a href="#serviceForm" class="btn btn--primary">Хочу такой же результат</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ЭКРАН 5: FAQ -->
    <?php
    $faqs = agency_get_faq_by_category($category_slug);
    if (!empty($faqs)) :
    ?>
    <section class="section service-faq">
        <div class="container">
            <h2 class="section__title">Частые вопросы</h2>
            <div class="faq-list" itemscope itemtype="https://schema.org/FAQPage">
                <?php foreach ($faqs as $index => $faq) : ?>
                    <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <button class="faq-item__question" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                itemprop="name">
                            <?php echo esc_html($faq['question']); ?>
                            <span class="faq-item__icon">+</span>
                        </button>
                        <div class="faq-item__answer" itemscope itemprop="acceptedAnswer"
                             itemtype="https://schema.org/Answer" <?php echo $index === 0 ? 'style="display:block"' : ''; ?>>
                            <div itemprop="text">
                                <?php echo $faq['answer']; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- Schema.org для FAQ -->
    <?php echo agency_get_faq_schema($faqs); ?>
    <?php endif; ?>

    <!-- ЭКРАН 6: ФОРМА ЗАХВАТА -->
    <section class="section section--gray service-form" id="serviceForm">
        <div class="container">
            <h2 class="section__title">Готовы обсудить проект?</h2>
            <p class="section__subtitle">Оставьте заявку — проведём бесплатный аудит и покажем точки роста</p>

            <form class="form lead-form" data-ajax="true" data-redirect="/thank-you/" method="POST"
                  action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">

                <?php wp_nonce_field('agency_lead_nonce', '_wpnonce'); ?>
                <input type="hidden" name="action" value="agency_send_lead">
                <input type="hidden" name="service" value="<?php the_title(); ?>">
                <input type="hidden" name="page_url" value="<?php the_permalink(); ?>">

                <div class="form__grid">
                    <div class="form__group">
                        <label for="serviceName" class="form__label">Имя *</label>
                        <input type="text" id="serviceName" name="name" class="form__input" required
                               placeholder="Александр">
                    </div>

                    <div class="form__group">
                        <label for="servicePhone" class="form__label">Телефон / Telegram *</label>
                        <input type="tel" id="servicePhone" name="phone" class="form__input" required
                               placeholder="+7 (XXX) XXX-XX-XX">
                    </div>

                    <div class="form__group form__group--full">
                        <label for="serviceMessage" class="form__label">Опишите задачу (необязательно)</label>
                        <textarea id="serviceMessage" name="message" class="form__input" rows="3"
                                  placeholder="Например: нужно собрать базу поставщиков в Казани"></textarea>
                    </div>
                </div>

                <div class="form__submit">
                    <button type="submit" class="btn btn--accent">
                        Отправить заявку
                    </button>
                </div>
            </form>
        </div>
    </section>
</article>

<?php
endwhile;

get_footer();
