<?php 

echo '<title>'._('Sign in').'</title>';
$login_error = '';
$email_current = '';


if(!empty($_POST['username']) && !empty($_POST['password'])) {
	$request =  new Api();
	$request -> add_request('connection', array($_POST['username'], $_POST['password']));
	$result  =  $request -> send_request();

	if(!empty($result) && !empty($result->connection)) {
		if(!empty($result->connection->token)) {
			setcookie('token', $result->connection->token, ($_SERVER['REQUEST_TIME']+3600*24*14), '/');
			redirect('home');
		}
		else if($result->connection->error > 0) {
			$login_error = number2Error($result->connection->error);
		}
	}
}
	
?>