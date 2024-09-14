<?php

// Добавляем preconnect для ускорения загрузки шрифтов
function add_google_fonts_preconnect() {    
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action('wp_head', 'add_google_fonts_preconnect', 1);


// подключение шрифтов
// function enqueue_fonts() {
// 	$font_name = "Manrope"; 
// 	$font_url = 'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&amp;display=swap';
// 	wp_enqueue_style(strtolower($font_name), $font_url, array(), null);
// }
// function enqueue_fonts() {
// 	global $_SITE;
// 	if(isset($_SITE['style'])) $file_fonts = $_SITE['style']['font'];
	
// 	if(!$file_fonts) {
// 		$font_name = "Manrope"; $font_url = 'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&amp;display=swap';
// 		wp_enqueue_style(strtolower($font_name), $font_url, array(), null);
// 	} else {
// 		foreach($file_fonts as $font) {
// 			if($font['location'] == "local") { 
// 				$p = get_template_directory_uri() . '/assets/fonts/' . strtolower($font['name']) . '.css';
// 				wp_enqueue_style($font['id'], $p, array(), null, 'all');				
// 			}
// 			else { wp_enqueue_style($font['id'], $font['url'], array(), null, 'all'); }
// 		}		
// 	}	
// }
// add_action('wp_enqueue_scripts', 'enqueue_fonts');


// подключение стилей
function enqueue_styles() {   
	// wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), null, 'all');
	// wp_enqueue_style('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css', array(), null, 'all');
	wp_enqueue_style('pico', get_template_directory_uri() . '/assets/css/pico.css', array(), null, 'all');	
	wp_enqueue_style('main', get_template_directory_uri() . '/assets/css/main.css', array('pico'), null, 'all');	
		
	// $custom_css = '';
    // Подключаем инлайн CSS к основному файлу стилей
    // wp_add_inline_style('main', $custom_css);	
	
}
add_action('wp_enqueue_scripts', 'enqueue_styles');


// подключение скриптов  
function enqueue_scripts() {
    if (!is_admin()) {
        wp_deregister_script('jquery');        
        wp_register_script('jquery', includes_url('/js/jquery/jquery.min.js'), false, null, true);
		wp_register_script('jquery-migrate', includes_url('/js/jquery/jquery-migrate.min.js'), false, array('jquery'), true);
        wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-migrate');		
		// wp_enqueue_script('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', array('jquery-migrate'), null, true);
		// wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@8.4.7/swiper-bundle.min.js', array('jquery-migrate'), null, true);
        // wp_enqueue_script('inputmask', get_template_directory_uri() . '/assets/js/inputmask.js', array('jquery-migrate'), null, true);
		wp_enqueue_script('scripts', get_template_directory_uri() . '/assets/js/scripts.js', array('jquery'), null, true);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_scripts');