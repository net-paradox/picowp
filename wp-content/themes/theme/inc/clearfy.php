<?php

// Убираем комментарии из меню
function theme_menu()
{ 
   remove_menu_page('edit-comments.php'); // убираем комментарии из меню
}
add_action('admin_menu', 'theme_menu');


// Уалить малоиспользуемые размеры 
function remove_default_sizes( $sizes ) {
	unset( $sizes[ '1536x1536' ] );
	unset( $sizes[ '2048x2048' ] );
	return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'remove_default_sizes' );


// Добавить картинку превью для страниц и записей
function add_featured_image_support_to_your_wordpress_theme() {
	add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'add_featured_image_support_to_your_wordpress_theme' );


// Убираем пагинацию одиночных страниц
add_action('template_redirect', 'remove_single_pagination_duplicate');
function remove_single_pagination_duplicate()
{
    if (is_singular() && ! is_front_page()) {
        global $post, $page;

        // if woocommerce just return
        if (
            class_exists('woocommerce')
            && function_exists('is_cart') && function_exists('is_checkout') && function_exists('is_woocommerce') && function_exists('is_account_page')
            && (is_cart() || is_checkout() || is_woocommerce() || is_account_page())
        ) return;

        $num_pages = substr_count($post->post_content, '<!--nextpage-->') + 1;
        if ($page > $num_pages) {
            if (apply_filters('remove_single_pagination_duplicate_before_redirect', true)) :
                wp_redirect(get_permalink($post->ID));
                exit;
            endif;
        }
    }
}


// Удаляем ненужный функционал в комментариях
add_action('template_redirect', 'remove_replytocom_redirect');
add_filter('comment_reply_link', 'remove_replytocom_link');
function remove_replytocom_redirect()
{
    if (isset($_GET['replytocom']) && is_singular()) {
        $post_url = get_permalink($GLOBALS['post']->ID);
        $comment_id = sanitize_text_field($_GET['replytocom']);
        $query_string = remove_query_arg('replytocom', sanitize_text_field($_SERVER['QUERY_STRING']));

        if (! empty($query_string)) {
            $post_url .= '?' . $query_string;
        }
        $post_url .= '#comment-' . $comment_id;

        wp_redirect($post_url, 301);
        die();
    }

    return false;
}
function remove_replytocom_link($link)
{
    return preg_replace('`href=(["\'])(?:.*(?:\?|&|&#038;)replytocom=(\d+)#respond)`', 'href=$1#comment-$2', $link);
}


// Автоматический robots.txt 
add_filter( 'robots_txt', 'right_robots_txt');
function right_robots_txt( $output ) {

    
    $site_url = get_home_url();
    $site_url_clear = str_replace('http://', '', $site_url);
    $site_url_clear = str_replace('https://', '', $site_url_clear);

    if ( is_ssl() ) {
        $dir_host = 'https://' . $site_url_clear;
    } else {
        $dir_host = $site_url_clear;
    }

    $output  = 'User-agent: *' . PHP_EOL;
    //$output .= 'Disallow: /cgi-bin' . PHP_EOL;
    $output .= 'Disallow: /wp-admin' . PHP_EOL;
    $output .= 'Disallow: /wp-includes' . PHP_EOL;
    $output .= 'Disallow: /wp-content/plugins' . PHP_EOL;
    $output .= 'Disallow: /wp-content/cache' . PHP_EOL;
    //$output .= 'Disallow: /wp-content/themes' . PHP_EOL;
    $output .= 'Disallow: /wp-json/' . PHP_EOL;
    $output .= 'Disallow: /xmlrpc.php' . PHP_EOL;
    $output .= 'Disallow: /readme.html' . PHP_EOL;
    //$output .= 'Disallow: */trackback' . PHP_EOL;
    //$output .= 'Disallow: */feed' . PHP_EOL;
    //$output .= 'Disallow: */comments' . PHP_EOL;
    $output .= 'Disallow: /*?' . PHP_EOL;
    $output .= 'Disallow: /?s=' . PHP_EOL;
    $output .= 'Allow: /wp-includes/*.css' . PHP_EOL;
    $output .= 'Allow: /wp-includes/*.js' . PHP_EOL;
    $output .= 'Allow: /wp-content/plugins/*.css' . PHP_EOL;
    $output .= 'Allow: /wp-content/plugins/*.js' . PHP_EOL;
    $output .= 'Allow: /*.css' . PHP_EOL;
    $output .= 'Allow: /*.js' . PHP_EOL;

    /**
     * Check sitemaps
     */
    if ( function_exists( 'get_headers' ) ):
        $get_headers = @get_headers($site_url . '/wp-sitemap.xml', 1);

        // standart path
        if ( preg_match( '#200 OK#i', $get_headers[0] ) ) {
            $output .= 'Sitemap: ' . $site_url . '/wp-sitemap.xml' . PHP_EOL;

        // if redirect, like yoast example
        } else if ( isset($get_headers['Location']) && !empty($get_headers['Location']) ) {
            $output .= 'Sitemap: ' . $get_headers['Location'] . PHP_EOL;
        }
    endif;

    return $output;
}

