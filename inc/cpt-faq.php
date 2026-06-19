<?php
/**
 * Регистрация кастомного типа записи «FAQ»
 */

if (!defined('ABSPATH')) {
    exit;
}

function agency_register_cpt_faq() {
    $labels = array(
        'name'                  => 'FAQ (Вопросы-ответы)',
        'singular_name'         => 'Вопрос',
        'menu_name'             => 'FAQ',
        'add_new'               => 'Добавить вопрос',
        'add_new_item'          => 'Добавить новый вопрос',
        'edit_item'             => 'Редактировать вопрос',
        'view_item'             => 'Просмотреть вопрос',
        'all_items'             => 'Все вопросы',
        'search_items'          => 'Искать вопросы',
        'not_found'             => 'Вопросы не найдены',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => false, // Не выводим отдельные страницы FAQ
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => false,
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => 8,
        'menu_icon'           => 'dashicons-editor-help',
        'supports'            => array(
            'title',
            'editor',
            'custom-fields',
        ),
        'show_in_rest'        => true,
    );

    register_post_type('faq', $args);
}
add_action('init', 'agency_register_cpt_faq');

/**
 * Таксономия для привязки FAQ к услугам
 */
function agency_register_taxonomy_faq_category() {
    $labels = array(
        'name'          => 'Категории FAQ',
        'singular_name' => 'Категория FAQ',
        'menu_name'     => 'Категории FAQ',
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => false,
        'show_in_rest'      => true,
    );

    register_taxonomy('faq_category', array('faq'), $args);
}
add_action('init', 'agency_register_taxonomy_faq_category');

/**
 * Создаём категории FAQ (по услугам)
 */
function agency_create_default_faq_categories() {
    $faq_cats = array(
        'parsing'     => 'Парсинг',
        'crm'         => 'Внедрение CRM',
        'chat-boty'   => 'Чат-боты',
        'ai'          => 'AI-агенты',
        'sajty'       => 'Разработка сайтов',
        'seo'         => 'SEO',
        'ppc'         => 'Контекстная реклама',
        'smm'         => 'SMM',
        'brending'    => 'Брендинг',
    );

    foreach ($faq_cats as $slug => $name) {
        if (!term_exists($slug, 'faq_category')) {
            wp_insert_term($name, 'faq_category', array('slug' => $slug));
        }
    }
}
add_action('after_switch_theme', 'agency_create_default_faq_categories');
