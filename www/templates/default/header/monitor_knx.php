<?php 

$request =  new Api();
$request -> add_request('confDaemonList');
$request -> add_request('confMenuProtocol');
$result  =  $request -> send_request();

echo '<title>'._('Monitor KNX').'</title>';

$listdae = $result->confDaemonList;
$menuProtocol = $result->confMenuProtocol;

?>