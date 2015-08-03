<?php

include('header.php');

echo
'<div id="colorpicker"></div>'.
'<form><input type="text" id="color" name="color" value="#123456" disabled="disabled" /></form>'.
'<script type="text/javascript">'.
'$("#colorpicker").farbtastic("#color");'.
'</script>';

?>