remove_filter( 'wp_robots', 'wp_robots_max_image_preview_large' );

// Удаляет встроенную карту сайта
// add_filter( 'wp_sitemaps_enabled', '__return_false' );


// Удаляем пользователей из автоматической карты сайта
add_filter('wp_sitemaps_add_provider', function ($provider, $name) {
    return ($name == 'users') ? false : $provider;
}, 10, 2);


// Автоматическая простановка alt в контенте
add_filter('the_content', 'content_image_auto_alt');
function content_image_auto_alt($content)
{
    global $post;

    // if not singular - return
    if (! is_singular()) {
        return $content;
    }

    // if post_title doesnt exist - return
    if (! isset($post->post_title)) {
        return $content;
    }

    $pattern = array(' alt=""', ' alt=\'\'');
    $replacement = array(
        ' alt="' . esc_attr($post->post_title) . '"',
        ' alt=\'' . esc_attr($post->post_title) . '\''
    );
    $content = str_replace($pattern, $replacement, $content);

    return $content;
}


// Отключить ревизии
add_filter( 'wp_revisions_to_keep', 'clearfy_revisions_to_keep', 10, 2 );
function clearfy_revisions_to_keep( $num, $post ) {
	$num = 0;
	return $num;
}


// Убирает данные о версии и на чем сделан сайт
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );


// Перенаправляет страницу автора на главную
add_action( 'wp', 'protect_author_get' );
function protect_author_get() {
    if ( isset( $_GET['author'] ) && ! is_admin() ) {
        wp_redirect( home_url(), 301 );
        die();
    }
} 


// Отключить виджет
function remove_recent_comments_style() {
            global $wp_widget_factory;
            if ( ! empty( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
                remove_action( 'wp_head', array(
                    $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
                    'recent_comments_style'
                ) );
            }
}
add_action( 'widgets_init', 'remove_recent_comments_style' );


// Google Site-kit
//add_filter( 'googlesitekit_generator', '__return_empty_string' );
//remove_action( 'wp_head', 'wp_resource_hints', 2 );

// Удалить лишнее из head включая emojii
function remove_wp_api_link() {    
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
	remove_action('wp_head', 'wp_oembed_add_discovery_links');	
}
add_action('after_setup_theme', 'remove_wp_api_link');
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
remove_action( 'template_redirect', 'wp_shortlink_header', 11, 0 );
remove_action( 'wp_head', 'rest_output_link_wp_head');
remove_action( 'template_redirect', 'rest_output_link_header', 11);
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
function disable_emojis_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
}


// Отключает данные о фидах
function disable_feed_redirect() {
            // if GET param - remove and redirect
            if( isset( $_GET['feed'] ) ) {
                wp_redirect( esc_url_raw( remove_query_arg( 'feed' ) ), 301 );
                exit;
            }

            // if beauty permalink - remove and redirect
            if( get_query_var( 'feed' ) !== 'old' ) {
                set_query_var( 'feed', '' );
            }
            redirect_canonical();

            wp_redirect( get_option( 'siteurl' ), 301 );
            die();	
}
//Remove feed links from the <head> section
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );
//Redirect feed URLs to home page
add_action( 'do_feed', 'disable_feed_redirect', 1 );
add_action( 'do_feed_rdf', 'disable_feed_redirect', 1 );
add_action( 'do_feed_rss', 'disable_feed_redirect', 1 );
add_action( 'do_feed_rss2', 'disable_feed_redirect', 1 );
add_action( 'do_feed_atom', 'disable_feed_redirect', 1 );


