<?php 

include('configuration-menu.php');

echo '
<div class="col-md-10 col-md-offset-2 col-sm-9 col-sm-offset-3 col-xs-11 col-xs-offset-1">';
	echo '
	<div class="col-xs-12 center"><h2>'._('New device').'</h2></div>
	<div class="right"><a href="/conf_installation/'.$_GET['floor'].'/'.$_GET['room'].'" class="btn btn-danger"><span class="fa fa-reply"></span> '._('Cancel').'</a></div>
	<div class="col-xs-12"><br/>
		<div class="col-md-6 col-xs-12">
			<div class="control-group">
				<label class="control-label" for="devicename">'._('Device Name').'</label>
				<div class="input-group">
					<label for="devicename" class="input-group-addon">
						<span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
					</label>
					<input name="devicename" type="text" class="form-control" id="devicename" placeholder="'._('Enter your device name').'">
				</div>
			</div><br/>
			<div class="control-group" id="selectFloor">
				<label class="control-label" for="listfloor">'._('Floor').'</label>
				<select class="selectpicker form-control" id="listfloor" onchange="GetRoomByFloor()">
					<option value="0">'._('Choose the floor').'</option>';
					foreach($devfloorlist as $elem){
						echo '
						<option value="'.$elem->floor_id.'">'.$elem->floor_name.'</option>';
					}
				echo '
				</select>
			</div><br/>
			<div class="control-group" id="selectRoom">
				<label class="control-label" for="listroom">'._('Room').'</label>
				<select class="selectpicker form-control" id="listroom">
				</select>
			</div><br/>	
		</div>	
		<div class="col-md-6 col-xs-12">
			<div class="control-group" id="selectApp">
				<label class="control-label" for="applist">'.
					_('Application').'
				</label>
				<select class="selectpicker form-control" id="applist" onchange="GetAppList()">
					<option value="0">'._('Choose your application').'</option>';
					foreach($Applist as $elem){
						if ($elem->application_id != 7) {
							echo '
							<option value="'.$elem->application_id.'">'.$elem->name.'</option>';
						}
					}
				echo '
				</select>
			</div><br/>
			<div class="control-group" id="selectDev">
				<label class="control-label" for="listdevice">'._('Device').'</label>
				<select class="selectpicker form-control" id="listdevice" onchange="GetProtoList()">
				</select>
			</div><br/>
			<div class="control-group" id="selectProto">
				<label class="control-label" for="listproto">'._('Protocol').'</label>
				<select class="selectpicker form-control" id="listproto" onchange="CheckProto()">	
				</select>
			</div><br/>
			<div class="control-group" id="selectDae">
				<label class="control-label" for="listdaemon">'.
					_('Daemon').'
				</label>
				<select class="selectpicker form-control" id="listdaemon">	
				</select>
			</div>
		</div>
	</div>
<div class="col-xs-12"><br/>
	<div id="knx">
		<div class="col-xs-6">
			<label for="knxaddr">'._('Enter the KNX address').'</label>
				<div class="input-group">
					<input name="knxaddr" type="text" class="form-control" id="knxaddr" placeholder="'._('KNX physical address').'">
				</div><br/>
				<div class="center">
					<button class="btn btn-greenleaf" onclick="AddNewDevice()"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> '._('Add').'</button><br/><br/>
				</div>
				<div class="alert alert-danger alert-hidden alert-dismissible" role="alert">
				</div>
		</div>
		<div class="col-xs-6" id="knxinfo">
		</div>
	</div>
	<div class="col-md-12" id="ip">
		<div class="col-xs-6">
			<div class="input-group">
				<label for="ipaddress" class="input-group-addon">
					<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
				</label>
				<input type="text" class="form-control" id="ipaddr" placeholder="'._('IP address or name').'">
			</div>
			<div class="input-group">
				<label for="port" class="input-group-addon">
					<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
				</label>
				<input type="text" class="form-control" id="port" placeholder="'._('Port').' ('._('Default: 80').')">
			</div>
			<div class="input-group">
				<label for="login" class="input-group-addon">
					<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
				</label>
				<input type="text" class="form-control" id="login" placeholder="'._('Login').'">
			</div>
			<div class="input-group">
				<label for="password" class="input-group-addon">
					<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
				</label>
				<input type="password" class="form-control" id="pass" placeholder="'._('Password').'">
			</div>
			<div class="input-group">
				<label for="macaddress" class="input-group-addon">
					<span class="fa flaticon-chip" aria-hidden="true"></span>
				</label>
				<input type="text" class="form-control" id="macaddr" placeholder="'._('Mac address').'">
			</div><br/>
			<div class="center"><button class="btn btn-greenleaf" onclick="AddNewDevice()"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> '._('Add').'</button></div>
			<br/>
			<div class="alert alert-danger alert-hidden alert-dismissible" role="alert">
			</div>
		</div>
		<div class="col-xs-6" id="ipinfo">
		</div>
	</div>
	<div class="col-md-6" id="enocean">
		<table>
			<tr>
				<td>
					<div class="input-group">
						<input type="text" class="form-control" id="enoceanaddr" placeholder="'._('Enocean address ').'">
					</div>
				</td>
				<td class="center">
					&nbsp;&nbsp;<button class="btn btn-greenleaf" onclick="AddNewDevice()"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> '._('Add').'</button>
				</td>
			</tr>
		</table><br/>
		<div class="alert alert-danger alert-hidden alert-dismissible" role="alert">
		</div>
	</div>
