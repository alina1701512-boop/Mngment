<?php
/**
 * Agency Custom Theme — Главный файл функций
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================
// 1. ПОДКЛЮЧЕНИЕ МОДУЛЕЙ ИЗ ПАПКИ INC/
// ============================================
require_once get_template_directory() . '/inc/cpt-services.php';
require_once get_template_directory() . '/inc/cpt-cases.php';
require_once get_template_directory() . '/inc/cpt-faq.php';
require_once get_template_directory() . '/inc/schema-markup.php';
require_once get_template_directory() . '/inc/webhook-handler.php';
require_once get_template_directory() . '/inc/dynamic-linking.php';
require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/breadcrumbs.php';

// ============================================
// 2. НАСТРОЙКА ТЕМЫ
// ============================================
function agency_theme_setup() {
    // Локализация
    load_theme_textdomain('agency', get_template_directory() . '/languages');

    // Поддержка темы
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
        'navigation-widgets',
    ));
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('responsive-embeds');
    add_theme_support('customize-selective-refresh-widgets');

    // Регистрация меню
    register_nav_menus(array(
        'primary'   => 'Главное меню (шапка)',
        'footer_1'  => 'Футер — колонка 1 (Трафик)',
        'footer_2'  => 'Футер — колонка 2 (Конверсия)',
        'footer_3'  => 'Футер — колонка 3 (Управление)',
        'footer_4'  => 'Футер — колонка 4 (Всё сразу)',
        'mobile'    => 'Мобильное меню',
    ));

    // Размеры изображений
    add_image_size('case-thumbnail', 600, 400, true);
    add_image_size('blog-thumbnail', 800, 450, true);
    add_image_size('service-icon', 80, 80, true);
}
add_action('after_setup_theme', 'agency_theme_setup');

// ============================================
// 3. ПОДКЛЮЧЕНИЕ СТИЛЕЙ И СКРИПТОВ
// ============================================
function agency_enqueue_assets() {
    $theme_version = wp_get_theme()->get('Version');
    $template_uri = get_template_directory_uri();

    // Основной файл стилей
    wp_enqueue_style(
        'agency-main',
        $template_uri . '/css/main.css',
        array(),
        $theme_version
    );

    // Стили для калькуляторов
    wp_enqueue_style(
        'agency-calculators',
        $template_uri . '/css/calculators.css',
        array('agency-main'),
        $theme_version
    );

    // Основной скрипт
    wp_enqueue_script(
        'agency-script',
        $template_uri . '/js/script.js',
        array(), // jQuery не нужен — всё на нативном JS
        $theme_version,
        true // Подключаем в футере
    );

    // Передаём данные в script.js
    wp_localize_script('agency-script', 'agencyData', array(
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'ajaxNonce' => wp_create_nonce('agency_lead_nonce'),
        'siteUrl'  => home_url(),
        'themeUrl' => $template_uri,
    ));

    // Стили для мобильных (подключаем последними для приоритета)
    wp_enqueue_style(
        'agency-mobile',
        $template_uri . '/css/mobile.css',
        array('agency-main'),
        $theme_version,
        'screen and (max-width: 768px)'
    );

    // Google Fonts (опционально)
    wp_enqueue_style(
        'agency-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        array(),
        null
    );
}
add_action('wp_enqueue_scripts', 'agency_enqueue_assets');

// ============================================
// 4. КАСТОМАЙЗЕР (НАСТРОЙКИ ТЕМЫ)
// ============================================
function agency_customizer_settings($wp_customize) {
    // Секция: Контакты
    $wp_customize->add_section('agency_contacts', array(
        'title'    => 'Контакты',
        'priority' => 30,
    ));

    $wp_customize->add_setting('agency_phone', array(
        'default'   => '+7 (XXX) XXX-XX-XX',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('agency_phone', array(
        'label'   => 'Телефон',
        'section' => 'agency_contacts',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('agency_telegram', array(
        'default'   => 'https://t.me/your_channel',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('agency_telegram', array(
        'label'   => 'Ссылка на Telegram',
        'section' => 'agency_contacts',
        'type'    => 'url',
    ));

    $wp_customize->add_setting('agency_email', array(
        'default'   => 'info@domain.ru',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('agency_email', array(
        'label'   => 'Email',
        'section' => 'agency_contacts',
        'type'    => 'email',
    ));
}
add_action('customize_register', 'agency_customizer_settings');

// ============================================
// 5. ОТКЛЮЧАЕМ ЛИШНЕЕ
// ============================================
// Отключаем эмодзи (ускоряет загрузку)
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Отключаем REST API для незалогиненных (безопасность)
add_filter('rest_authentication_errors', function ($result) {
    if (!is_user_logged_in() && !empty($result)) {
        return new WP_Error('rest_not_logged_in', 'Вы не авторизованы.', array('status' => 401));
    }
    return $result;
});

// Отключаем версию WordPress в исходном коде
remove_action('wp_head', 'wp_generator');

// ============================================
// 6. РЕГИСТРАЦИЯ САЙДБАРА ДЛЯ БЛОГА
// ============================================
function agency_register_sidebars() {
    register_sidebar(array(
        'name'          => 'Сайдбар блога',
        'id'            => 'blog-sidebar',
        'description'   => 'Боковая колонка в блоге. Сюда выводятся динамические блоки «Связанные услуги» и «Инструменты».',
        'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="sidebar-widget__title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'agency_register_sidebars');

// ============================================
// 7. НАСТРОЙКА ДЛИНЫ АНОНСА
// ============================================
function agency_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'agency_excerpt_length');

function agency_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'agency_excerpt_more');

// ============================================
// 8. ПОДДЕРЖКА SVG
// ============================================
function agency_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'agency_mime_types');

// ============================================
// 9. ПЕРЕНАПРАВЛЕНИЕ ПОСЛЕ ФОРМЫ
// ============================================
function agency_thank_you_redirect() {
    // Если это AJAX-запрос нашей формы — просто пропускаем
    if (wp_doing_ajax()) {
        return;
    }
}
add_action('template_redirect', 'agency_thank_you_redirect');

// ============================================
// 10. АВТОМАТИЧЕСКОЕ ОБНОВЛЕНИЕ ГОДА В ФУТЕРЕ
// ============================================
function agency_get_current_year() {
    return date('Y');
}