// Отключить индексацию страниц пагинации
function noindex_pagination() {
    if ( is_paged() ) {
        echo '<meta name="robots" content="noindex,follow">' . PHP_EOL;
    }
}
function noindex_pagination_filter( $value ) {
    if ( is_paged() ) {
        return 'noindex,follow';
    }   
    return $value;
}
add_action( 'wp_head', 'noindex_pagination' , 1 );


// Отключить страницу аттача
function attachment_pages_redirect() {
	global $post;
    if ( is_attachment() ) {
                if ( isset($post->post_parent) && ($post->post_parent != 0) ) {
                    wp_redirect( get_permalink($post->post_parent), 301 );
                } else {
                    wp_redirect( home_url(), 301 );
                }
                exit;
    }
}
add_action( 'template_redirect', 'attachment_pages_redirect' );


// Отключить версию скриптов и стилей
function remove_versions_styles_scripts($src, $handle)
{

    if (is_admin()) return $src;

    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }

    return $src;
}
add_filter('script_loader_src', 'remove_versions_styles_scripts', 9999, 2);
add_filter('style_loader_src', 'remove_versions_styles_scripts', 9999, 2);


// Отключить Ping
function remove_x_pingback($headers)
{
    unset($headers['X-Pingback']);
    return $headers;
}
function remove_x_pingback_headers($headers)
{
    if (function_exists('header_remove')) {
        header_remove('X-Pingback');
        header_remove('Server');
    }
}
add_filter('xmlrpc_enabled', '__return_false');
add_filter('xmlrpc_methods', function ($methods) {
    return [];
});
add_filter('template_redirect', 'remove_x_pingback_headers');
add_filter('wp_headers', 'remove_x_pingback');


// Отключаем Gutenberg для всех типов постов
add_filter('use_block_editor_for_post', '__return_false', 10);
// Отключаем стили блоков Gutenberg на фронтенде и админке
function remove_gutenberg_styles() {
    wp_dequeue_style( 'wp-block-library' ); // Отключаем базовые стили блоков
    wp_dequeue_style( 'wp-block-library-theme' ); // Отключаем дополнительные стили для темы
    wp_dequeue_style( 'wc-block-style' ); // Отключаем стили блоков WooCommerce (если используется)
}
add_action( 'wp_enqueue_scripts', 'remove_gutenberg_styles', 100 );
// Полностью удаляем стили Gutenberg на фронтенде
function deregister_gutenberg_styles() {
    wp_deregister_style( 'wp-block-library' ); // Удаляем стили блоков Gutenberg
    wp_deregister_style( 'wp-block-library-theme' ); // Удаляем дополнительные стили блоков для темы
    wp_deregister_style( 'wc-block-style' ); // Удаляем стили блоков WooCommerce
}
add_action( 'wp_enqueue_scripts', 'deregister_gutenberg_styles', 100 );
// Отключаем стили Gutenberg в виджетах
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
add_filter( 'use_widgets_block_editor', '__return_false' );
add_filter('gutenberg_can_edit_post_type', '__return_false', 100);
add_filter('use_block_editor_for_post_type', '__return_false', 100);
// Move the Privacy Policy help notice back under the title field.
add_action('admin_init', function () {
    remove_action('admin_notices', array('WP_Privacy_Policy_Content', 'notice'));
    add_action('edit_form_after_title', array('WP_Privacy_Policy_Content', 'notice'));
});
remove_action('admin_menu', 'gutenberg_menu');
remove_action('admin_init', 'gutenberg_redirect_demo');
remove_filter('wp_refresh_nonces', 'gutenberg_add_rest_nonce_to_heartbeat_response_headers');
remove_filter('get_edit_post_link', 'gutenberg_revisions_link_to_editor');
remove_filter('wp_prepare_revision_for_js', 'gutenberg_revisions_restore');
remove_action('rest_api_init', 'gutenberg_register_rest_routes');
remove_action('rest_api_init', 'gutenberg_add_taxonomy_visibility_field');
remove_filter('rest_request_after_callbacks', 'gutenberg_filter_oembed_result');
remove_filter('registered_post_type', 'gutenberg_register_post_prepare_functions');
remove_action('do_meta_boxes', 'gutenberg_meta_box_save', 1000);
remove_action('submitpost_box', 'gutenberg_intercept_meta_box_render');
remove_action('submitpage_box', 'gutenberg_intercept_meta_box_render');
remove_action('edit_page_form', 'gutenberg_intercept_meta_box_render');
remove_action('edit_form_advanced', 'gutenberg_intercept_meta_box_render');
remove_filter('redirect_post_location', 'gutenberg_meta_box_save_redirect');
remove_filter('filter_gutenberg_meta_boxes', 'gutenberg_filter_meta_boxes');
remove_action('admin_notices', 'gutenberg_build_files_notice');
remove_filter('body_class', 'gutenberg_add_responsive_body_class');
remove_filter('admin_url', 'gutenberg_modify_add_new_button_url'); // old
remove_action('admin_enqueue_scripts', 'gutenberg_check_if_classic_needs_warning_about_blocks');
remove_filter('register_post_type_args', 'gutenberg_filter_post_type_labels');

