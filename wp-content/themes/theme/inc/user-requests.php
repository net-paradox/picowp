<?php 
if (wp_doing_ajax()){
    add_action('wp_ajax_request', 'site_request');
    add_action('wp_ajax_nopriv_request', 'site_request');
    add_action('wp_ajax_quiz', 'site_quiz');
    add_action('wp_ajax_nopriv_quiz', 'site_quiz');
}

function site_request(){
    $msg = 'Заявка с сайта <br>';
    if ($_POST['phone']){
        $msg .= '<br>
        Телефон : ' . $_POST['phone'];
    }
	if ($_POST['form']){
        $msg .= '<br>
        Форма : ' . $_POST['form'];
    }
	if ($_POST['promotion_name']){
        $msg .= '<br>
        Акция : ' . $_POST['promotion_name'];
    }
	if ($_POST['service_name']){
        $msg .= '<br>
        Услуга : ' . $_POST['service_name'];
    }
	if ($_POST['product_name']){
        $msg .= '<br>
        Товар : ' . $_POST['product_name'];
    }
	if ($_POST['url']){
        $msg .= '<br>
        url : ' . $_POST['url'];
    }
	if ($_POST['call_back_time']){
        $msg .= '<br>
        В какое время позвонить : ' . $_POST['call_back_time'];
    }
    if ($_POST['social']){
        $msg .= '<br>
        Способ связи : ' . $_POST['social'];
    }
	if ($_POST['social_type']){
        $msg .= '<br>
        Способ связи : ' . $_POST['social_type'];
    }
    if ($_POST['message']){
        $msg .= '<br>
        Сообщение : ' . $_POST['message'];
    }
	if ($_POST['service_type']){
        $msg .= '<br>
        Услуга : ' . $_POST['service_type'];
    }
    if ($_POST['name']){
        $msg .= '<br>
        Имя : ' . $_POST['name'];
    }  
    if ($_POST['email']){
        $msg .= '<br>
        Email : ' . $_POST['email'];
    }
    if ($_POST['city']){
        $msg .= '<br>
        Город : ' . $_POST['city'];
    }
	if ($_POST['ymcid']){
        $msg .= '<br>
        Яндекс Метрика ClientID : ' . $_POST['ymcid'];
    }
	
	
	$files_path = array();
    if ($_FILES['resume_file']){
		$uploadedfile       = $_FILES['resume_file'];
        $upload_overrides   = array( 'test_form' => false );
        $movefile           = wp_handle_upload( $uploadedfile, $upload_overrides );
        if( $movefile ) {
            $files_path = $movefile[ 'file' ];
        }
    }

    if ($_FILES['profile_img']){
        $uploadedfile       = $_FILES['profile_img'];
        $upload_overrides   = array( 'test_form' => false );
        $movefile           = wp_handle_upload( $uploadedfile, $upload_overrides );
        if( $movefile ) {
            $files_path = $movefile[ 'file' ];
        }
    }
	if ($_FILES['project_img']){
        $uploadedfile       = $_FILES['project_img'];
        $upload_overrides   = array( 'test_form' => false );
        $movefile           = wp_handle_upload( $uploadedfile, $upload_overrides );
        if( $movefile ) {
            $files_path = $movefile[ 'file' ];
        }
    }
	 $msg_rep = str_replace('<br>', '', $msg);
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/html; charset=utf-8' . "\r\n";
    wp_mail(get_field('admin_email', 'options'), 'Заявка с сайта', $msg, $headers, $files_path);
	if (get_field('admin_email2', 'options')){
        wp_mail(get_field('admin_email2', 'options'), 'Заявка с сайта', $msg, $headers, $files_path);
    }
	if (get_field('admin_amo', 'options')){
        $date = date('m/d/Y h:i:s ', time());
        wp_mail(get_field('admin_amo', 'options'), 'Заявка с квиз-сайта ' . $date, $msg);
    }
	if (get_field('tg_token', 'options') and get_field('tg_chat', 'options')){
        $ch = curl_init();
        curl_setopt_array(
                $ch,
                array(
                    CURLOPT_URL => 'https://api.telegram.org/bot' . get_field('tg_token', 'options') . '/sendMessage',
                    CURLOPT_POST => TRUE,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_POSTFIELDS => array(
                        'chat_id' => get_field('tg_chat', 'options'),
                        'text' => $msg_rep,
                        'parse_mode' => 'HTML',
                    ),
                )
            );
        curl_exec($ch);
        if ($_FILES){
            foreach ($_FILES as $pFile){
                if (!empty($pFile['tmp_name'])) 
                { 
                  $path = basename($pFile['name']); 
                  if (copy($pFile['tmp_name'], $path)) $file = $path; 
                } 
                if(!empty($file)) {
                    $url = "https://api.telegram.org/bot" . get_field('tg_token', 'options') . "/sendDocument";
                            // $_document = "ok.json";
                    $_document = $pFile['tmp_name'];
                    $document = new CURLFile(realpath($file));
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, ["chat_id" => get_field('tg_chat', 'options'), "document" => $document]);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    $out = curl_exec($ch);
                    curl_close($ch);
                    // print_r($out);
                }
                unlink($file);
            }
        }
    }
	
	if (get_field('b24_link', 'options') and get_field('b24_login', 'options') and get_field('b24_pass', 'options')){
		define('CRM_HOST', get_field('b24_link', 'options'));
		define('CRM_PORT', '443'); 
		define('CRM_PATH', '/crm/configs/import/lead.php');
		define('CRM_LOGIN', get_field('b24_login', 'options')); 
		define('SOURCE_ID', '1'); 
		define('CRM_PASSWORD', get_field('b24_pass', 'options')); 
		
		$postData = array(
			'TITLE' => $_POST['source_id'],
			'NAME' => $_POST['name'],
			'EMAIL_WORK' => $_POST['mail'],
			'PHONE_WORK' => $_POST['phone'],
			'SOURCE_ID' => $_POST['source_id'],		
			'COMMENTS' => $msg
		);
		$postData['LOGIN'] = CRM_LOGIN;
		$postData['PASSWORD'] = CRM_PASSWORD;
		$fp = fsockopen("ssl://".CRM_HOST, CRM_PORT, $errno, $errstr, 30);
	if ($fp)
	{
		// prepare POST data
		$strPostData = '';
		foreach ($postData as $key => $value)
			$strPostData .= ($strPostData == '' ? '' : '&').$key.'='.urlencode($value);

		// prepare POST headers
		$str = "POST ".CRM_PATH." HTTP/1.0\r\n";
		$str .= "Host: ".CRM_HOST."\r\n";
		$str .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$str .= "Content-Length: ".strlen($strPostData)."\r\n";
		$str .= "Connection: close\r\n\r\n";
		$str .= $strPostData;
		// send POST to CRM
		fwrite($fp, $str);
		// get CRM headers
		$result = '';
		while (!feof($fp))
		{
			$result .= fgets($fp, 128);
		}
		fclose($fp);
	}
	}
    
    $roistatData = array(
        'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : 'nocookie',
        'key'     => get_field('roistat_key', 'options'), 
        'title'   => 'Заявка с сайта - Квиз', 
        'comment' => $msg, 
        'name'    => $name,
        'email'   => $email, 
        'phone'   => $phone, 
        'order_creation_method' => '', 
        'is_need_callback' => '0', 
        'sync'    => '0', 
        'is_need_check_order_in_processing' => '1', 
        'is_need_check_order_in_processing_append' => '1', 
        'is_skip_sending' => '1', 
        'fields'  => array(
         "charset" => "Windows-1251", 
        ),
    );
    
    file_get_contents("https://cloud.roistat.com/api/proxy/1.0/leads/add?" . http_build_query($roistatData));
	
    wp_die();
}


