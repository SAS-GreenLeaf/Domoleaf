<?php

function display_smartcmd($info, $icon) {
	$display = 
	'<div class="display-widget col-xs-12 col-sm-6 col-lg-4 room-'.$info->room_id.' app-7">
		<div class="box">
			<div class="icon">
				<div id="image-smartcmd-'.$info->smartcmd_id.'" class="image">
					<i id="image-smartcmd-'.$info->smartcmd_id.'" class="'.$icon.'"></i>
				</div>
				<div class="info col-sm-12 col-xs-12 widget-content">
					<h3 class="title margin-top">'.$info->name.'</h3>
					<div>
						<button type="button" class="btn btn-warning" onclick="launchSmartcmd('.$info->smartcmd_id.')">
							'._('Go').'
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>';
	
	return $display;
}

?>