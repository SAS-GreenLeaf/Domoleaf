<?php 

$request =  new Api();
$request -> add_request('confDaemonList');
$result  =  $request -> send_request();

echo '<title>'._('Monitor KNX').'</title>';

$listdae = $result->confDaemonList;

?>