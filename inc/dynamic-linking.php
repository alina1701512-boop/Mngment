<?php
/**
 * Динамическая перелинковка: сайдбар блога
 * Связывает теги статьи с услугами и инструментами
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Матрица связей: тег статьи → связанные услуги
 * Используется в сайдбаре блога
 */
function agency_get_related_services_matrix() {
    return array(
        'parsing'       => array('parsing', 'crm'),
        'crm'           => array('crm', 'avtomatizaciya'),
        'amocrm'        => array('crm', 'avtomatizaciya'),
        'chat-boty'     => array('chat-boty', 'ai'),
        'ai'            => array('ai', 'chat-boty'),
        'nejroseti'     => array('ai', 'avtomatizaciya'),
        'sajty'         => array('sajty', 'seo'),
        'lending'       => array('sajty', 'ppc'),
        'seo'           => array('seo', 'kontent'),
        'produizhenie'  => array('seo', 'analitika'),
        'direkt'        => array('ppc', 'analitika'),
        'reklama'       => array('ppc', 'smm'),
        'smm'           => array('smm', 'kontent'),
        'target'        => array('smm', 'ppc'),
        'kontent'       => array('kontent', 'seo'),
        'analitika'     => array('analitika', 'crm'),
        'avtomatizaciya'=> array('avtomatizaciya', 'crm'),
        'brending'      => array('brending', 'sajty'),
        'full-cycle'    => array('full-cycle', 'analitika'),
    );
}

/**
 * Получить связанные услуги для текущей статьи блога
 *
 * @return array Массив WP_Post объектов услуг
 */
function agency_get_related_services() {
    if (!is_singular('post')) {
        return array();
    }

    $tags = get_the_tags();
    if (!$tags) {
        return array();
    }

    $matrix = agency_get_related_services_matrix();
    $service_slugs = array();

    // Собираем slug'и услуг по тегам статьи
    foreach ($tags as $tag) {
        $tag_slug = $tag->slug;
        if (isset($matrix[$tag_slug])) {
            foreach ($matrix[$tag_slug] as $service_slug) {
                if (!in_array($service_slug, $service_slugs)) {
                    $service_slugs[] = $service_slug;
                }
            }
        }
    }

    if (empty($service_slugs)) {
        // Если тегов нет — показываем основные услуги
        $service_slugs = array('parsing', 'crm', 'ai', 'seo', 'sajty');
    }

    // Ограничиваем до 3 услуг
    $service_slugs = array_slice($service_slugs, 0, 3);

    // Получаем посты услуг по slug'ам
    $services = array();
    foreach ($service_slugs as $slug) {
        $term = get_term_by('slug', $slug, 'service_category');
        if ($term) {
            $posts = get_posts(array(
                'post_type'      => 'service',
                'posts_per_page' => 1,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'service_category',
                        'field'    => 'slug',
                        'terms'    => $slug,
                    ),
                ),
            ));
            if (!empty($posts)) {
                $services[] = $posts[0];
            }
        }
    }

    return $services;
}

/**
 * Получить инструменты для блока «Инструменты из статьи»
 *
 * @return array Массив названий инструментов и ссылок
 */
function agency_get_related_tools() {
    if (!is_singular('post')) {
        return array();
    }

    $content = get_the_content();
    $tools = array();

    // Проверяем упоминания инструментов в тексте
    $tool_keywords = array(
        'AmoCRM'     => '/services/vnedrenie-amocrm/',
        'amoCRM'     => '/services/vnedrenie-amocrm/',
        'Python'     => '/services/parsing-lidogeneraciya/',
        'ChatGPT'    => '/services/ai-agenty/',
        'OpenAI'     => '/services/ai-agenty/',
        'Яндекс.Директ' => '/services/kontekstnaya-reklama/',
        'Директ'     => '/services/kontekstnaya-reklama/',
        'Яндекс.Метрика' => '/services/veb-analitika/',
        'Метрика'    => '/services/veb-analitika/',
        'Telegram Bot' => '/services/chat-boty/',
        'WordPress'  => '/services/razrabotka-sajtov/',
        'Figma'      => '/services/brending-firmennyj-stil/',
    );

    foreach ($tool_keywords as $keyword => $url) {
        if (strpos($content, $keyword) !== false) {
            $tools[$keyword] = $url;
        }
    }

    return $tools;
}
