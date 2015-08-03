<?php 

echo '<title>'._('Monitor KNX').'</title>';


$request =  new Api();
$request -> add_request('confDaemonList');
$result  =  $request -> send_request();

$listdae = $result->confDaemonList;

?>