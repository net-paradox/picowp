<?php

function to_num($string) {
    // Используем регулярное выражение для удаления всех символов, кроме цифр
    return preg_replace('/[^0-9]/', '', $string);
}

function to_translit($value){
	$converter = array(
		'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
		'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
		'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
		'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
		'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
		'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
		'э' => 'e',    'ю' => 'yu',   'я' => 'ya',
	); 
	$value = mb_strtolower($value);
	$value = strtr($value, $converter);
	$value = mb_ereg_replace('[^-0-9a-z]', '-', $value);
	$value = mb_ereg_replace('[-]+', '-', $value);
	$value = trim($value, '-');	 
	return $value;
}

// вставка изображения секции
function isi($filename) {
    global $c_upload_url;
    return $c_upload_url . "/" . $filename;
}

// вставка изображения темы
function iti($filename) {
    return get_template_directory_uri() . "/assets/img/".$filename;
}

// вставка загруженных изображений
function ibi($filename) {
	$path = wp_get_upload_dir();
	return $path['baseurl'] . "/" . $filename; 	
}

// получить ip
function get_ip() {
	if ( function_exists( 'get_ip' ) ) {
		$ip = get_ip();
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}