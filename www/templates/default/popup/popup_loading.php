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

?>