<?php 

// Функция для проверки, нужно ли кешировать текущую страницу
function should_cache_page() {
    
    // Не кешируем для авторизованных пользователей и страниц с POST-запросами
    if (is_user_logged_in() || $_SERVER['REQUEST_METHOD'] === 'POST') {
        return false;
    }


    $excluded_urls = get_option('excluded_cache_urls', '');

    if (!empty($excluded_urls)) {
        $excluded_urls = explode("\n", $excluded_urls); // Преобразуем список URL в массив
        $excluded_urls = array_map('trim', $excluded_urls); // Очищаем от лишних пробелов

        // Проверяем, если текущий URL в списке исключенных
        $current_url = $_SERVER['REQUEST_URI'];
        foreach ($excluded_urls as $url) {
            if (strpos($current_url, $url) !== false) {
                return false;
            }
        }
    }

    // Добавьте сюда свои условия для исключения страниц (например, корзину)
    // if (is_cart() || is_checkout()) {
    //     return false;
    // }
    return true;
}


// Функция для создания папки cache, если ее нет
function ensure_cache_folder_exists() {
    $upload_dir = wp_upload_dir();
    $cache_dir = $upload_dir['basedir'] . '/cache';

    if (!file_exists($cache_dir)) {
        wp_mkdir_p($cache_dir);
    }

    return $cache_dir;
}

// Функция для подсчета количества файлов в кэше
function count_cache_files() {
    $cache_dir = ensure_cache_folder_exists();

    if (file_exists($cache_dir)) {
        $files = glob($cache_dir . '/*'); // Получаем все файлы в папке
        return count($files); // Возвращаем количество файлов
    } else {
        return 0; // Если папка не существует, возвращаем 0
    }
}


// Функция для удаления кэша конкретных страниц
function delete_cache_for_excluded_urls($excluded_urls) {
    $cache_dir = ensure_cache_folder_exists();

    foreach ($excluded_urls as $url) {
        $cache_file = $cache_dir . '/' . md5($url) . '.html';
        if (file_exists($cache_file)) {
            unlink($cache_file); // Удаляем файл кэша для исключенных URL
        }
    }
}

// Функция для очистки кэша
function clear_cache_files() {
    $cache_dir = ensure_cache_folder_exists();

    if (file_exists($cache_dir)) {
        $files = glob($cache_dir . '/*'); // Получаем все файлы в папке

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file); // Удаляем каждый файл
            }
        }
    }
}

// Очистка кеша при публикации нового поста или обновлении
add_action('save_post', 'clear_cache_files');

// Функция для кэширования страницы
function cache_page_output() {

    // global $timestart;
    // $time_end = microtime(true);
    // $execution_time = round(($time_end - $timestart) * 1000); 


    // Исключаем кэширование ресурсов
    $excluded_file_types = array('.ico', '.jpg', '.jpeg', '.png', '.gif', '.css', '.js', '.svg', '.woff', '.woff2', '.ttf', '.eot');
    $request_uri = $_SERVER['REQUEST_URI'];
    // Если запрос относится к ресурсу (например, .ico или .jpg), не кэшируем его
    foreach ($excluded_file_types as $file_type) {               
        if (strpos($request_uri, $file_type) !== false) {            
            return;
        }
    }


    if (!should_cache_page()) {
        return; // Если страница в списке исключений, не кэшируем её
    }


    $cache_enabled = get_option('cache_enabled');
    $cache_time = get_option('cache_time', 3600); // Время жизни кэша по умолчанию 1 час
    $cache_dir = ensure_cache_folder_exists();
   

    if ($cache_enabled) {
        $cache_file = $cache_dir . '/' . md5($request_uri) . '.html';

        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
            // Если файл кэша существует и не устарел, выводим его и завершаем выполнение
            readfile($cache_file);
            echo "\n<!-- Page served from cache -->";
            exit;
        } else {
            // Начинаем буферизацию вывода для кэширования страницы            
            ob_start();            
        }
    }
}

