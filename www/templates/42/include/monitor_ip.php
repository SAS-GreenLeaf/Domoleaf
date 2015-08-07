<?php 

include('configuration-menu.php');

echo '<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">';

echo '<div class="center"><h2>'.
	_('Monitor Ip').
	 '
	 <div class="btn-group btn-group-greenleaf">
			  <button type="button" class="btn btn-warning" onclick="RefreshIp()"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></button>
	</div>
	</h2></div>';
	echo '
			<div id="monitor_ip">
			</div>
			
<script type="text/javascript">

ListIp();
setInterval(function() { ListIp() }, 10000);
			
function ListIp(){
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_conf_monitor_ip.php",
			success: function(result) {
				$("#monitor_ip").html(result);				
			}
		});
}

function RefreshIp(){
		$.ajax({
			type:"GET",
			url: "/form/form_monitor_ip_refresh.php",
		});
}
</script>';

?>