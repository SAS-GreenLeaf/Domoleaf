<?php

echo '<title>'._('General Configuration').'</title>';

$request =  new Api();
$request -> add_request('conf_load');
$result  =  $request -> send_request();

$generalinfo = $result->conf_load;

$httpport = $generalinfo->{'1'}->configuration_value;
$httpsport = $generalinfo->{'2'}->configuration_value;
$mastersversion = $generalinfo->{'4'}->configuration_value;

?>