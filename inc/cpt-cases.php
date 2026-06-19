<?php
/**
 * Регистрация кастомного типа записи «Кейсы» (Cases)
 */

if (!defined('ABSPATH')) {
    exit;
}

function agency_register_cpt_cases() {
    $labels = array(
        'name'                  => 'Кейсы',
        'singular_name'         => 'Кейс',
        'menu_name'             => 'Кейсы',
        'add_new'               => 'Добавить кейс',
        'add_new_item'          => 'Добавить новый кейс',
        'edit_item'             => 'Редактировать кейс',
        'view_item'             => 'Просмотреть кейс',
        'all_items'             => 'Все кейсы',
        'search_items'          => 'Искать кейсы',
        'not_found'             => 'Кейсы не найдены',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array(
            'slug'       => 'cases',
            'with_front' => false,
        ),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 6,
        'menu_icon'           => 'dashicons-portfolio',
        'supports'            => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'custom-fields',
        ),
        'show_in_rest'        => true,
    );

    register_post_type('case', $args);
}
add_action('init', 'agency_register_cpt_cases');

/**
 * Регистрация таксономии «Отрасль кейса»
 */
function agency_register_taxonomy_case_industry() {
    $labels = array(
        'name'          => 'Отрасли',
        'singular_name' => 'Отрасль',
        'menu_name'     => 'Отрасли',
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'case-industry'),
        'show_in_rest'      => true,
    );

    register_taxonomy('case_industry', array('case'), $args);
}
add_action('init', 'agency_register_taxonomy_case_industry');

/**
 * Добавляем отрасли при активации темы
 */
function agency_create_default_case_industries() {
    $industries = array(
        'b2b'           => 'B2B',
        'b2c'           => 'B2C',
        'edtech'        => 'EdTech',
        'proizvodstvo'  => 'Производство',
        'stroitelstvo'  => 'Строительство',
        'logistika'     => 'Логистика',
        'retail'        => 'Ритейл',
        'medicina'      => 'Медицина',
        'it'            => 'IT',
    );

    foreach ($industries as $slug => $name) {
        if (!term_exists($slug, 'case_industry')) {
            wp_insert_term($name, 'case_industry', array('slug' => $slug));
        }
    }
}
add_action('after_switch_theme', 'agency_create_default_case_industries');
