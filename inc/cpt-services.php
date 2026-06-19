<?php
/**
 * Регистрация кастомного типа записи «Услуги» (Services)
 * и таксономии «Категории услуг» (service_category)
 */

if (!defined('ABSPATH')) {
    exit; // Прямой доступ запрещён
}

/**
 * Регистрация CPT «Услуги»
 */
function agency_register_cpt_services() {
    $labels = array(
        'name'                  => 'Услуги',
        'singular_name'         => 'Услуга',
        'menu_name'             => 'Услуги',
        'name_admin_bar'        => 'Услугу',
        'add_new'               => 'Добавить услугу',
        'add_new_item'          => 'Добавить новую услугу',
        'new_item'              => 'Новая услуга',
        'edit_item'             => 'Редактировать услугу',
        'view_item'             => 'Просмотреть услугу',
        'all_items'             => 'Все услуги',
        'search_items'          => 'Искать услуги',
        'not_found'             => 'Услуги не найдены',
        'not_found_in_trash'    => 'В корзине услуги не найдены',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array(
            'slug'       => 'services',
            'with_front' => false,
        ),
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-hammer',
        'supports'            => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'custom-fields',
            'page-attributes',
        ),
        'show_in_rest'        => true, // Включаем Gutenberg
        'template'            => array(
            array('core/heading', array(
                'level'       => 1,
                'placeholder' => 'Заголовок услуги (H1)',
            )),
            array('core/paragraph', array(
                'placeholder' => 'Подзаголовок — оффер услуги',
            )),
            array('acf/calculator-block', array()),
            array('acf/before-after-block', array()),
            array('acf/case-teaser-block', array()),
            array('acf/faq-block', array()),
        ),
        'template_lock'       => 'insert', // Разрешаем добавлять блоки, но не удалять основные
    );

    register_post_type('service', $args);
}
add_action('init', 'agency_register_cpt_services');

/**
 * Регистрация таксономии «Категории услуг»
 */
function agency_register_taxonomy_service_category() {
    $labels = array(
        'name'              => 'Категории услуг',
        'singular_name'     => 'Категория услуги',
        'search_items'      => 'Искать категории',
        'all_items'         => 'Все категории',
        'parent_item'       => 'Родительская категория',
        'parent_item_colon' => 'Родительская категория:',
        'edit_item'         => 'Редактировать категорию',
        'update_item'       => 'Обновить категорию',
        'add_new_item'      => 'Добавить новую категорию',
        'new_item_name'     => 'Название новой категории',
        'menu_name'         => 'Категории услуг',
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array(
            'slug'       => 'service-category',
            'with_front' => false,
        ),
        'show_in_rest'      => true,
    );

    register_taxonomy('service_category', array('service'), $args);
}
add_action('init', 'agency_register_taxonomy_service_category');

/**
 * Добавляем заранее заготовленные категории услуг при активации темы
 */
function agency_create_default_service_categories() {
    $categories = array(
        'parsing'              => 'Парсинг и лидогенерация',
        'crm'                  => 'Внедрение CRM',
        'chat-boty'            => 'Чат-боты',
        'ai'                   => 'AI-агенты',
        'sajty'                => 'Разработка сайтов',
        'full-cycle'           => 'Комплексный маркетинг',
        'seo'                  => 'SEO-продвижение',
        'ppc'                  => 'Контекстная реклама',
        'smm'                  => 'SMM и таргет',
        'kontent'              => 'Контент-маркетинг',
        'analitika'            => 'Веб-аналитика',
        'avtomatizaciya'       => 'Автоматизация маркетинга',
        'brending'             => 'Брендинг и фирменный стиль',
    );

    foreach ($categories as $slug => $name) {
        if (!term_exists($slug, 'service_category')) {
            wp_insert_term($name, 'service_category', array(
                'slug' => $slug,
            ));
        }
    }
}
add_action('after_switch_theme', 'agency_create_default_service_categories');
