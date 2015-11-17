<?php

include('header.php');

if (!empty($_GET['iddevice'])) {
	$request =  new Api();
	$result  =  $request -> send_request();
	
	echo
	'<div class="center">'.
		'<form class="btn center padding-bottom">'.
			'<input type="text" id="color" name="color" value="#123456" disabled="disabled"/>'.
		'</form>';
		if (!empty($_GET['bg_color'])) {
			echo
			'<button class="btn btn-warning" onclick="updateBGColor(\'#eee\', '.$_GET['userid'].')">'.
				_('Reset').
				'&nbsp<span class="glyphicon glyphicon-refresh"></span>'.
			'</button>';
		}
	echo
	'</div>'.
	'<div id="colorpicker"></div>
	<div class="controls center">'.
		'<button class="btn btn-success"';
		if (empty($_GET['bg_color'])) {
			echo 'onclick="updateRGBColor('.$_GET['iddevice'].', $(\'#color\').val())"';
		}
		else {
			echo 'onclick="updateBGColor($(\'#color\').val(), '.$_GET['userid'].')"';
		}
		echo '>'.
			_('Send').
			'&nbsp<span class="glyphicon glyphicon-ok"></span>'.
		'</button> '.
		'<button onclick="popup_close()" class="btn btn-danger">'.
			_('Close').
			'&nbsp<span class="glyphicon glyphicon-remove"></span>'.
		'</button>'.
	'</div>'.
	
	'<script type="text/javascript">'.
		'$("#popupTitle").html("'._("Chroma Wheel").'");'.
		'$("#colorpicker").farbtastic("#color");'.
	'</script>';
}
?>