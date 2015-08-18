<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

if (empty($_GET['filename']) or !($_GET['filename'] > 0)){
	redirect();
}

echo '
<div class="center">';
printf(_('Do you want to restore the save of %s?').'<br/>'._('Caution, maybe you will be logout.'), '<strong>'.format_date($_GET['filename']).'</strong>');
echo '</div>
<div class="controls center">'.
'<button onclick="RestoreDbUsb('.$_GET['filename'].')" class="btn btn-success">'._('Yes').' <span class="glyphicon glyphicon-ok"></span></button> '.
'<button onclick="popup_close_last()" class="btn btn-danger">'._('No').' <span class="glyphicon glyphicon-remove"></span></button>'.
'</div>';

?>