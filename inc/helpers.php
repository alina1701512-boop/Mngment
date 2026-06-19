<?php
/**
 * Вспомогательные функции темы
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Получить URL страницы услуги по slug'у категории
 *
 * @param string $category_slug Slug категории услуги
 * @return string URL страницы услуги или '#'
 */
function agency_get_service_url_by_category($category_slug) {
    $posts = get_posts(array(
        'post_type'      => 'service',
        'posts_per_page' => 1,
        'tax_query'      => array(
            array(
                'taxonomy' => 'service_category',
                'field'    => 'slug',
                'terms'    => $category_slug,
            ),
        ),
    ));

    if (!empty($posts)) {
        return get_permalink($posts[0]);
    }

    return '#';
}

/**
 * Получить список FAQ-вопросов для страницы услуги
 *
 * @param string $faq_category_slug Slug категории FAQ
 * @param int    $limit Количество вопросов
 * @return array Массив с ключами 'question' и 'answer'
 */
function agency_get_faq_by_category($faq_category_slug, $limit = 5) {
    $posts = get_posts(array(
        'post_type'      => 'faq',
        'posts_per_page' => $limit,
        'tax_query'      => array(
            array(
                'taxonomy' => 'faq_category',
                'field'    => 'slug',
                'terms'    => $faq_category_slug,
            ),
        ),
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ));

    $faqs = array();
    foreach ($posts as $post) {
        $faqs[] = array(
            'question' => $post->post_title,
            'answer'   => apply_filters('the_content', $post->post_content),
        );
    }

    return $faqs;
}

/**
 * Получить телефон компании из настроек темы
 *
 * @return string Отформатированный телефон
 */
function agency_get_phone() {
    $phone = get_theme_mod('agency_phone', '+7 (XXX) XXX-XX-XX');
    return $phone;
}

/**
 * Получить ссылку на Telegram из настроек темы
 *
 * @return string URL
 */
function agency_get_telegram_url() {
    return get_theme_mod('agency_telegram', 'https://t.me/your_channel');
}

/**
 * Форматирование числа для отображения в кейсах и калькуляторах
 *
 * @param int|float $number Число
 * @param bool      $is_money Форматировать как деньги
 * @return string
 */
function agency_format_number($number, $is_money = false) {
    $formatted = number_format($number, 0, ',', ' ');
    if ($is_money) {
        $formatted .= ' ₽';
    }
    return $formatted;
}

/**
 * Генерация UTM-меток для внутренних ссылок (если нужно отслеживать переходы)
 *
 * @param string $url Базовый URL
 * @param string $source Источник (например, 'sidebar', 'footer', 'blog')
 * @return string URL с UTM-метками
 */
function agency_add_utm($url, $source = 'internal') {
    if (empty($url) || $url === '#') {
        return $url;
    }

    $args = array(
        'utm_source'   => $source,
        'utm_medium'   => 'organic',
        'utm_campaign' => 'perelinkovka',
    );

    return add_query_arg($args, $url);
}

/**
 * Проверить, находимся ли мы на странице услуги
 *
 * @return bool
 */
function agency_is_service_page() {
    return is_singular('service');
}

/**
 * Проверить, находимся ли мы на странице кейса
 *
 * @return bool
 */
function agency_is_case_page() {
    return is_singular('case');
}

/**
 * Обрезать текст до нужной длины
 *
 * @param string $text Исходный текст
 * @param int    $length Максимальная длина
 * @return string
 */
function agency_trim_text($text, $length = 150) {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . '...';
}
