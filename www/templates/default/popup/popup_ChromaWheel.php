<?php

include('header.php');

echo
	'<form class="center padding-bottom">'.
		'<input type="text" id="color" name="color" value="#123456" disabled="disabled"/>'.
	'</form>'.
	'<div id="colorpicker"></div>'.
	
	'<script type="text/javascript">'.
		'$("#colorpicker").on("mouseup touchend", function(event){'.
			'updateRGBColor('.$_GET['iddevice'].', $("#color").val());'.
		'});'.
		'$("#colorpicker").farbtastic("#color");'.
	'</script>';
?>