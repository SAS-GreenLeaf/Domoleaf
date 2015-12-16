<?php

include('header.php');

$request =  new Api();
$request -> add_request('profileTime');
$result  =  $request -> send_request();

$time = $result->profileTime;

if (empty($_GET['filename']) or !($_GET['filename'] > 0)){
	redirect();
}

echo '
<div class="center">';
printf(_('Do you want to delete the save of %s?'), '<strong>'.format_date($_GET['filename'] + $time).'</strong>');
echo '</div>
<div class="controls center">'.
	'<button onclick="RemoveDbUsb('.$_GET['filename'].')" class="btn btn-success">'._('Yes').' <span class="glyphicon glyphicon-ok"></span></button> '.
	'<button onclick="popup_close_last()" class="btn btn-danger">'._('No').' <span class="glyphicon glyphicon-remove"></span></button>'.
'</div>';

?>