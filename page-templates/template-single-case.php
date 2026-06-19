<?php
/**
 * Template Name: Шаблон одного кейса
 * Используется для CPT "case"
 */

get_header();

while (have_posts()) : the_post();
    $case_result = get_post_meta(get_the_ID(), '_case_result', true);
    $case_client = get_post_meta(get_the_ID(), '_case_client', true);
    $case_task = get_post_meta(get_the_ID(), '_case_task', true);
    $case_solution = get_post_meta(get_the_ID(), '_case_solution', true);
    $industry = get_the_terms(get_the_ID(), 'case_industry');
    $industry_name = ($industry && !is_wp_error($industry)) ? $industry[0]->name : '';
?>

<article class="case-page">
    <section class="section">
        <div class="container">
            <?php agency_breadcrumbs(); ?>

            <div class="case-page__header">
                <?php if ($industry_name) : ?>
                    <span class="case-page__industry"><?php echo esc_html($industry_name); ?></span>
                <?php endif; ?>
                <h1 class="case-page__title"><?php the_title(); ?></h1>
            </div>

            <?php if (has_post_thumbnail()) : ?>
                <div class="case-page__image">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

            <div class="case-page__grid">
                <div class="case-page__sidebar">
                    <?php if ($case_client) : ?>
                        <div class="case-page__info-block">
                            <h3>Клиент</h3>
                            <p><?php echo esc_html($case_client); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($case_task) : ?>
                        <div class="case-page__info-block">
                            <h3>Задача</h3>
                            <p><?php echo esc_html($case_task); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($case_result) : ?>
                        <div class="case-page__info-block case-page__info-block--result">
                            <h3>Результат</h3>
                            <p><?php echo esc_html($case_result); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="case-page__content">
                    <?php if ($case_solution) : ?>
                        <h2>Решение</h2>
                        <div class="case-page__solution">
                            <?php echo wpautop($case_solution); ?>
                        </div>
                    <?php endif; ?>

                    <?php the_content(); ?>
                </div>
            </div>

            <div class="case-page__cta">
                <h2>Хотите такой же результат?</h2>
                <p>Оставьте заявку — обсудим ваш проект и покажем, как добиться похожих цифр в вашей нише.</p>
                <a href="/contacts/" class="btn btn--accent">Обсудить проект</a>
            </div>
        </div>
    </section>

    <!-- Другие кейсы -->
    <?php
    $other_cases = get_posts(array(
        'post_type'      => 'case',
        'posts_per_page' => 2,
        'post__not_in'   => array(get_the_ID()),
    ));
    if (!empty($other_cases)) :
    ?>
    <section class="section section--gray">
        <div class="container">
            <h2 class="section__title">Другие кейсы</h2>
            <div class="cases-grid">
                <?php foreach ($other_cases as $case) : ?>
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
                            <a href="<?php echo esc_url(get_permalink($case)); ?>" class="case-teaser__link">
                                Читать кейс →
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</article>

<?php
endwhile;

get_footer();
