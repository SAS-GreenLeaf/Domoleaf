<?php

include('header.php');

if (!empty($_GET['smartcmd_name'])) {
	$request =  new Api();
	$request -> add_request('createNewSmartcmd', array($_GET['smartcmd_name']));
	$result  =  $request -> send_request();

	if (empty($result->createNewSmartcmd) || $result->createNewSmartcmd == -1) {
		echo '-1'.
		'<div class="alert alert-danger alert-dismissible center" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'">
				<span aria-hidden="true">&times;</span>
			</button>
			'._('Name already existing').'
		</div>';
	}
	else {
		echo $result->createNewSmartcmd;
	}
}
else {
	echo '-1'.
			'<div class="alert alert-danger alert-dismissible center" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'">
					<span aria-hidden="true">&times;</span>
				</button>
				'._('Invalid Name').'
			</div>';
}

?>