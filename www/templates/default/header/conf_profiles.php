<?php 

if (empty($_GET['user'])){
	redirect();
}
else {
	$userid = $_GET['user'];
}

$request =  new Api();
$request -> add_request('profileInfo', Array($_GET['user']));
$request -> add_request('language');
$request -> add_request('design');
$request -> add_request('confMenuProtocol');
$result  =  $request -> send_request();

$profilInfo = $result->profileInfo;
$language = $result->language;
$currentuser = $request->getId();
$menuProtocol = $result->confMenuProtocol;

if (empty($profilInfo)){
	redirect('/conf_users');
}

$allTimeZone = array(
		2	=>	'UTC-12',
		3	=>	'UTC-11',
		4	=>	'UTC-10',
		5	=>	'UTC-9:30',
		6	=>	'UTC-9',
		7	=>	'UTC-8',
		8	=>	'UTC-7',
		9	=>	'UTC-6',
		10	=>	'UTC-5',
		11	=>	'UTC-4:30',
		12	=>	'UTC-4',
		13	=>	'UTC-3:30',
		14	=>	'UTC-3',
		15	=>	'UTC-2',
		16	=>	'UTC-1',
		17	=>	'UTC',
		1	=>	'UTC+1',
		18	=>	'UTC+2',
		19	=>	'UTC+3',
		20	=>	'UTC+3:30',
		21	=>	'UTC+4',
		22	=>	'UTC+4:30',
		23	=>	'UTC+5',
		24	=>	'UTC+5:30',
		25	=>	'UTC+5:45',
		26	=>	'UTC+6',
		27	=>	'UTC+6:30',
		28	=>	'UTC+7',
		29	=>	'UTC+8',
		30	=>	'UTC+8:30',
		31	=>	'UTC+8:45',
		32	=>	'UTC+9',
		33	=>	'UTC+9:30',
		34	=>	'UTC+10',
		35	=>	'UTC+10:30',
		36	=>	'UTC+11',
		37	=>	'UTC+11:30',
		38	=>	'UTC+12',
		39	=>	'UTC+12:45',
		40	=>	'UTC+13',
		41	=>	'UTC+14'
);

?>