<?php

include('header.php');

if (!empty($_GET['scenario_id']) && !empty($_GET['scenario_name'])) {
	$request =  new Api();
	$request -> add_request('updateScenarioName', array($_GET['scenario_id'], $_GET['scenario_name']));
	$result  =  $request -> send_request();

	if (empty($result->updateScenarioName) || $result->updateScenarioName == -1) {
		echo '-1'.
		'<div class="alert alert-danger alert-dismissible center" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'">
				<span aria-hidden="true">&times;</span>
			</button>
			'._('Name already existing').'
		</div>';
	}
	else {
		echo $result->updateScenarioName;
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