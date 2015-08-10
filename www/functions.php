<?php 

function defineLocale($lang='') {
	switch($lang) {
		case 'de':
			define('LOCALE', 'de_DE');
		break;
		case 'es':
			define('LOCALE', 'es_ES');
		break;
		case 'fr':
			define('LOCALE', 'fr_FR');
		break;
		case 'it':
			define('LOCALE', 'it_IT');
		break;
		case 'en':
			define('LOCALE', 'en_UK');
		break;
		default:
			 define('LOCALE', detect_language());
		break;
	}
	putenv("LC_ALL=".LOCALE);
	setlocale(LC_ALL, LOCALE);
	bind_textdomain_codeset("messages", "UTF-8");
	bindtextdomain("messages", "./locales");
	textdomain("messages");
}

/**
 * Redirect to an URL after "time" seconds
 * @param string : destination
 * @param int : time en seconds
 */
function redirect($url='/', $time=0) {
	echo'<meta http-equiv="refresh" content="',$time,'; url=',$url,'" />';
	if(empty($time)) {
		exit();
	}
}

/**
 * Format a number
 * @param int : number
 * @param int : decimal places
 */
function nbf($n, $nb=2) {
	if(is_numeric($n)) {
		if($n == round($n)) {
			return number_format($n , 0, ',', ' ');
		}
		else {
			if($nb == 0) {
				$n = floor($n);
			}
			return number_format($n , $nb, ',', ' ');
		}
	}
}

/**
 * 
 * @param unknown $error
 * @param unknown $param
 */
function number2Error($error=0, $param=array()) {
	$text = '';
	
	switch ($error) {
		case 1:
			$text = _('This account does not exist');
		break;
		
		case 2:
			$text = _('Bad password');
		break;
	}
	return $text;
}

function detect_language() {
	$language = null;
	if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$language = $language{0}.$language{1};
	}
	switch ($language)
	{
		case 'fr':
			return 'fr_FR';
		break;
		
		default :
			return 'en_UK';
		break;
	}
}

function format_date($time) {
	switch(LOCALE) {
		case 'fr_FR':
			return utf8_encode(strftime('%d/%m/%y', $time)).' à '.
			strftime('%H:%M', $time);
		break;
		case 'en_UK':
			return utf8_encode(strftime('%d/%m/%y', $time)).' at '.
			strftime('%l:%M%P', $time);
		break;
		case 'es_ES':
			return utf8_encode(strftime('%d/%m/%y', $time)).' a las '.
			strftime('%H:%M', $time);
		break;
		default:
			return utf8_encode(strftime('%d/%m/%y', $time)).' à '.
			strftime('%H:%M', $time);
		break;
	}
	putenv("LC_ALL=".LOCALE);
	setlocale(LC_ALL, LOCALE);
	bind_textdomain_codeset("messages", "UTF-8");
	bindtextdomain("messages", "./locales");
	textdomain("messages");
}

function format_size($bytes, $format = '%.2f') {
	$units = array(
			_('B'),
			_('KB'),
			_('MB'),
			_('GB')
	);
	$b = (double)$bytes;
	if($b > 0) {
		$e = (int)(log($b, 1024));
		if(isset($units[$e]) === false) {
			$e = 2;
		}
		$b = $b/pow(1024, $e);
	}
	else {
		$b = 0;
		$e = 0;
	}
	return sprintf($format.' %s', $b, $units[$e]);
}

?>