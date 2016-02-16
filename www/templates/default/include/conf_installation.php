<?php 

include('configuration-menu.php');

echo '
<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">';
	echo '
	<div class="center"><h2>'._('Configuration installation').'</h2></div>
		
		<div class="btn-group btn-group-greenleaf block-right">';
			if (sizeof($roomlist) == 0){
				echo '
				<button type="button" class="btn btn-greenleaf" onclick="PopupNewFloor()">
					<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> '._('New floor').'
				</button>';
			}
			else {
				echo '
				<button type="button" class="btn btn-greenleaf" onclick="PopupNewRoom()">
					<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> '._('New room').'
				</button>
				<button type="button"
				        class="btn btn-greenleaf dropdown-toggle"
				        data-toggle="dropdown">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu dropdown-confgreenleaf  dropdown-menu-right">
					<li><a onclick="PopupNewFloor()">'._('New floor').'</a></li>
				</ul>';
			}
		echo '
		</div><br/><br/>';
		foreach ($roomlist as $elem) {
			echo '
			<table id="table-'.$elem['id'].'" class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th id="thfield-'.$elem['id'].'">
							<a href="/conf_installation/'.$elem['id'].'" >'.$elem['name'].'</a>
						</th>
						<th class="col-sm-2 col-xs-2 center">
							<a href="/conf_installation/'.$elem['id'].'" class="btn btn-info">
								<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
							</a>
							<button type="button" title="'._('Edit').'" class="btn btn-primary" id="btn-edit-'.$elem['id'].'" onclick="PopupRenameFloor('.$elem['id'].')">
								<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
							</button>
							<button type="button" title="'._('Delete floor').'" id="btn-del-'.$elem['id'].'" class="btn btn-danger" onclick="PopupRemoveFloor('.$elem['id'].')">
								<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
							</button>
						</th>
					</tr>
				</thead>
				<tbody>';
					foreach ($elem['room'] as $room){
						echo '
							<tr>
								<td>
									<a href="/conf_installation/'.$elem['id'].'/'.$room['id'].'">'.$room['name'].'</a>
								</td>
								<td class="center">
									<a href="/conf_installation/'.$elem['id'].'/'.$room['id'].'" class="btn btn-info">
										<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
									</a>
									<button type="button" title="'._('Edit').'" class="btn btn-primary" id="btn-edit-'.$room['id'].'" onclick="PopupRenameRoom('.$room['id'].','.$elem['id'].')">
										<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
									</button>
									<button type="button" title="'._('Delete room').'" id="btn-del-'.$room['id'].'" class="btn btn-danger" onclick="PopupRemoveRoom('.$room['id'].','.$elem['id'].')">
										<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
									</button>
								</td>
							</tr>';
					}
				echo '
				</tbody>
			</table>';
		}
echo '
</div>
		
<script type="text/javascript">

$(document).ready(function(){
	activateMenuElem(\'install\');
});

function PopupRenameFloor(idfloor){
		
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_rename_floor.php",
		data: "id="+idfloor,
		success: function(msg) {
			BootstrapDialog.show({
				title: "'._('Edit Floor').'",
				message: msg
			});
		}
	});
}

function PopupRenameRoom(idroom){
		
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_rename_room.php",
		data: "idroom="+idroom,
		success: function(msg) {
			BootstrapDialog.show({
				title: "'._('Edit Room').'",
				message: msg
			});
		}
	});
}

function PopupRemoveFloor(idfloor) {
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_delete_floor.php",
		data: "id="+idfloor,
		success: function(msg) {
			BootstrapDialog.show({
				title: "'._('Remove floor').'",
				message: msg
			});
		}
	});
}

function PopupRemoveRoom(roomid, floorid){
		
		$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_delete_room.php",
		data: "roomid="+roomid+"&floorid="+floorid,
		success: function(msg) {
			BootstrapDialog.show({
				title: "'._('Confirm').'",
           		message: msg
        	});
		}
	});
	
}

function PopupNewFloor(){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_new_floor.php",
		success: function(msg) {
			BootstrapDialog.show({
				title: "'._('New floor').'",
				message: msg
			});
		}
	});
}

function PopupNewRoom(){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_new_room.php",
		success: function(msg) {
			BootstrapDialog.show({
				title: "'._('New Room').'",
 				message: msg
			});
		}
	});
}

function FloorNew(){
	var namefloor = $("#newfloor").val();
	var nameroom = $("#newroom").val();

	$.ajax({
		type:"GET",
		url: "/form/form_floor_new.php",
		data: "namefloor="+encodeURIComponent(namefloor)
		      +"&nameroom="+encodeURIComponent(nameroom),
		beforeSend:function(result, status){
			PopupLoading();
		},
		complete: function(result, status) {
			location.href=\'/conf_installation\';
		}
	});
}

function floorRemove(floorid) {
	$.ajax({
		type:"GET",
		url: "/form/form_floor_remove.php",
		data: "floor="+floorid,
		complete: function(result, status) {
			location.href=\'/conf_installation\';
		}
	});
}

function RemoveRoom(roomid, floorid) {
	$.ajax({
		type:"GET",
		url: "/form/form_room_remove.php",
		data: "roomid="+roomid+"&floorid="+floorid,
		complete: function(result, status) {
			location.href=\'/conf_installation\';
		}
	});
}

function FloorRename(floorid){
	var namefloor = $("#newfloorname").val();

	if (namefloor != \'\') {
		$.ajax({
			type:"GET",
			url: "/form/form_floor_new.php",
			data: "namefloor="+encodeURIComponent(namefloor)+"&id="+floorid,
			complete: function(result, status) {
				location.href=\'/conf_installation\';
			}
		});
	}
	else {
		location.href=\'/conf_installation\';
	}
}
						
function RoomManager(idroom){
		var room = $("#newroomname").val();
		var floor = $("#changefloor").val();

		if (room != \'\'){
			$.ajax({
				type:"GET",
				url: "/form/form_room_new.php",
				data: "id="+idroom+"&name="+encodeURIComponent(room),
				complete: function(result, status) {
					$.ajax({
						type:"GET",
						url: "/form/form_room_move.php",
						data: "room="+idroom+"&floor="+floor,
						complete: function(result, status) {
							location.href=\'/conf_installation\';
						}
					});
				}
			});
			
		}
		else{
			location.href=\'/conf_installation\';
		}
}

function RoomNew() {
	var idfloor = $("#floorsel").val();
	var name = $("#newroom").val();
	
	if (idfloor != 0) {
		$.ajax({
			type:"GET",
			url: "/form/form_room_new.php",
			data: "name="+encodeURIComponent(name)+"&idfloor="+idfloor,
			beforeSend:function(result, status){
				PopupLoading();
			},
			complete: function(result, status) {
				location.href=\'/conf_installation\';
			}
		});				
	}
	else{
		location.href=\'/conf_installation\';				
	}
}
</script>';

?>