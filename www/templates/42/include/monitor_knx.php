<?php 

include('configuration-menu.php');

echo '<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">';

echo '<div class="center"><h2>'._('Monitor KNX').'</h2></div>
		
		';
				echo '<div class="center">
							<div>
							<div class="btn-group btn-group-greenleaf">
							  <button type="button" id="dae" class="btn btn-greenleaf"> '._('All').'</button>
							  <button type="button" class="btn btn-greenleaf dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
							  <ul class="dropdown-menu dropdown-confgreenleaf  dropdown-menu-right">
								<li><a onclick="SwitchActionDae(-1, \'All\')">'._('All').'</a></li>
								<li><a onclick="SwitchActionDae(0, \'Unknow\')">'._('Unknow').'</a></li>
								<li role="presentation" class="divider"></li>';
								foreach ($listdae as $elem){
									if (in_array(1, $elem->protocol)){
											echo '<li><a onclick="SwitchActionDae('.$elem->daemon_id.', \''.$elem->name.'\')">'.$elem->name.'</a></li>';
									}
								}	
					echo	  '</ul>
								<input hidden id="hiddendae" value="-1" type="hidden">
							</div>
							<div class="btn-group btn-group-greenleaf">
							  <button type="button" id="launch" class="btn btn-greenleaf"> '._('Write (short)').'</button>
							  <button type="button" class="btn btn-greenleaf dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
							  <ul class="dropdown-menu dropdown-confgreenleaf  dropdown-menu-right">
								  <li><a onclick="SwitchAction(1)">'._('Write (short)').'</a></li>
								  <li><a onclick="SwitchAction(2)">'._('Write (long)').'</a></li>
								  <li><a onclick="SwitchAction(3)">'._('Read').'</a></li>
							  </ul>
								<input value="1" id="hiddenval" type="hidden">
							</div>
								  		
						<div class="btn-group btn-group-greenleaf">
							<div class="input-group">
								<label for="username" class="input-group-addon">
									<span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
								</label>
								 <input type="text" class="form-control" id="addr" placeholder="'._('KNX address').'"> 
							</div>
						</div>
						<div class="btn-group btn-group-greenleaf">
							<div class="input-group">
								<label class="input-group-addon">
									<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>
								</label>
								<input type="text" class="form-control" id="value" placeholder="'._('Value').'">
							</div>
							
					  </div>
						<button class="btn btn-greenleaf" id="btn-send" onclick="Send()">
							<span class="glyphicon glyphicon-send" aria-hidden="true"></span> '._('Send').'
						</button>
					</div>
					<br/>			';

	echo '
			<div id="monitor_knx">
			</div>
			
<script type="text/javascript">

ListKnx();
setInterval(function() { ListKnx() }, 2000);
			
function ListKnx(){
		var val = $("#hiddendae").val();
		
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_conf_monitor_knx.php",
			data: "id="+val,
			success: function(result) {
				$("#monitor_knx").html(result);				
			}
		});
}
					
function Send(){
	var type = $("#hiddenval").val();
	var addr = $("#addr").val();
	var val = $("#value").val();
	var daemon = $("#hiddendae").val();				

	if (daemon){
		$.ajax({
				type:"GET",
				url: "/form/form_send_knx.php",
				data: "type="+type+"&addr="+addr+"&value="+val+"&daemon="+daemon,
				success: function(result) {
					ListKnx();			
				}
			});
	}
	else{
		
	}
}

function SwitchActionDae(id, name){
	$("#dae").text(name);
	$("#hiddendae").val(id);
	if (id == 0 || id == -1){
		$("#btn-send").attr(\'disabled\', \'disabled\');
	}
	else{
		$("#btn-send").removeAttr(\'disabled\');
	}
	ListKnx();	
}
					
function SwitchAction(id){
		var val;
			
		if (id == 1){ 
			val="'._('Write (short)').'";
			$("#value").removeAttr(\'disabled\');
		}
		else if (id == 2) { 
			val="'._('Write (long)').'"; 
			$("#value").removeAttr(\'disabled\');
		}
		else { 
			val="'._('Read').'";
			$("#value").attr(\'disabled\', \'disabled\');
		}
		$("#hiddenval").val(id);
		$("#launch").text(val);
}

$("#btn-send").attr(\'disabled\', \'disabled\');
$("#value").removeAttr(\'disabled\');
$("#hiddendae").val(-1);			
</script>';

?>