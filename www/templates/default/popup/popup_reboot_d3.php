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
echo '<br/>'._('It will take 60 seconds.').''.
	 '<br/><br/>'.
	 ''._('If you want to shutdown, be careful, you will have to switch off switch on the D3').''.
	 '<br/><br/>'.
	 '</div>'.
	 '<div class="controls center">'.
	 	'<button onclick="RebootD3('.$daemon->daemon_id.', 2)" class="btn btn-danger">'._('Shutdown').' <span class="glyphicon glyphicon glyphicon-off"></span></button> '.
	 	'<button onclick="RebootD3('.$daemon->daemon_id.', 1)" class="btn btn-warning">'._('Reboot').' <span class="glyphicon glyphicon-refresh""></span></button> '.
	 '<button onclick="popup_close()" class="btn btn-success">'._('Cancel').' <span class="fa fa-reply"></span></button>'.
'</div>';

?>
