<?php
	echo '
		<div class="col-md-2 col-sm-2 col-xs-2 bhoechie-tab-menu sidebar">
		  	<div class="list-group">
				<a title="'._('General').'" href="/conf_general" class="list-group-item text-center change">
		                <h4 class="fa fa-cog lg"></h4><br/><span class="hidden-xs">'._('General').'</span>
		         </a>
				<a title="'._('Database').'" href="/conf_db" class="list-group-item text-center change">
		                <h4 class="fa fa-database lg"></h4><br/><span class="hidden-xs">'._('Database').'</span>
		         </a>
		         <a title="'._('Installation').'" href="/conf_installation" class="list-group-item text-center change">
		                <h4 class="fa fa-flask lg"></h4><br/><span class="hidden-xs">'._('Installation').'</span>
		         </a>
		         <a title="'._('Box').'" href="/conf_daemon" class="list-group-item text-center change">
		                <h4 class="fa fa-cubes lg"></h4><br/><span class="hidden-xs">'._('Box').'</span>
		         </a>
		          <a title="'._('Users').'" href="/conf_users" class="list-group-item text-center change">
		                <h4 class="fa fa-users lg"></h4><br/><span class="hidden-xs">'._('Users').'</span>
		         </a>
		          <a title="'._('Busmonitor').'" href="#" onclick="ShowBusmonitor()" class="list-group-item text-center change">
		                <h4 class="fa fa-wrench lg"></h4><br/><span class="hidden-xs">'._('Busmonitor').'</span>
		         </a>
		         <ul id="dropdown" class="nav nav-sidebar" hidden role="menu">
		                		<li>
                    				<a class="list-group-item text-center" href="/monitor_enocean">'._('Enocean').'</a>
                    			</li>
                    			<li>
                    				<a class="list-group-item text-center" href="/monitor_ip">'._('Ip').'</a>
                    			</li>
                    			<li>
                    				<a class="list-group-item text-center" href="/monitor_knx">'._('KNX').'</a>
                    			</li>
                  </ul>
			</div>
		</div>';

echo '<script type="text/javascript">

$(".change").click(function() {
	$(this).addClass("active");
});
		
function Listfloors(){
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/include/conf_floors.php",
			success: function(msg) {
				$("#conf-container").html(msg);
			}
		});
}
					
	
function ListRooms(){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/include/conf_rooms.php",
		success: function(msg) {
			$("#conf-container").html(msg);
		}
	});
}
	
function ShowBusmonitor(){
	$("#dropdown").toggle("slow");
}

</script>';

?>