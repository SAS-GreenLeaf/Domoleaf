<?php 

include('configuration-menu.php');

echo '
<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">';
	echo '
	<div class="col-xs-12"><h2 class="center">'._('Box configuration').'</h2></div>';
	echo '
	<div class="btn-group btn-group-greenleaf decalage-droite block-right">
		<button type="button" class="btn btn-greenleaf" onclick="PopupNewDaemon()">
			<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> '._('New Box').'
		</button>
	</div><br/><br/>';
	if (!empty($listdaemon)) {
		echo '
		<table class="table table-bordered table-striped table-condensed">
			<thead>
				<tr>
					<th>'._('Name').'</th>
					<th>'._('Serial').'</th>
					<th>'._('Protocol').'</th>
					<th class="col-xs-1">'._('Validation').'</th>
					<th class="col-sm-2 col-xs-2 center">&nbsp;</th>
				</tr>
			</thead>
			<tbody>';
				foreach ($listdaemon as $elem){
					echo '
					<tr>
						<td>
							'.$elem->name.'
						</td>
						<td>
							'.$elem->serial.'
						</td>
						<td>';
							$i = 0;
							foreach($elem->protocol as $proto){
								if ($i != 0){
									echo ', ';
								}
								echo $allproto->$proto->name;
								$i++;
							}
						echo'
						</td>
						<td class="center">';
							if ($elem->validation == 0){
								echo '
								<button id="btn-'.$elem->daemon_id.'" title="'._('Ask validation').'" onclick="Validation(\''.$elem->daemon_id.'\')" class="btn btn-danger"><i id="icon-'.$elem->daemon_id.'" class="glyphicon glyphicon-remove"></i></button>';
							}
							else {
								echo '
								<button id="btn-'.$elem->daemon_id.'" title="'._('Ask validation').'" onclick="Validation(\''.$elem->daemon_id.'\')" class="btn btn-greenleaf"><i id="icon-'.$elem->daemon_id.'" class="glyphicon glyphicon-ok"></i></button>';
							}
						echo '
						</td>
						<td class="center">
							<button type="button" title="'._('Edit').'" class="btn btn-primary" onclick="PopupRenameDaemon('.$elem->daemon_id.')">
								<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
							</button>
							<button type="button" title="'._('Delete').'" class="btn btn-danger" onclick="PopupDeleteDaemon('.$elem->daemon_id.')">
								<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
							</button>
						</td>
					</tr>';
				}
			echo '
			</tbody>
		</table>';
	}
	else{
		echo '
		<div class="col-xs-12"><br/><div class="alert alert-warning center" role="alert">
			<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			<span class="sr-only">Error:</span>
			'._('No Box. Please add a box').'
		</div>';
	}
echo '
</div>
		
<script type="text/javascript">

function Validation(iddaemon){

	$("#btn-"+iddaemon).attr("class", $("#btn-"+iddaemon).attr("class")+" m-progress");
		
	$.ajax({
		type:"GET",
		url: "form/form_daemon_send_validation.php",
		data: "iddaemon="+iddaemon,
		timeout: 5000,
		success: function(result) {
			if (result == 1){
				setTimeout(function(){
					$("#btn-"+iddaemon).attr("class", "btn btn-greenleaf");
					$("#icon-"+iddaemon).attr("class", "glyphicon glyphicon-ok");
				}, 2000);
			}
			else {
				setTimeout(function(){
					$("#btn-"+iddaemon).attr("class", "btn btn-danger");
					$("#icon-"+iddaemon).attr("class", "glyphicon glyphicon-remove");
				}, 2000);
			}	
		},
		error: function(result, status){
			$("#btn-"+iddaemon).attr("class", "btn btn-danger");
			$("#icon-"+iddaemon).attr("class", "glyphicon glyphicon-remove");
		}
	});
}

function ListDaemon(){
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_conf_new_daemon.php",
			success: function(result) {
				$("#tableinfo").html(result);				
			}
		});
}

function AutoFill(name){
	if (name != \'\'){
		$("#newserial").val(name);
		if ($("#newdaemon").val() == \'\'){
			$("#newdaemon").val(name)
		}
	}			
}	
					
function PopupNewDaemon(){
	
	var ajaxcall = $.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_new_daemon.php",
		success: function(msg) {
			ListDaemon();
			BootstrapDialog.show({
				title: "'._('New Daemon').'",
			message: msg
		});
		}
	});
}

function PopupRenameDaemon(id){
		$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_rename_daemon.php",
		data: "id="+id,
		success: function(msg) {
			BootstrapDialog.show({
				title: "'._('Rename Daemon').'",
			message: msg
			});
		}
	});
}
						
function PopupDeleteDaemon(id){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_delete_daemon.php",
		data: "id="+id,
		success: function(msg) {
			BootstrapDialog.show({
				title: "'._('Delete Daemon').'",
				message: msg
			});
		}
	});
}

function DeleteDaemon(id){
	$.ajax({
			type:"GET",
			url: "/form/form_daemon_remove.php",
			data: "id="+id,
			complete: function(result, status) {
				location.href=\'/conf_daemon\'
			}
		});
}
						
function NewDaemon(){
	var name = $("#newdaemon").val();
	var serial = $("#newserial").val();
	var skey = $("#newsercretkey").val();
						
	if (name != \'\' && serial != \'\' && skey != \'\'){
		$.ajax({
			type:"GET",
			url: "/form/form_daemon_new.php",
			data: "name="+encodeURIComponent(name)+"&serial="+encodeURIComponent(serial)+"&skey="+encodeURIComponent(skey),
			complete: function(result, status) {
				location.href=\'/conf_daemon\'
			}
		});
	}
	else {
		$("#signerr").show("slow");
	}
}						
						
function	RenameDaemon(id){
						
	$("#btn-"+id).attr("class", "btn btn-danger");
	$("#icon-"+id).attr("class", "glyphicon glyphicon-remove");
				
	var name = $("#redaemon").val();
	var serial = $("#reserial").val();
	var skey = $("#resercretkey").val();
	var proto = [];
	$(".checkbox-daemon").each(function(index){
		if ($(this).prop(\'checked\')){
			proto.push($(this).val());
		}
	
	});

	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/form/form_conf_daemon_rename.php",
		data: "id="+id+"&name="+encodeURIComponent(name)+"&serial="+encodeURIComponent(serial)+"&skey="+encodeURIComponent(skey)+"&proto="+proto.join(\'_\'),
		complete: function(result, status) {
			location.href=\'/conf_daemon\'
		}
	});
}

$("#rcv").val(\'\');
				
</script>';
		
?>