<?php

$request =  new Api();
$request -> add_request('conf_load');
$request -> add_request('confMenuProtocol');
$result  =  $request -> send_request();

echo '<title>'._('General Configuration').'</title>';

$generalinfo = $result->conf_load;
$menuProtocol = $result->confMenuProtocol;

$httpport  = $generalinfo->{'1'}->configuration_value;
$httpsport = $generalinfo->{'2'}->configuration_value;
$forcessl  = $generalinfo->{'3'}->configuration_value;

$mastersversion = $generalinfo->{'4'}->configuration_value;

$fromMail = $generalinfo->{'5'}->configuration_value;
$fromName = $generalinfo->{'6'}->configuration_value;
$smtpHost = $generalinfo->{'7'}->configuration_value;
$smtpSecure = $generalinfo->{'8'}->configuration_value;
$smtpPort = $generalinfo->{'9'}->configuration_value;
$smtpUsername = $generalinfo->{'10'}->configuration_value;

$highCost = $generalinfo->{'14'}->configuration_value;
$lowCost = $generalinfo->{'15'}->configuration_value;
$lowField1 = $generalinfo->{'16'}->configuration_value;
$lowField2 = $generalinfo->{'17'}->configuration_value;
$currency = $generalinfo->{'18'}->configuration_value;

if (!empty($lowField1) && !empty(explode('-', $lowField1)[0]) && !empty(explode('-', $lowField1)[1])){
	$lowField1_1 = explode('-', $lowField1)[0];
	$lowField1_2 = explode('-', $lowField1)[1];
}
else{
	$lowField1_1 = 0;
	$lowField1_2 = 0;
}

if (!empty($lowField2) && !empty(explode('-', $lowField2)[0]) && !empty(explode('-', $lowField2)[1])){
	$lowField2_1 = explode('-', $lowField2)[0];
	$lowField2_2 = explode('-', $lowField2)[1];
}
else{
	$lowField2_1 = 0;
	$lowField2_2 = 0;
}

$allCurrency = array(
	2	=>	_('Dollar - $'),
	1	=>	_('Euro - €'),
	3	=>	_('Franc Suisse - ₣'),
	4	=>	_('Livre Sterling - £'),
	5	=>	_('Yen - ¥'),
	6	=>	_('Yuan - Ұ'),
);

?>
