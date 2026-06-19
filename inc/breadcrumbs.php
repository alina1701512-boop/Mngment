<?php
/**
 * Хлебные крошки (HTML-вывод)
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Вывести хлебные крошки
 */
function agency_breadcrumbs() {
    if (is_front_page()) {
        return;
    }

    $separator = '<span class="breadcrumbs__sep">/</span>';
    $home_text = 'Главная';
    $home_url  = home_url();

    echo '<nav class="breadcrumbs" aria-label="Хлебные крошки">';
    echo '<a href="' . esc_url($home_url) . '">' . $home_text . '</a>';

    if (is_singular('service')) {
        echo $separator;
        // Родительская категория (первая)
        $categories = get_the_terms(get_the_ID(), 'service_category');
        if ($categories && !is_wp_error($categories)) {
            $category = $categories[0];
            echo '<a href="' . esc_url(home_url('/services/')) . '">Услуги</a>';
            echo $separator;
        }
        echo '<span>' . get_the_title() . '</span>';

    } elseif (is_post_type_archive('case') || is_singular('case')) {
        echo $separator;
        echo '<a href="' . esc_url(home_url('/cases/')) . '">Кейсы</a>';
        if (is_singular('case')) {
            echo $separator;
            echo '<span>' . get_the_title() . '</span>';
        }

    } elseif (is_singular('post') || is_home()) {
        echo $separator;
        echo '<a href="' . esc_url(home_url('/blog/')) . '">Блог</a>';
        if (is_singular('post')) {
            echo $separator;
            // Категория статьи
            $categories = get_the_category();
            if ($categories) {
                echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . $categories[0]->name . '</a>';
                echo $separator;
            }
            echo '<span>' . get_the_title() . '</span>';
        }

    } elseif (is_page()) {
        echo $separator;
        echo '<span>' . get_the_title() . '</span>';

    } elseif (is_404()) {
        echo $separator;
        echo '<span>Страница не найдена</span>';
    }

    echo '</nav>';
}
