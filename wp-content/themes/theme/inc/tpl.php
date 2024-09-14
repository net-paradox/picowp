<?php

// Создание функционала шаблонов глобальных секций

add_action( 'init', function() {
	register_post_type( 'tpl', array(
	'labels' => array(
		'name' => 'Шаблоны',
		'singular_name' => 'Шаблон',
		'menu_name' => 'Шаблоны',
		'all_items' => 'Все Шаблоны',
		'edit_item' => 'Изменить Шаблон',
		'view_item' => 'Посмотреть Шаблон',
		'view_items' => 'Посмотреть Шаблоны',
		'add_new_item' => 'Добавить новое Шаблон',
		'add_new' => 'Добавить новое Шаблон',
		'new_item' => 'Новый Шаблон',
		'parent_item_colon' => 'Родитель Шаблон:',
		'search_items' => 'Поиск Шаблоны',
		'not_found' => 'Не найдено шаблоны',
		'not_found_in_trash' => 'В корзине не найдено шаблоны',
		'archives' => 'Архивы Шаблон',
		'attributes' => 'Атрибуты Шаблон',
		'insert_into_item' => 'Вставить в шаблон',
		'uploaded_to_this_item' => 'Загружено в это шаблон',
		'filter_items_list' => 'Фильтровать список шаблоны',
		'filter_by_date' => 'Фильтр шаблоны по дате',
		'items_list_navigation' => 'Шаблоны навигация по списку',
		'items_list' => 'Шаблоны список',
		'item_published' => 'Шаблон опубликовано.',
		'item_published_privately' => 'Шаблон опубликована приватно.',
		'item_reverted_to_draft' => 'Шаблон преобразован в черновик.',
		'item_scheduled' => 'Шаблон запланировано.',
		'item_updated' => 'Шаблон обновлён.',
		'item_link' => 'Cсылка на Шаблон',
		'item_link_description' => 'Ссылка на шаблон.',
	),
	'public' => true,
	'show_in_rest' => false,
	'menu_icon' => 'dashicons-admin-post',
	'supports' => array(
		0 => 'title',
		1 => 'custom-fields',
	),
	'delete_with_user' => false,
) );
} );



// Функция для вывода данных из Flexible Content для произвольного типа записи через шорткод
function display_acf_flexible_section( $atts ) {
	
    // Получаем ID записи из атрибутов шорткода
    $atts = shortcode_atts( array(
        'post_id' => get_the_ID(), // Если не передан ID, используется текущий пост
    ), $atts, 'acf_flexible' );

    $post_id = $atts['post_id'];

    
    // Проверяем, есть ли данные в Flexible Content для указанной записи
    if ( have_rows( 'sections', $post_id ) ) {
        ob_start();
        // Проходим по каждому макету Flexible Content
        while ( have_rows( 'sections', $post_id ) ) {
            the_row(); $section_name = get_row_layout();
            include "sections/". $section_name . "/". $section_name . "_acf.php";             
        }
		return ob_get_clean();
    } else {
        return ''; // Если нет данных в ACF
    }
}

add_shortcode( 'acf_section', 'display_acf_flexible_section' );


function add_acf_flexible_shortcode_column( $columns ) {
    // Добавляем новую колонку для шорткода
    $columns['acf_flexible_shortcode'] = 'Шорткод ACF секции';
    return $columns;
}
add_filter( 'manage_tpl_posts_columns', 'add_acf_flexible_shortcode_column' );

// Выводим шорткод в новой колонке для каждой записи
function display_acf_flexible_shortcode_column( $column, $post_id ) {
    if ( 'acf_flexible_shortcode' === $column ) {
        // Выводим шорткод с ID записи
        echo '[acf_section post_id="' . $post_id . '"]';
    }
}
add_action( 'manage_tpl_posts_custom_column', 'display_acf_flexible_shortcode_column', 10, 2 );