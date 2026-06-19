<?php
/**
 * Обработчик Webhook: отправка лидов из форм сайта в AmoCRM
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Регистрируем AJAX-обработчики
 */
function agency_register_webhook_handlers() {
    add_action('wp_ajax_agency_send_lead', 'agency_send_lead_to_crm');
    add_action('wp_ajax_nopriv_agency_send_lead', 'agency_send_lead_to_crm');
}
add_action('init', 'agency_register_webhook_handlers');

/**
 * Основная функция отправки лида в AmoCRM
 */
function agency_send_lead_to_crm() {
    // Проверяем nonce для безопасности
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'agency_lead_nonce')) {
        wp_send_json_error('Ошибка безопасности. Обновите страницу.');
    }

    // Собираем данные из формы
    $data = array(
        'name'       => sanitize_text_field($_POST['name'] ?? ''),
        'phone'      => sanitize_text_field($_POST['phone'] ?? ''),
        'email'      => sanitize_email($_POST['email'] ?? ''),
        'service'    => sanitize_text_field($_POST['service'] ?? 'Не указана'),
        'industry'   => sanitize_text_field($_POST['industry'] ?? ''),
        'message'    => sanitize_textarea_field($_POST['message'] ?? ''),
        'budget'     => sanitize_text_field($_POST['budget'] ?? ''),
        'page_url'   => esc_url_raw($_POST['page_url'] ?? ''),
        'page_title' => sanitize_text_field($_POST['page_title'] ?? ''),
        'utm_source' => sanitize_text_field($_POST['utm_source'] ?? ''),
        'utm_medium' => sanitize_text_field($_POST['utm_medium'] ?? ''),
        'utm_campaign' => sanitize_text_field($_POST['utm_campaign'] ?? ''),
        'utm_term'   => sanitize_text_field($_POST['utm_term'] ?? ''),
        'utm_content' => sanitize_text_field($_POST['utm_content'] ?? ''),
        'calculator_result' => sanitize_text_field($_POST['calculator_result'] ?? ''),
    );

    // Проверяем обязательные поля
    if (empty($data['name']) || empty($data['phone'])) {
        wp_send_json_error('Пожалуйста, заполните имя и телефон.');
    }

    // === НАСТРОЙКИ AMOCRM (ЗАМЕНИТЕ НА РЕАЛЬНЫЕ) ===
    $amo_subdomain = 'yourcompany'; // Ваш поддомен: yourcompany.amocrm.ru
    $amo_webhook_url = 'https://hooks.amocrm.ru/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
    // ИЛИ используйте готовый Webhook от AmoCRM (раздел «Интеграции» → «Webhooks»)
    // =============================================

    // Формируем тело запроса для AmoCRM
    $lead_data = array(
        'leads' => array(
            array(
                'name'   => 'Заявка с сайта: ' . $data['service'],
                'price'  => 0,
                'status_id' => 34567890, // ID первого этапа воронки. ЗАМЕНИТЕ!
                'pipeline_id' => 1234567, // ID воронки. ЗАМЕНИТЕ!
                'custom_fields_values' => array(
                    array(
                        'field_id' => 123456, // Поле «Телефон». ЗАМЕНИТЕ ID!
                        'values'   => array(
                            array('value' => $data['phone']),
                        ),
                    ),
                    array(
                        'field_id' => 123457, // Поле «Email». ЗАМЕНИТЕ ID!
                        'values'   => array(
                            array('value' => $data['email']),
                        ),
                    ),
                    array(
                        'field_id' => 123458, // Поле «Услуга». ЗАМЕНИТЕ ID!
                        'values'   => array(
                            array('value' => $data['service']),
                        ),
                    ),
                    array(
                        'field_id' => 123459, // Поле «Сфера бизнеса». ЗАМЕНИТЕ ID!
                        'values'   => array(
                            array('value' => $data['industry']),
                        ),
                    ),
                ),
                '_embedded' => array(
                    'tags' => array(
                        array('name' => 'сайт'),
                        array('name' => $data['service']),
                    ),
                    'notes' => array(
                        array(
                            'note_type' => 'common',
                            'params'    => array(
                                'text' => agency_build_lead_note($data),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    );

    // Отправляем запрос в AmoCRM
    $response = wp_remote_post($amo_webhook_url, array(
        'method'      => 'POST',
        'timeout'     => 15,
        'redirection' => 5,
        'httpversion' => '1.1',
        'blocking'    => true,
        'headers'     => array(
            'Content-Type' => 'application/json',
        ),
        'body'        => wp_json_encode($lead_data),
    ));

    // Логируем результат (опционально, для отладки)
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('AmoCRM Webhook Response: ' . print_r($response, true));
    }

    // Отправляем ответ клиенту
    if (is_wp_error($response)) {
        // Если ошибка — всё равно говорим «успех», чтобы не терять лида
        // Но логируем ошибку
        error_log('AmoCRM Webhook Error: ' . $response->get_error_message());
        wp_send_json_success('Заявка получена. Мы свяжемся с вами в ближайшее время.');
    }

    $http_code = wp_remote_retrieve_response_code($response);
    if ($http_code >= 200 && $http_code < 300) {
        wp_send_json_success('Заявка успешно отправлена!');
    } else {
        wp_send_json_success('Заявка получена. Мы свяжемся с вами в ближайшее время.');
    }
}

/**
 * Формирует текст примечания к сделке
 */
function agency_build_lead_note($data) {
    $note = "=== НОВАЯ ЗАЯВКА С САЙТА ===\n\n";
    $note .= "Имя: {$data['name']}\n";
    $note .= "Телефон: {$data['phone']}\n";
    if (!empty($data['email'])) {
        $note .= "Email: {$data['email']}\n";
    }
    $note .= "Услуга: {$data['service']}\n";
    if (!empty($data['industry'])) {
        $note .= "Сфера бизнеса: {$data['industry']}\n";
    }
    if (!empty($data['budget'])) {
        $note .= "Бюджет: {$data['budget']}\n";
    }
    if (!empty($data['message'])) {
        $note .= "Сообщение: {$data['message']}\n";
    }
    if (!empty($data['calculator_result'])) {
        $note .= "Результат калькулятора: {$data['calculator_result']}\n";
    }
    $note .= "\n--- Источник ---\n";
    $note .= "Страница: {$data['page_url']}\n";
    $note .= "Заголовок: {$data['page_title']}\n";

    $utm_string = '';
    if (!empty($data['utm_source'])) $utm_string .= "utm_source={$data['utm_source']}&";
    if (!empty($data['utm_medium'])) $utm_string .= "utm_medium={$data['utm_medium']}&";
    if (!empty($data['utm_campaign'])) $utm_string .= "utm_campaign={$data['utm_campaign']}&";
    if (!empty($data['utm_term'])) $utm_string .= "utm_term={$data['utm_term']}&";
    if (!empty($data['utm_content'])) $utm_string .= "utm_content={$data['utm_content']}";

    if (!empty($utm_string)) {
        $note .= "UTM-метки: " . rtrim($utm_string, '&') . "\n";
    }

    $note .= "\nДата: " . current_time('d.m.Y H:i') . "\n";

    return $note;
}