</div>

<script type="text/javascript">

$(document).ready(function(){
	activateMenuElem(\'install\');
	setTimeout(function(){
		$("#devicename").focus();
	}, 400);
});

function HideAll(){
	$("#ip").hide("slow");
	$("#knx").hide("slow");
	$("#selectDae").hide("slow");
	$("#enocean").hide("slow");
	$("#ipaddr").val("");
	$("#knxaddr").val("");
}

function CheckProto(){
	var proto = $("#listproto").val()

	if (proto == 6){
		$("#ip").show("slow");
		$("#knx").hide("slow");
		$("#selectDae").hide("slow");
		$("#enocean").hide("slow");
		GetIpList();
	}
	else if (proto == 1){
		$("#knx").show("slow");
		$("#knxaddr").val("0.0.0");
		$("#ip").hide("slow");
		$("#enocean").hide("slow");
		 GetDaemonList();
		GetKnxList();
	}
	else if (proto == 2){
		$("#enocean").show("slow");
		$("#selectDae").hide("slow");
		$("#ip").hide("slow");
		$("#knx").hide("slow");
	}
	else {
		HideAll();
	}
}

function AddNewDevice(){
	var name = $("#devicename").val();
	var floor = $("#listfloor").val();
	var room = $("#listroom").val();
	var app = $("#applist").val();
	var device = $("#listdevice").val();
	var proto = $("#listproto").val();

	if (name == \'\'){
		name = $("#listdevice option:selected").text();
	}
	if (name != \'\'){
		if (floor != 0 && room != null){
			if (proto){
				var data = "name="+name+"&floor="+floor+"&room="+room+"&app="+app+"&device="+device+"&proto="+proto;
									
				if (proto == 6){
					var ipaddr = $("#ipaddr").val();
					var port = $("#port").val();
					var login = $("#login").val();
					var pass = $("#pass").val();
					var macaddr = $("#macaddr").val();
					
					if (ipaddr != \'\'){
						SendByAjax(data+"&ipaddr="+ipaddr+"&port="+port+"&login="+login+"&pass="+pass+"&macaddr="+macaddr);
					}
					else {
						CatchError("'._('Empty ip address').'");
					}
				}
				else if (proto == 1){
					var knxaddr = $("#knxaddr").val();
					var daemon = $("#listdaemon").val();

					if (daemon){
						if (knxaddr != \'\'){
							SendByAjax(data+"&knxaddr="+knxaddr+"&daemon="+daemon);
						}
						else {
							CatchError("'._('Empty knx address').'");
						}
					}
					else {
							CatchError("'._('No daemon associate').'");
					}
				}
				else {
					var enoceanaddr = $("#enoceanaddr").val();
								
					if (enoceanaddr != \'\'){
						SendByAjax(data+"&enoceanaddr="+enoceanaddr);
					}
					else {
						CatchError("'._('Empty enocean address').'");
					}
				}
			}
			else {
				CatchError("'._('Missing protocol').'");
			}
		}
		else {
				CatchError("'._('Floor and room is required').'");
		}
	}	else {
		CatchError("'._('Empty device name').'");
	}

}

function CatchError(msg){
	$.ajax({
				type:"GET",
				url: "/templates/'.TEMPLATE.'/form/form_conf_alert_error.php",
				data: "msg="+encodeURIComponent(msg),
				success: function(result) {
					$(".alert").html(result).show("slow");
				},
				error: function(result, status, error){
					location.href=\'/conf_device_new\';
				}
			});
}

function SendByAjax(data){
	if (data != \'\'){
		$.ajax({
			type:"GET",
			url: "/form/form_device_new.php",
			data: data,
			success: function(result) {
				if (result == "1"){
					CatchError("'._('IP address/name is invalid').'");
				}
				else{
					location.href=\'/conf_installation/\'+result;
				}
			},
			error: function(result, status, error){
				location.href=\'/conf_device_new\';
			}
		});
	}
}

function GetRoomByFloor(){
	var floor = $("#listfloor").val();
	
	if (floor != 0){
		$.ajax({
				type:"GET",
				async: false,
				url: "/templates/'.TEMPLATE.'/form/form_conf_device_room.php",
				data: "floor="+floor,
				success: function(result) {
					$("#listroom").html(result);
					$(".selectpicker").selectpicker(\'refresh\');
					$("#listroom").selectpicker(\'val\', '.$_GET['room'].');
					$("#selectRoom").show();
				},
				error: function(result, status, error){
					location.href=\'/conf_device_new\';
				}
			});
	}
	else {
		$("#selectRoom").hide("slow");
	}
}

function SelectedRowKnx(addr){
	$("#knxaddr").val(addr);
}

function SelectedRowIp(host, ip, mac){
	$("#ipaddr").val(ip);
	$("#macaddr").val(mac);

	var hostval = $("#devicename").val();
	if (!hostval){
		$("#devicename").val(host);
	}
}

function GetAppList(){
			
	var app = $("#applist").val();

	if (app > 0){
		GetDeviceList(app);
		$("#selectDev").show("slow");
	}
	else{
		$("#selectDev").hide("slow");
		$("#selectProto").hide("slow");
		HideAll();
	}
}

function GetDeviceList(idapp){
	$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_conf_device_list.php",
			data: "idapp="+idapp,
			success: function(result) {
				$("#listdevice").html(result);
				$(".selectpicker").selectpicker(\'refresh\');
				GetProtoList();
			},
			error: function(result, status, error){
				location.href=\'/conf_device_new\';
			}
		});
	  
}

function GetProtoList(){
	var iddevice = $("#listdevice").val();
	
	if (iddevice != 0){
		$("#selectProto").show("slow");
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_conf_protocol.php",
			data: "iddevice="+iddevice,
			success: function(result) {
				$("#listproto").html(result);
				$(".selectpicker").selectpicker(\'refresh\');
				CheckProto();
					
				if (result.split("</option>").length > 2) {
					$("#listproto").removeAttr(\'disabled\');
				}
				else {
					$("#listproto").attr(\'disabled\', \'disabled\');
				}
				$("#listproto").show("slow");
			},
			error: function(result, status, error){
				location.href=\'/conf_device_new\';
			}
		});
	}
	else{
		$("#selectProto").hide("slow");
		$("#selectDae").hide("slow");
		HideAll();
	}
}

function GetIpList(){
	$.ajax({
				type:"GET",
				url: "/templates/'.TEMPLATE.'/form/form_conf_device_ip.php",
				success: function(result) {
					$("#ipinfo").html(result);
				},
				error: function(result, status, error){
					location.href=\'/conf_device_new\';
				}
			});
}

function GetKnxList(){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/form/form_conf_device_knx.php",
		success: function(result) {
			$("#knxinfo").html(result);
		},
		error: function(result, status, error){
			location.href=\'/conf_device_new\';
		}
		});
}
						
function GetDaemonList(){			
	$("#selectDae").show("slow");
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_conf_daemon_list.php",
			success: function(result) {
				$("#listdaemon").html(result);
				$(".selectpicker").selectpicker(\'refresh\');
			},
			error: function(result, status, error){
				location.href=\'/conf_device_new\';
			}
		});
}
$("#devicename").val("");
$("#applist").val(0);

$("#listdevice").val(0);
$("#listproto").val(0);
$("#selectProto").hide();
$("#selectDae").hide();
$("#selectRoom").hide();

$("#knx").hide();
$("#ip").hide();
$("#enocean").hide();';
	if (!empty($_GET['floor'])){
			echo '$("#listfloor").val("'.$_GET['floor'].'");
					GetRoomByFloor();';
			if (!empty($_GET['room'])){
				echo '$("#listroom").val("'.$_GET['room'].'");';
			}
			else {
				echo '$("#listroom").val(0);';
			}
		}
		else {
			echo '$("#listfloor").val(0);';
		}
echo '
</script>';

?>