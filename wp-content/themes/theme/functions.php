<?php

require_once "inc/options.php"; // дополнительные поля на странице настроек
require_once "inc/meta-box.php"; // конструктор мета боксов
require_once "inc/clearfy.php"; // оптимизатор сайта
require_once "inc/functions.php"; // вспомогательные функции
require_once "inc/seo.php"; // свои seo поля
if(0) require_once "inc/tpl.php"; // глобальные шаблоны
require_once "inc/BEM_Walker_Nav_Menu.php"; // кастомный конструктор меню
require_once "inc/sanitize-title.php"; // транслитератор слагов и именфайлов
require_once "inc/duplicate-pages.php"; // дублирование страниц
require_once "inc/styles-scripts-fonts.php"; // стили скрипты шрифты
require_once "inc/breadcrumbs.php"; // стили скрипты шрифты

                
new Clearfy_Sanitize;
Kama_SEO_Tags::init();
class_exists( 'Kama_Post_Meta_Box' ) && new Kama_Post_Meta_Box( [
	'id'     => '_seo',
	'title'  => 'SEO поля',
	'theme'  => 'grid',
	'fields' => [
		'title'       => [
			'type'  => 'text',
			'title' => 'Title',
			'desc_after'  => 'Заголовок страницы (рекомендуется 70 символов)',
			'attr'  => 'style="width:99%;"',
		],
		'description' => [
			'type'  => 'textarea',
			'title' => 'Description',
			'desc_before'  => 'Описание страницы (рекомендуется 160 символов)',
			'attr'  => 'style="width:99%;"',
		],
		'keywords'    => [
			'type'  => 'text',
			'title' => 'Keywords',
			'desc'  => 'Ключевые слова для записи',
			'attr'  => 'style="width:99%;"',
		],
		'robots'      => [
			'type'    => 'radio',
			'title'   => 'Robots',
			'options' => [ '' => 'index,follow', 'noindex,nofollow' => 'noindex,nofollow' ],
		],
		'h1' => [
			'type'  => 'textarea',
			'title' => 'H1',
			'desc_before'  => 'Альтернативный H1',
			'attr'  => 'style="width:99%;"',
		],
	],
] );


// объявляем меню
add_action( 'after_setup_theme', function(){
    register_nav_menu( 'nav-menu', 'Меню основное в шапке');
    register_nav_menu( 'footer-menu', 'Меню в подвале');    
});


// поддержка тега title
add_action( 'after_setup_theme', 'theme_functions' );
function theme_functions() {
    add_theme_support( 'title-tag' );
}


// Добавляем класс для простых страниц
function add_custom_class_based_on_template( $classes ) {
    // Проверяем, используется ли определённый шаблон страницы
    if ( is_page_template( 'simple-page.php' ) ) {
        $classes[] = 'other-page';
    }

    return $classes;
}
add_filter( 'body_class', 'add_custom_class_based_on_template' );


// Добавляем срок действия токена
add_filter('jwt_auth_expire', 'on_jwt_expire_token', 10,1);	
function on_jwt_expire_token($exp) {		
	$exp = time() + (DAY_IN_SECONDS * 365);			
	return $exp;
}


require_once "inc/cache.php"; // кеширование