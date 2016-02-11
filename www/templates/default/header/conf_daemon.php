<?php 

$request =  new Api();
$request -> add_request('confDaemonList');
$request -> add_request('confProtocolAll');
$request -> add_request('confMenuProtocol');
$result  =  $request -> send_request();

echo '<title>'._('Box configuration').'</title>';

$listdaemon = $result->confDaemonList;
$allproto   = $result->confProtocolAll;
$menuProtocol = $result->confMenuProtocol;
$set = 0;

?>