// Сохраняем страницу в кэш после завершения вывода
function cache_page_save() {

    // Исключаем кэширование ресурсов
    $excluded_file_types = array('.ico', '.jpg', '.jpeg', '.png', '.gif', '.css', '.js', '.svg', '.woff', '.woff2', '.ttf', '.eot');
    $request_uri = $_SERVER['REQUEST_URI'];
    // Если запрос относится к ресурсу (например, .ico или .jpg), не кэшируем его
    foreach ($excluded_file_types as $file_type) {               
        if (strpos($request_uri, $file_type) !== false) {            
            return;
        }
    }

    if (!should_cache_page()) {
        return; // Если страница в списке исключений, не кэшируем её
    }

    $cache_enabled = get_option('cache_enabled');
    $cache_dir = ensure_cache_folder_exists();
    
    if ($cache_enabled) {
        $cache_file = $cache_dir . '/' . md5($_SERVER['REQUEST_URI']) . '.html';
        // Сохраняем буферизированный вывод в файл кэша
        $cached_output  = ob_get_contents().'</body></html>';        
        file_put_contents($cache_file, $cached_output);         
        ob_end_flush();
    }
}

// Добавляем функции для кэширования при загрузке страницы
add_action('init', 'cache_page_output');
add_action('wp_footer', 'cache_page_save', 9999);


// Добавляем страницу настроек
function cache_settings_page() {
    add_options_page(
        'Настройки кэширования', // Заголовок страницы
        'Кэширование',           // Название пункта меню
        'manage_options',         // Уровень доступа
        'cache-settings',         // Слаг страницы
        'render_cache_settings_page' // Функция рендеринга страницы
    );
}
add_action('admin_menu', 'cache_settings_page');

// Функция для рендеринга страницы настроек
function render_cache_settings_page() {
    ?>
    <div class="wrap">
        <h1>Настройки кэширования</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('cache_settings_group'); // Название группы настроек
            do_settings_sections('cache-settings'); // Название страницы
            submit_button();
            ?>
        </form>
        
        <h2>Файлы в кэше</h2>
        <p>Количество файлов в кэше: <strong><?php echo count_cache_files(); ?></strong></p>

        <form method="post">
            <input type="hidden" name="clear_cache" value="1">
            <?php submit_button('Очистить кэш', 'secondary'); ?>
        </form>

        <?php
        // Если была отправлена форма для очистки кэша
        if (isset($_POST['clear_cache']) && $_POST['clear_cache'] == '1') {
            clear_cache_files();
            echo '<p><strong>Кэш успешно очищен!</strong></p>';
        }
        ?>
    </div>
    <?php
}

// Добавляем настройки
function cache_settings_init() {
    register_setting('cache_settings_group', 'cache_enabled');
    register_setting('cache_settings_group', 'cache_time');

    add_settings_section(
        'cache_settings_section', // Идентификатор секции
        'Основные настройки',     // Заголовок секции
        null,                     // Функция вывода перед настройками
        'cache-settings'          // Слаг страницы настроек
    );

    add_settings_field(
        'cache_enabled', 
        'Включить кэширование', 
        'render_cache_enabled_field', 
        'cache-settings', 
        'cache_settings_section'
    );

    add_settings_field(
        'cache_time', 
        'Время жизни кэша (в секундах)', 
        'render_cache_time_field', 
        'cache-settings', 
        'cache_settings_section'
    );

    add_settings_field(
        'excluded_cache_urls', 
        'URL страниц, которые не нужно кэшировать (один URL на строку)', 
        'render_excluded_urls_field', 
        'cache-settings', 
        'cache_settings_section'
    );
}
add_action('admin_init', 'cache_settings_init');

function render_cache_enabled_field() {
    $cache_enabled = get_option('cache_enabled');
    ?>
    <input type="checkbox" name="cache_enabled" value="1" <?php checked(1, $cache_enabled, true); ?> />
    Включить кэширование
    <?php
}

function render_cache_time_field() {
    $cache_time = get_option('cache_time', 3600);
    ?>
    <input type="number" name="cache_time" value="<?php echo esc_attr($cache_time); ?>" />
    <?php
}

// Функция для рендеринга текстового поля для исключенных URL
function render_excluded_urls_field() {
    $excluded_urls = get_option('excluded_cache_urls', '');
    ?>
    <textarea name="excluded_cache_urls" rows="5" cols="50"><?php echo esc_textarea($excluded_urls); ?></textarea>
    <p class="description">Введите URL страниц, которые не должны быть кэшированы. Один URL на строку.</p>
    <?php
}

// Добавляем время генерации страницы в футер
// function add_page_generation_time() {
//     global $timestart;
//     $time_end = microtime(true);
//     $execution_time = round(($time_end - $timestart) * 1000); 

//     echo "\n<!-- Page generated in {$execution_time} ms -->";
// }
// add_action('wp_footer', 'add_page_generation_time');
