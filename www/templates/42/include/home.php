<?php 

include('templates/'.TEMPLATE.'/function/display_widget.php');

$devices_notPlaced = array(); $devices_placed = array();

if (isset($_GET['id']) && floor_exist($_GET['id'])){
	$devices_notPlaced = getDevices($_GET['id'], false);
	$devices_placed = getDevices($_GET['id'], true);
}

$displayBottom = (isset($_GET['id']) && floor_exist($_GET['id'])) ? '' : 'display: none';

echo '
<script type="text/javascript">global_template = "'.TEMPLATE.'"; var sentence_goBackFloor = "'._('Back to floor selection').'";</script>

<script type="text/javascript" src="templates/'.TEMPLATE.'/homePositioning.js"></script>

<div class="popContainer">
	<div id="popBottom" style="'.$displayBottom.'">
		<div id="popInnerBottom">
		<div class="innerSentence">'._('You can stock here the devices you don\'t know where to put.').'</div>'; 
			
			if (count($devices_notPlaced) > 0){
				foreach($devices_notPlaced as $device){
					$this_icon = (isset($icons_device[$device->device_id])) ? $icons_device[$device->device_id] : '';
					
					echo '
						<div class="box device_self draggable" data-id="'.$device->room_device_id.'" style="top:0; left: 0;">
							<div class="icon">
								<div class="image">
									<i class="'.$this_icon.'"></i>
								</div>
							</div>
							
							<p>'.$device->name.'</p>
						</div>';
				}
			}
			
			if (count($devices_placed) > 0){ 
				foreach($devices_placed as $device){
					$this_icon = (isset($icons_device[$device->device_id])) ? $icons_device[$device->device_id] : '';
					
					echo '
						<div class="box device_self draggable loadingPose" data-id="'.$device->room_device_id.'" data-x="'.$device->pos_x_icon.'" data-y="'.$device->pos_y_icon.'">
							<div class="icon">
								<div class="image">
									<i class="'.$this_icon.'"></i>
								</div>
							</div>
							
							<p>'.$device->name.'</p>
						</div>';
				}
			}
			
			if (count($devices_placed) > 0 || count($devices_notPlaced)){
				echo '<script type="text/javascript" src="templates/'.TEMPLATE.'/dragDevices.js"></script>';
			}
	
	echo '
		</div>
	</div>
</div>

<div class="popContainer zunder">
	<div id="popMiddle">
		<div id="innerPopMiddle">
			';
				
				// The user didn't choose any floor yet
			if (!isset($_GET['id']) || !floor_exist($_GET['id'])){ 
		
		echo '
			<h4>'._('Choose a floor to display').':</h4>
			<div id="home">
				<img src="templates/'.TEMPLATE.'/img/top_house.png" id="topHouse" />
				';
				
				foreach($floorallowed as $floor){
					echo  '<div class="floor_self" onclick="if (window.location.href.match(/\?/)){ window.location.href = window.location.href+\'&id='.$floor->floor_id.'\'; } else { window.location.href = window.location.href+\'?id='.$floor->floor_id.'\'; }"><img src="templates/'.TEMPLATE.'/img/part_house.png" class="partHouse" /><div class="floor_name">'.$floor->floor_name.'</div></div>';
				}
				
				echo '
				<img src="templates/'.TEMPLATE.'/img/base_house.png" id="baseHouse" />
			</div>';
			} else {
				$floor_id = $_GET['id'];
				$floor_background = getFloorBackground($floor_id);
				
				if (is_null($floor_background)){
					echo '<div id="glob_title" onclick="window.location.href=\'profile_user_installation\';">'._('You didn\'t choose any background image for this floor yet.').'<br />'._('Click here to add one.').'</div>';
					
					echo '<script type="text/javascript">posiAdditionalElement();</script>';
				} else {
					echo '<img src="'.$floor_background.'" id="globalFloorBackground" />';
				}
			}
		echo '
		</div>
	</div>
</div>';

?>