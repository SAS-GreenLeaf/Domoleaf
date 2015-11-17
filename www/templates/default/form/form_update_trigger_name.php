<?php

include('header.php');

if (!empty($_GET['trigger_id']) && !empty($_GET['trigger_name'])) {
	$request =  new Api();
	$request -> add_request('updateTriggerName', array($_GET['trigger_id'], $_GET['trigger_name']));
	$result  =  $request -> send_request();

	if (empty($result->updateTriggerName) || $result->updateTriggerName == -1) {
		echo '-1'.
		'<div class="alert alert-danger alert-dismissible center" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'">
				<span aria-hidden="true">&times;</span>
			</button>
			'._('Name already existing').'
		</div>';
	}
	else {
		echo $result->updateTriggerName;
	}
}
else {
	$request =  new Api();
	$result  =  $request -> send_request();
	echo '-1'.
			'<div class="alert alert-danger alert-dismissible center" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'">
					<span aria-hidden="true">&times;</span>
				</button>
				'._('Invalid Name').'
			</div>';
}

?>