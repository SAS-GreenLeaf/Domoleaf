<?php 

include('header.php');

$request =  new Api();
$request -> add_request('confDaemonList');
$result  =  $request -> send_request();

$listdaemon = $result->confDaemonList;

$daemon = $listdaemon->$_GET['id'];

echo '
<div class="center">';
printf(_('It will take few seconds to shutdown %s.'), '<strong>'.$daemon->name.'</strong>');
echo '</div>';

echo '<script type="text/javascript">'.

'$(document).ready(function(){'.
	'setTimeout(function(){popup_close();}, 10000);'.
'});'.

'</script>';

?>
