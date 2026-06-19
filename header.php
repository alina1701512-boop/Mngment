<?php
/**
 * Шапка сайта
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<header class="header" id="header">
    <div class="container header__inner">

        <!-- ЛОГОТИП -->
        <div class="header__logo">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="header__logo-link">
                    <span class="header__logo-text"><?php bloginfo('name'); ?></span>
                </a>
            <?php endif; ?>
        </div>

        <!-- ГЛАВНОЕ МЕНЮ (ДЕСКТОП) -->
        <nav class="header__nav" id="mainNav" aria-label="Главное меню">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'header__menu',
                    'fallback_cb'    => false,
                ));
            } else {
                // Базовое меню, если не настроено в админке
                echo '<ul class="header__menu">';
                echo '<li><a href="' . home_url('/services/parsing-lidogeneraciya/') . '">Парсинг</a></li>';
                echo '<li><a href="' . home_url('/services/vnedrenie-amocrm/') . '">CRM</a></li>';
                echo '<li><a href="' . home_url('/services/ai-agenty/') . '">AI-агенты</a></li>';
                echo '<li><a href="' . home_url('/services/marketing-pod-kljuch/') . '">Под ключ</a></li>';
                echo '</ul>';
            }
            ?>
        </nav>

        <!-- CTA-КНОПКА + ТЕЛЕФОН -->
        <div class="header__actions">
            <a href="tel:<?php echo esc_attr(agency_get_phone()); ?>" class="header__phone">
                <?php echo esc_html(agency_get_phone()); ?>
            </a>
            <a href="#auditForm" class="btn btn--primary header__cta">
                Аудит бесплатно
            </a>
        </div>

        <!-- БУРГЕР (МОБИЛЬНОЕ МЕНЮ) -->
        <button class="burger" id="burgerBtn" aria-label="Меню" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>
</header>

<main class="main-content">
