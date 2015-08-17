<?php

echo '<title>'._('General Configuration').'</title>';

$request =  new Api();
$request -> add_request('conf_load');
$result  =  $request -> send_request();

$generalinfo = $result->conf_load;

$httpport = $generalinfo->{'1'}->configuration_value;
$httpsport = $generalinfo->{'2'}->configuration_value;
$mastersversion = $generalinfo->{'4'}->configuration_value;
$fromMail = $generalinfo->{'5'}->configuration_value;
$fromName = $generalinfo->{'6'}->configuration_value;
$smtpHost = $generalinfo->{'7'}->configuration_value;
$smtpSecure = $generalinfo->{'8'}->configuration_value;
$smtpPort = $generalinfo->{'9'}->configuration_value;
$smtpUsername = $generalinfo->{'10'}->configuration_value;

?>