add_action( 'after_setup_theme', function() {
    remove_theme_support( 'core-block-patterns' );
} );
add_filter( 'should_load_remote_block_patterns', '__return_false' );

add_action('admin_init', 'remove_wp_block_menu', 900);
function remove_wp_block_menu() {
    remove_submenu_page( 'themes.php', 'site-editor.php?path=/patterns');
}


// Отключить ненужные пункты в админбаре
function remove_unnecessary_link_admin_bar()
{
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('about');
    $wp_admin_bar->remove_menu('wporg');
    $wp_admin_bar->remove_menu('documentation');
    $wp_admin_bar->remove_menu('support-forums');
    $wp_admin_bar->remove_menu('feedback');
    $wp_admin_bar->remove_menu('view-site');
}
add_action('wp_before_admin_bar_render', 'remove_unnecessary_link_admin_bar');


// Убрать нотификацию
add_filter('auto_core_update_send_email', '__return_false');
add_filter('auto_plugin_update_send_email', '__return_false');
add_filter('auto_theme_update_send_email', '__return_false');


// Удалить ненужные виджеты
function remove_unneeded_widgets()
{
    unregister_widget('WP_Widget_Pages');

    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_Tag_Cloud');
}
add_action('widgets_init', 'remove_unneeded_widgets');


// Удалить ненужные архивы
function redirect_archives()
{
    if (is_author() && ! is_admin()) {
        wp_redirect(home_url(), 301);
        die();
    }
    if (is_date() && ! is_admin()) {
        wp_redirect(home_url(), 301);
        die();
    }
    if (is_tag() && ! is_admin()) {
        wp_redirect(home_url(), 301);
        die();
    }
}
add_action('wp', 'redirect_archives');


// Удалить неиспользуемы блоки инлайн стилей
add_action( 'wp_enqueue_scripts', 'remove_global_styles' );
function remove_global_styles(){
    wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style('core-block-supports');
}
add_action( 'wp_footer', function() {
	wp_dequeue_style('core-block-supports');
});


# Добавляет SVG в список разрешенных для загрузки файлов
function svg_upload_allow($mimes)
{
    $mimes["svg"] = "image/svg+xml";
    return $mimes;
}
add_filter("upload_mimes", "svg_upload_allow");


# Исправление MIME типа для SVG файлов
function fix_svg_mime_type($data, $file, $filename, $mimes, $real_mime = "")
{
    // WP 5.1 +
    if (version_compare($GLOBALS["wp_version"], "5.1.0", ">=")) {
        $dosvg = in_array($real_mime, ["image/svg", "image/svg+xml"]);
    } else {
        $dosvg = ".svg" === strtolower(substr($filename, -4));
    }
    if ($dosvg) {
        // разрешим
        if (current_user_can("manage_options")) {
            $data["ext"] = "svg";
            $data["type"] = "image/svg+xml";
        }
        // запретим
        else {
            $data["ext"] = $type_and_ext["type"] = false;
        }
    }	
    return $data;
}
add_filter("wp_check_filetype_and_ext", "fix_svg_mime_type", 10, 5);


// Исправляет некоторые ошибки при загрузке
function hs_image_editor_default_to_gd($editors)
{
    $gd_editor = "WP_Image_Editor_GD";
    $editors = array_diff($editors, [$gd_editor]);
    array_unshift($editors, $gd_editor);
    return $editors;
}
add_filter("wp_image_editors", "hs_image_editor_default_to_gd");

