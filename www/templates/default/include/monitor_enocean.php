<?php 

include('configuration-menu.php');

echo '<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">';

echo '<div class="center"><h2>'._('Monitor enocean').'</h2></div>';

	echo '
			<div id="monitor_Enocean">
			</div>
			
<script type="text/javascript">

$(document).ready(function(){
	ShowBusmonitor();
	activateMenuElem(\'enocean\');
	ListEnocean();
});

setInterval(function() { ListEnocean() }, 10000);
			
function ListEnocean(){
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_conf_monitor_enocean.php",
			success: function(result) {
				$("#monitor_Enocean").html(result);				
			}
		});
}
						
</script>';

?>