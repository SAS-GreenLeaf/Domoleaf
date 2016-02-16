<?php 

$request = new Api();
$request -> add_request('listScenarios');
$result  =  $request -> send_request();

$scenarioList = $result->listScenarios;

echo '<title>'._('Scenarios').'</title>';

?>