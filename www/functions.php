<?php 

function langToLocale($lang) {
	switch($lang) {
		case 'de':
			return 'de_DE';
		case 'es':
			return 'es_ES';
		case 'fr':
			return 'fr_FR';
		case 'it':
			return 'it_IT';
		case 'en':
			return 'en_UK';
		default:
			return NULL;
	}
}

function defineLocale($lang='') {
	$locale = langToLocale($lang);
	if (!empty($locale)){
		define('LOCALE', $locale);
	}
	else{
		define('LOCALE', detect_language());
	}
	$results = putenv("LC_ALL=".LOCALE);
	$results = putenv("LANG=".LOCALE);
	$results = setlocale(LC_ALL, LOCALE.'.utf8');
	$results = bind_textdomain_codeset("messages", "UTF-8");
	$results = bindtextdomain("messages", "./locales");
	$results = textdomain("messages");
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
			return utf8_encode(strftime('%d/%m/%y', $time)).' at '.
			strftime('%l:%M%P', $time);
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

function convertHexaToRGB($color) {
	if ($color[0] == '#') {
		$color = substr($color, 1);
	}
	$red = substr($color, 0, 2);
	$green = substr($color, 2, 2);
	$blue = substr($color, 4, 2);

	$red = hexdec($red);
	$green = hexdec($green);
	$blue = hexdec($blue);

	return array($red, $green, $blue);
}

function convertHexaToRGBW($color) {
	if ($color[0] == '#') {
		$color = substr($color, 1);
	}
	
	$red = substr($color, 0, 2);
	$green = substr($color, 2, 2);
	$blue = substr($color, 4, 2);
	$white = substr($color, 6, 2);
	
	$red   = hexdec($red);
	$green = hexdec($green);
	$blue  = hexdec($blue);
	$white = hexdec($white);
	
	return array($red, $green, $blue, $white);
}

function convertRGBToHexa($red, $green, $blue) {
	$red = dechex($red);
	$green = dechex($green);
	$blue = dechex($blue);
	$hexa_color = '#'.$red.$green.$blue;
	
	return $hexa_color;
}

function convertRGBWToHexa($red, $green, $blue, $white) {
	$red   = dechex($red);
	$green = dechex($green);
	$blue  = dechex($blue);
	$white = dechex($white);
	$hexa_color = '#'.$red.$green.$blue.$white;
	
	return $hexa_color;
}

function compress_image($src, $dest , $quality)
{
	$info = getimagesize($src);
	if ($info['mime'] == 'image/jpeg') {
		$image = imagecreatefromjpeg($src);
	}
	else if ($info['mime'] == 'image/png') {
		$image = imagecreatefrompng($src);
	}
	else {
		return null;
	}

	//compress and save file to jpg
	imagejpeg($image, $dest, $quality);

	//return destination file
	return $dest;
}

function checkCurrency($currencyId) {
	$allCurrency = array(
			2	=>	'$',
			1	=>	'€',
			3	=>	'₣',
			4	=>	'£',
			5	=>	'¥',
			6	=>	'Ұ',
	);
	if (!empty($allCurrency[$currencyId])){
		return $allCurrency[$currencyId];
	}
	else{
		return NULL;
	}
}

?>
