<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

echo '
<div class="center">'.
_('Please wait...').
'<br/>'.
'<br/>'.
'</div>';

echo '<script type="text/javascript">'.
		'$("#popupLoading").html("'._('Loading in progress').'");'.
		'</script>';
?>