<?php 

include('header.php');

$request =  new Api();
$request -> add_request('confDaemonList');
$result  =  $request -> send_request();

$listdaemon = $result->confDaemonList;

$daemon = $listdaemon->$_GET['id'];

echo '
<div class="center">';
printf(_('Do you want to reboot %s?'), '<strong>'.$daemon->name.'</strong>');
echo '<br/>'._('It will take 60 seconds.').'';
echo '</div>
<div class="controls center">
	<button  id="" onclick="RebootD3('.$daemon->daemon_id.')" class="btn btn-success">'._('Yes').' <span class="glyphicon glyphicon-ok"></span></button> <button onclick="popup_close()" class="btn btn-danger">'._('No').' <span class="glyphicon glyphicon-remove"></span></button>
</div>';

?>
