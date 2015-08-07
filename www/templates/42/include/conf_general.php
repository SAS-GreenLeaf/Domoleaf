<?php

include('configuration-menu.php');

echo '
<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">
	<div class="center"><h2>'._('General configuration').'</h2></div><br/><br/>

	<div class="col-xs-12 col-md-6">
		<b>'._('Version').'</b><br/>
		'._('Current:').' '.$mastersversion.'
	</div>
	<div class="col-xs-12 col-md-6">
		<input id="checkboxaccess" type="checkbox"> '._('Activate remote access').'
		<div id="remoteaccess">
			<div class="control-group">
				<label class="control-label" for="http">'._('Remote access port').'</label>
				<input id="http" name="http" min="0" max="65535" title="'._('Remote access port').'" type="number" value="'.$httpport.'" class="form-control">
			</div>
			<div class="control-group">
				<label class="control-label" for="https">'._('Secure remote access port').'</label>
				<input id="https" name="https" min="0" max="65535" title="'._('Secure emote access port').'" type="number" value="'.$httpsport.'" class="form-control">
			</div>
		</div>
		<div class="clearfix"></div>
		<input id="securemode" type="checkbox"> '._('Force securised mode').'
		<div class="center">
			<button type="button" class="btn btn-greenleaf" onclick="SaveChange()">'._('Save').'</button>
		</div>
	</div>
</div>
					
<script type="text/javascript">
	
	$("#checkboxaccess").click(function () {
		if($(this).is(":checked")){
			$("#remoteaccess").show("slow");
			if ($("#http").val() == "0" || $("#http").val() == ""){
				$("#http").val("6980");
			}
			if ($("#https").val() == "0" || $("#https").val() == ""){
				$("https").val("6924");
			}
		}
		else {
			$("#remoteaccess").hide("slow");
		}
	});';
	if (empty($httpport) && empty($httpsport)){
		echo '$("#remoteaccess").hide()';
	}
echo'

function SaveChange(){
	var httpval = $("#http").val();
	var httpsval = $("#https").val();
	var securemode = $("#securemode").prop("checked") ? 1 : 0;
	var checkboxaccess = $("#checkboxaccess").prop("checked") ? 1 : 0;

	if (checkboxaccess == 0){
		httpval = 0;
		httpsval = 0;
	}
			
	$.ajax({
			type:"GET",
			url: "/form/form_general.php",
			data: "httpval="+httpval+"&httpsval="+httpsval+"&securemode="+securemode,
			complete: function(result, status) {
				
			}
		});
}

</script>';

?>