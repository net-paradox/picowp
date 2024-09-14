<?php

// Регистрируем новые настройки
function add_custom_general_settings() {
    // Регистрируем опции для сохранения в базе данных
    register_setting('general', 'company_phone', array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));
    register_setting('general', 'company_hours', array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));
    register_setting('general', 'company_address', array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));

    // Добавляем поле "Телефон" на страницу общих настроек
    add_settings_field(
        'company_phone', // ID поля
        'Телефон компании', // Заголовок поля
        'display_company_phone_field', // Функция отображения
        'general' // Страница, где отобразится поле
    );

    // Добавляем поле "Время работы"
    add_settings_field(
        'company_hours',
        'Время работы компании',
        'display_company_hours_field',
        'general'
    );

    // Добавляем поле "Адрес"
    add_settings_field(
        'company_address',
        'Адрес компании',
        'display_company_address_field',
        'general'
    );
}
add_action('admin_init', 'add_custom_general_settings');

// Функция для отображения поля "Телефон"
function display_company_phone_field() {
    $value = get_option('company_phone', '');
    echo '<input type="text" id="company_phone" name="company_phone" value="' . esc_attr($value) . '" class="regular-text">';
}

// Функция для отображения поля "Время работы"
function display_company_hours_field() {
    $value = get_option('company_hours', '');
    echo '<input type="text" id="company_hours" name="company_hours" value="' . esc_attr($value) . '" class="regular-text">';
}

// Функция для отображения поля "Адрес"
function display_company_address_field() {
    $value = get_option('company_address', '');
    echo '<input type="text" id="company_address" name="company_address" value="' . esc_attr($value) . '" class="regular-text">';
}
