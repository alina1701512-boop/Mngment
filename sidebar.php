<?php
/**
 * Сайдбар блога
 * Динамически выводит связанные услуги и инструменты
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<aside class="sidebar" id="sidebar">
    <?php if (is_active_sidebar('blog-sidebar')) : ?>
        <?php dynamic_sidebar('blog-sidebar'); ?>
    <?php endif; ?>

    <!-- БЛОК 1: СВЯЗАННЫЕ УСЛУГИ -->
    <?php
    $related_services = agency_get_related_services();
    if (!empty($related_services)) :
    ?>
    <div class="sidebar-widget sidebar-widget--services">
        <h3 class="sidebar-widget__title">Связанные услуги</h3>
        <ul class="sidebar-services">
            <?php foreach ($related_services as $service) : ?>
                <li class="sidebar-services__item">
                    <a href="<?php echo esc_url(get_permalink($service)); ?>" class="sidebar-services__link">
                        <?php if (has_post_thumbnail($service->ID)) : ?>
                            <span class="sidebar-services__icon">
                                <?php echo get_the_post_thumbnail($service->ID, 'service-icon'); ?>
                            </span>
                        <?php endif; ?>
                        <span class="sidebar-services__text">
                            <span class="sidebar-services__name">
                                <?php echo esc_html($service->post_title); ?>
                            </span>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- БЛОК 2: ИНСТРУМЕНТЫ ИЗ СТАТЬИ -->
    <?php
    $related_tools = agency_get_related_tools();
    if (!empty($related_tools)) :
    ?>
    <div class="sidebar-widget sidebar-widget--tools">
        <h3 class="sidebar-widget__title">Инструменты из статьи</h3>
        <ul class="sidebar-tools">
            <?php foreach ($related_tools as $tool_name => $tool_url) : ?>
                <li class="sidebar-tools__item">
                    <a href="<?php echo esc_url($tool_url); ?>" class="sidebar-tools__link">
                        <?php echo esc_html($tool_name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- БЛОК 3: CTA (Всегда показываем) -->
    <div class="sidebar-widget sidebar-widget--cta">
        <h3 class="sidebar-widget__title">Нужна помощь?</h3>
        <p class="sidebar-widget__text">
            Не знаете, с чего начать? Оставьте заявку — проведём бесплатный аудит и покажем точки роста.
        </p>
        <a href="#auditForm" class="btn btn--primary btn--full">
            Получить аудит
        </a>
    </div>

    <!-- БЛОК 4: ПОСЛЕДНИЕ КЕЙСЫ -->
    <?php
    $latest_cases = get_posts(array(
        'post_type'      => 'case',
        'posts_per_page' => 2,
    ));
    if (!empty($latest_cases)) :
    ?>
    <div class="sidebar-widget sidebar-widget--cases">
        <h3 class="sidebar-widget__title">Последние кейсы</h3>
        <ul class="sidebar-cases">
            <?php foreach ($latest_cases as $case) : ?>
                <li class="sidebar-cases__item">
                    <a href="<?php echo esc_url(get_permalink($case)); ?>" class="sidebar-cases__link">
                        <?php echo esc_html($case->post_title); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
</aside>
