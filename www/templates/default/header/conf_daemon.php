<?php 

echo '<title>'._('Box configuration').'</title>';

$request =  new Api();
$request -> add_request('confDaemonList');
$request -> add_request('confProtocolAll');
$result  =  $request -> send_request();

$listdaemon = $result->confDaemonList;
$allproto   = $result->confProtocolAll;
$set = 0;

?>