function site_quiz(){
    $phone = $_POST['phone'];
    $messenger = $_POST['social'];
    $msg = 'Заявка с сайта - Квиз';
    $total = $_POST['total'];
    $files_path = array();
	for ($i=0; $i <= $total; $i++) {
		
        $quizAnsw = $_POST['quetion_' . $i];
        if (is_array($quizAnsw)) {
            $msg .= '<br>' . $_POST['title_' . $i] . ' ';
            foreach($quizAnsw as $check) {
                $msg .= $check . ', ';
            }
        } else {
            $msg .= '<br>
        ' . $_POST['title_' . $i] .  ' : ' . $quizAnsw;
        }
		if($_POST['question-inputs__' . $i]){
			for ($j=1; $j <= $_POST['question-inputs__' . $i]; $j++) { 
				$msg .= $_POST['question_' . $i . '-' . $j] . ', ';
			}
		}
        if ($_FILES['file_' . $i]){
            $uploadedfile       = $_FILES['file_' . $i];
            $upload_overrides   = array( 'test_form' => false );
            $movefile           = wp_handle_upload( $uploadedfile, $upload_overrides );
            if( $movefile ) {
                $files_path = $movefile[ 'file' ];
            }
        }
    }
    $msg .= '<br>
    Телефон : ' . $phone . '<br>  
    Способ связи : ' . $messenger; 
	if ($_POST['url']){
        $msg .= '<br>
        url : ' . $_POST['url'];
    }
	if ($_POST['name']){
        $msg .= '<br>
        Имя : ' . $_POST['name'];
    }
	if ($_POST['email']){
        $msg .= '<br>
        Email : ' . $_POST['email'];
    }
    if ($_POST['page']){
        $msg .= '<br>
        Отправлено со страницы : ' . $_POST['page'];
    }
	if ($_POST['ymcid']){
        $msg .= '<br>
        Яндекс Метрика ClientID : ' . $_POST['ymcid'];
    }
	
    $msg_rep = str_replace('<br>', '', $msg);
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html' . "\r\n";
    wp_mail(get_field('admin_email', 'options'), 'Заявка с сайта', $msg, $headers, $files_path);
	if (get_field('admin_email2', 'options')){
        wp_mail(get_field('admin_email2', 'options'), 'Заявка с сайта', $msg, $headers, $files);
    }
	if (get_field('admin_amo', 'options')){
        $date = date('m/d/Y h:i:s ', time());
        wp_mail(get_field('admin_amo', 'options'), 'Заявка с квиз-сайта ' . $date, $msg);
    }
	if (get_field('tg_token', 'options') and get_field('tg_chat', 'options')){
        $ch = curl_init();
        curl_setopt_array(
                $ch,
                array(
                    CURLOPT_URL => 'https://api.telegram.org/bot' . get_field('tg_token', 'options') . '/sendMessage',
                    CURLOPT_POST => TRUE,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_POSTFIELDS => array(
                        'chat_id' => get_field('tg_chat', 'options'),
                        'text' => $msg_rep,
                        'parse_mode' => 'HTML',
                    ),
                )
            );
        curl_exec($ch);
    }
	
	if (get_field('b24_link', 'options') and get_field('b24_login', 'options') and get_field('b24_pass', 'options')){
		define('CRM_HOST', get_field('b24_link', 'options'));
		define('CRM_PORT', '443'); 
		define('CRM_PATH', '/crm/configs/import/lead.php');
		define('CRM_LOGIN', get_field('b24_login', 'options')); 
		define('SOURCE_ID', '1'); 
		define('CRM_PASSWORD', get_field('b24_pass', 'options')); 
		
		$postData = array(
			'TITLE' => $_POST['source_id'],
			'NAME' => $_POST['name'],
			'EMAIL_WORK' => $_POST['mail'],
			'PHONE_WORK' => $_POST['phone'],
			'SOURCE_ID' => $_POST['source_id'],		
			'COMMENTS' => $msg
		);
		$postData['LOGIN'] = CRM_LOGIN;
		$postData['PASSWORD'] = CRM_PASSWORD;
		$fp = fsockopen("ssl://".CRM_HOST, CRM_PORT, $errno, $errstr, 30);
	if ($fp)
	{
		// prepare POST data
		$strPostData = '';
		foreach ($postData as $key => $value)
			$strPostData .= ($strPostData == '' ? '' : '&').$key.'='.urlencode($value);

		// prepare POST headers
		$str = "POST ".CRM_PATH." HTTP/1.0\r\n";
		$str .= "Host: ".CRM_HOST."\r\n";
		$str .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$str .= "Content-Length: ".strlen($strPostData)."\r\n";
		$str .= "Connection: close\r\n\r\n";
		$str .= $strPostData;
		// send POST to CRM
		fwrite($fp, $str);
		// get CRM headers
		$result = '';
		while (!feof($fp))
		{
			$result .= fgets($fp, 128);
		}
		fclose($fp);
	}
	}
    
    $roistatData = array(
        'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : 'nocookie',
        'key'     => get_field('roistat_key', 'options'), 
        'title'   => 'Заявка с сайта - Квиз', 
        'comment' => $msg, 
        'name'    => $name,
        'email'   => $email, 
        'phone'   => $phone, 
        'order_creation_method' => '', 
        'is_need_callback' => '0', 
        'sync'    => '0', 
        'is_need_check_order_in_processing' => '1', 
        'is_need_check_order_in_processing_append' => '1', 
        'is_skip_sending' => '1', 
        'fields'  => array(
         "charset" => "Windows-1251", 
        ),
    );
    wp_die();
}