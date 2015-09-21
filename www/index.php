<?php

header('GLD3: 780923');
include('config.php');
include('libs/Link.class.php');
include('functions.php');

include('libs/Socket.class.php');
include('libs/Guest.class.php');
include('libs/User.class.php');
include('libs/Admin.class.php');
include('libs/Root.class.php');

include('libs/Api.class.php');

$request = new Api();
$result  = $request -> send_request();

if(empty($_GET['page'])) {
	$_GET['page'] = '';
}

if($request->is_co()) {
	if ($request->getLevel() > 1){
		switch ($_GET['page']) {
			
			//Profile
			case 'profile':
				$page = $_GET['page'];
			break;
			
			//Profile installation
			case 'profile_user_installation':
				$page = $_GET['page'];
			break;
			
			//Profile scenarios
			case 'profile_user_scenarios':
				$page = $_GET['page'];
			break;
			
			//Profile edit scenarios
			case 'profile_user_scenarios_edit':
				$page = $_GET['page'];
				break;
			
			//Conf_general
			case 'conf_general':
				$page = $_GET['page'];
			break;
			
			//Conf_database
			case 'conf_db':
				$page = $_GET['page'];
				break;
			
			//Conf_installation
			case 'conf_installation':
				$page = $_GET['page'];
			break;
			
			//Conf_room
			case 'conf_room':
				$page = $_GET['page'];
			break;
			
			//Conf_box
			case 'conf_box':
				$page = $_GET['page'];
			break;
			
			//Conf_devices
			case 'conf_device':
				$page = $_GET['page'];
			break;
			
			//Conf_device_new
			case 'conf_device_new':
				$page = $_GET['page'];
			break;	
			
			//Conf_user
			case 'conf_users':
				$page = $_GET['page'];
			break;
			
			//Conf_user_permission
			case 'conf_user_permission':
				$page = $_GET['page'];
			break;
				
			//Conf_profiles
			case 'conf_profiles':
				$page = $_GET['page'];
			break;
			
			//Conf_daemon
			case 'conf_daemon':
				$page = $_GET['page'];
			break;
			
			//monitor_knx
			case 'monitor_knx':
				$page = $_GET['page'];
			break;
			
			//monitor_ip
			case 'monitor_ip':
				$page = $_GET['page'];
			break;
	
			//monitor_enocean
			case 'monitor_enocean':
				$page = $_GET['page'];
			break;
			
			//monitor_bluetooth
			case 'monitor_bluetooth':
				$page = $_GET['page'];
			break;
			
			default:
				$page = 'home';
			break;
		}
	}
	else {
		switch ($_GET['page']) {
			
			//Profile
			case 'profile':
				$page = $_GET['page'];
			break;
			
			//Profile installation
			case 'profile_user_installation':
				$page = $_GET['page'];
			break;
			
			default:
				$page = 'home';
			break;
		}
	}
}
else {
	switch ($_GET['page']) {
		
		default:
			$page = 'signin';
		break;
	}
}
echo '
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="/favicon.ico">';

include('templates/'.TEMPLATE.'/header/'.$page.'.php');

echo '
		<!-- Bootstrap core CSS -->
		<link href="/css/jquery-ui.min.css" rel="stylesheet">
		<link href="/css/bootstrap.min.css" rel="stylesheet">
		<link href="/css/bootstrap-dialog.min.css" rel="stylesheet">
		
		<link href="/css/font-awesome.min.css" rel="stylesheet">
		<link href="/css/flaticon.css" rel="stylesheet">
		<link href="/css/bootstrap-switch.css" rel="stylesheet">
		<link href="/css/bootstrap-select.css" rel="stylesheet">
		
		<!-- Custom styles for this template -->
		<link href="/templates/'.TEMPLATE.'/design.css" rel="stylesheet">

		<script src="/js/jquery.min.js"></script>
		<script src="/js/jquery-ui.min.js"></script>
		<script src="/js/touch.js"></script>
		
		<script src="/templates/'.TEMPLATE.'/scripts.js"></script>
		<script type="text/javascript" src="/js/farbtastic.js"></script>
		<script src="/js/bootstrap-switch.js"></script>
		<script src="/js/bootstrap-select.js"></script>
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="/js/html5shiv.min.js"></script>
			<script src="/js/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>';

	if($request->is_co())
	{
		include('templates/'.TEMPLATE.'/top_connected.php');
		
		echo '
		<div class="container-fluid">
		<div class="row">';
		
		include('templates/'.TEMPLATE.'/include/'.$page.'.php');
		
		echo '
		</div></div>';
		
		include('templates/'.TEMPLATE.'/bottom_connected.php');
	}
	else
	{
		include('templates/'.TEMPLATE.'/top_guest.php');
		include('templates/'.TEMPLATE.'/include/'.$page.'.php');
		include('templates/'.TEMPLATE.'/bottom_guest.php');
	}
echo'
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
		<script src="/js/ie10-viewport-bug-workaround.js"></script>
	
		<script src="/js/jquery.cookie.js"></script>
		<script src="/js/bootstrap.min.js"></script>
		<script src="/js/bootstrap-dialog.min.js"></script>
	</body>
</html>';

?>