<?php

include('configuration-menu.php');

echo '
<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">
	<div class="center"><h2>'._('Database configuration').'</h2></div><br/><br/>
		<div class="col-xs-12 center">
			<button type="button" class="btn btn-greenleaf" onclick="PopupUsb()">'._('USB Backup').'</button>
			<button type="button" id="creatBackupLocal"class="btn btn-greenleaf" onclick="CreateBackupLocal()"><i class="fa fa-floppy-o"></i>'._(' Create Backup').'</button>
		</div>
		<br/>
		<br/>
		<div id="listDbLocal"></div>';
	echo '

</div>
<script type="text/javascript">
			
ListDbLocal();
			
function PopupUsb(){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_conf_db_usb.php",
		success: function(result) {
			BootstrapDialog.show({
				title: "'._('Database USB').'",
				message: result
			});
		}
	});
}

function PopupRemoveDbLocal(filename){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_remove_db_local.php",
		data: "filename="+filename,
		success: function(result) {
			BootstrapDialog.show({
				title: "'._('Delete Database').'",
				message: result
			});
		}
	});
}

function ListDbLocal(){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/form/form_conf_list_db.php",
		success: function(result){
			$("#listDbLocal").html(result);
		}
})
}
			
function CreateBackupLocal(){
	LoadingButton("creatBackupLocal", 1);
	$.ajax({
		type:"GET",
		url: "form/form_create_backup_local.php",
		success: function(result){
		setTimeout(function(){ ListDbLocal() }, 5000);
		LoadingButton("creatBackupLocal", 0);
		}
})
}

function LoadingButton(id, status){
	if (status == 1){
		$("#"+id).addClass("m-progress");
	}
	else if (status == 0){
		setTimeout(function(){
			$("#"+id).removeClass("m-progress");
		}, 5000);
	}
}

function RemoveDbLocal(filename){
	$.ajax({
		type:"GET",
		url: "form/form_remove_backup_local.php",
		data: "filename="+filename+"&status=2",
		complete: function(result, status){
		}
})
}
		
function RestoreDbLocal(filename){
	$.ajax({
		type:"GET",
		url: "form/form_restore_backup_local.php",
		data: "filename="+filename+"&status=1",
		complete: function(result, status){
		}
})
}

function BackupUSB(){
	$.ajax({
		type:"GET",
		url: "form/form_backup_usb.php",
		complete: function(result, status){
		}
})
}

</script>';

?>