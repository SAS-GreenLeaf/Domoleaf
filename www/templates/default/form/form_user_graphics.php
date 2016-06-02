<?php

include('header.php');

if (empty($_GET['device_id'])) {
	$_GET['device_id'] = '';
}

if (empty($_GET['option_id'])) {
	$_GET['option_id'] = '';
}

if (empty($_GET['from_dateval'])) {
	$_GET['from_dateval'] = date('Y-m-d');
}
else {
	$_GET['from_dateval'] = strtr($_GET['from_dateval'], '/', '-');
}

if (empty($_GET['to_dateval'])) {
	$_GET['to_dateval'] = date('Y-m-d');
}
else {
	$_GET['to_dateval'] = strtr($_GET['to_dateval'], '/', '-');
}
$request =  new Api();
$request -> add_request('getGraphicsInfo', array($_GET['device_id'], $_GET['option_id'], $_GET['from_dateval'], $_GET['to_dateval']));
$result  =  $request -> send_request();
$listvalues = $result->getGraphicsInfo;

/* 
**	$listvalues->config is an array containing informations about cost of KWh, hollow time slot
**	and the actual currency.
*/
if (!empty($listvalues->config)) {
	$currency = checkCurrency($listvalues->config->{18});
}

/*
**	$listvalues->datas is an array containing consumption values concerning the actual device.
**	it build $data to create a chart. It returns a string with '-@-' separators: 
**	[data for chart]-@-id_selected_option-@-name_selected_option-@-time_label
*/
if (!empty($listvalues->datas)) {

	/* Define Labels and colors */
	switch($_GET['option_id']) {
		case 6:
			$color = '#234CA5';
		break;
		case 72:
			$color = '#FF321D';
		break;
		case 399:
			$color = '#456B35';
			$other = '#678d57';
		break;
		default:
			$color = '#002F2F';
		break;
	}
	$graphList = array();
	foreach ($listvalues->datas as $graphic) {
		if ($_GET['option_id'] == 399) {
			$graphList[] = '{"time": "'.date('Y-m-d H:i', $graphic->date_time).'", "value": '.strtr($graphic->value, ',', '.').', "cost": '.strtr(round($graphic->price, 2), ',', '.').'}';
		}
		else {
			$graphList[] = '{"time": "'.date('Y-m-d H:i', $graphic->date_time).'", "value": '.strtr($graphic->value, ',', '.').'}';
		}
	}
	/* Determine the timeLabel for a properly display */
	$date_a = $listvalues->datas[0]->date_time;
	$date_b = strtotime($_GET['to_dateval']);
	$interval = ($date_b - $date_a) / 86400;
	if ($interval < 1) {
		$xlabel = 'hour';
	}
	else if ($interval >= 1 && $interval < 61) {
		$xlabel = 'day';
	}	
	else if ($interval > 60 && $interval < 730) {
		$xlabel = 'month';
	}
	else  {
		$xlabel = 'year';
	}
	
	/* Build object for chart creation */
	if ($_GET['option_id'] == 399) {
		$res = array(
		'data'   => '['.implode(', ', $graphList).']',
		'xlabel' => $xlabel,
		'ykey'  => ['value', 'cost'],
		'ylabel' => [$listvalues->infos->name.' '.$listvalues->infos->unit, _('Cost').' '.$currency],
		'color'  => [$color, $other],
		'unit' => ''
		);
	}
	else {
		$res = array(
		'data'   => '['.implode(', ', $graphList).']',
		'xlabel' => $xlabel,
		'ykey'  => ['value'],
		'ylabel' => [$listvalues->infos->name],
		'color'  => [$color],
		'unit' => $listvalues->infos->unit
		);
	}
echo json_encode($res);
}
?>
