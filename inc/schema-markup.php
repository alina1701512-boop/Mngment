<?php
/**
 * Генерация микроразметки Schema.org (JSON-LD)
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Добавляем разметку Organization на все страницы
 */
function agency_schema_organization() {
    $schema = array(
        '@context'  => 'https://schema.org',
        '@type'     => 'Organization',
        'name'      => get_bloginfo('name'),
        'url'       => home_url(),
        'logo'      => get_template_directory_uri() . '/assets/images/logo.png',
        'contactPoint' => array(
            '@type'       => 'ContactPoint',
            'telephone'   => '+7-XXX-XXX-XX-XX', // ЗАМЕНИТЕ НА РЕАЛЬНЫЙ
            'contactType' => 'sales',
            'availableLanguage' => 'Russian',
        ),
        'sameAs' => array(
            'https://t.me/your_channel',    // ЗАМЕНИТЕ
            'https://vk.com/your_page',     // ЗАМЕНИТЕ
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE) . '</script>';
}
add_action('wp_head', 'agency_schema_organization');

/**
 * Schema.org для страниц услуг (Service)
 */
function agency_schema_service() {
    if (!is_singular('service')) {
        return;
    }

    global $post;

    $schema = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Service',
        'name'        => get_the_title(),
        'description' => get_the_excerpt(),
        'provider'    => array(
            '@type' => 'Organization',
            'name'  => get_bloginfo('name'),
        ),
        'offers' => array(
            '@type'         => 'Offer',
            'price'         => '0', // Цена договорная, указываем 0 или диапазон
            'priceCurrency' => 'RUB',
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE) . '</script>';
}
add_action('wp_head', 'agency_schema_service');

/**
 * Schema.org для FAQ-блоков на странице услуги
 *
 * @param array $faqs Массив вопросов-ответов [['question' => '', 'answer' => '']]
 * @return string JSON-LD скрипт
 */
function agency_get_faq_schema($faqs) {
    if (empty($faqs)) {
        return '';
    }

    $mainEntity = array();
    foreach ($faqs as $faq) {
        $mainEntity[] = array(
            '@type'          => 'Question',
            'name'           => $faq['question'],
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => $faq['answer'],
            ),
        );
    }

    $schema = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $mainEntity,
    );

    return '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE) . '</script>';
}

/**
 * Schema.org для страниц статей блога (Article)
 */
function agency_schema_article() {
    if (!is_singular('post')) {
        return;
    }

    global $post;

    $schema = array(
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        'headline'         => get_the_title(),
        'description'      => get_the_excerpt(),
        'datePublished'    => get_the_date('c'),
        'dateModified'     => get_the_modified_date('c'),
        'author'           => array(
            '@type' => 'Person',
            'name'  => get_the_author(),
        ),
        'publisher'        => array(
            '@type' => 'Organization',
            'name'  => get_bloginfo('name'),
            'logo'  => array(
                '@type' => 'ImageObject',
                'url'   => get_template_directory_uri() . '/assets/images/logo.png',
            ),
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE) . '</script>';
}
add_action('wp_head', 'agency_schema_article');

/**
 * Schema.org для хлебных крошек (BreadcrumbList)
 */
function agency_schema_breadcrumbs() {
    if (is_front_page()) {
        return;
    }

    $breadcrumbs = array(
        array(
            '@type' => 'ListItem',
            'position' => 1,
            'name'     => 'Главная',
            'item'     => home_url(),
        ),
    );

    $position = 2;

    if (is_singular('service')) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => 'Услуги',
            'item'     => home_url('/services/'),
        );
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => get_the_title(),
        );
    } elseif (is_singular('case')) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => 'Кейсы',
            'item'     => home_url('/cases/'),
        );
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => get_the_title(),
        );
    } elseif (is_singular('post')) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => 'Блог',
            'item'     => home_url('/blog/'),
        );
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => get_the_title(),
        );
    }

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $breadcrumbs,
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE) . '</script>';
}
add_action('wp_head', 'agency_schema_breadcrumbs');
