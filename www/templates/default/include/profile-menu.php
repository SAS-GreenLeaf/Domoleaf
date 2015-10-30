<?php 

echo '<div class="col-xs-2 bhoechie-tab-menu sidebar">
			<div class="list-group">
				<a title="'._('Settings').'" href="/profile" id="menu-settings" class="list-group-item text-center">
					<h4 class="fa fa-cog lg"></h4><br/><span class="hidden-xs">'._('Settings').'</span>
				</a>
				<a title="'._('Installation').'" href="/profile_user_installation" id="menu-installation" class="list-group-item text-center">
					<h4 class="fa fa-server lg"></h4><br/><span class="hidden-xs">'._('Installation').'</span>
				</a>
				<a title="'._('Smartcommands & Scenarios').'" href="#" onclick="ShowScenarios()" class="list-group-item text-center change">
					<h4 class="fi flaticon-playbutton17 lg"></h4><span class="hidden-xs">'._('Smartcommands & Scenarios').'</span>
				</a>
				<ul id="dropdownScenarios" class="nav nav-sidebar" hidden role="menu">
					<li>
						<a id="menu-smartcmds" class="list-group-item text-center" href="/profile_user_smartcmd">'._('Smartcommands').'</a>
					</li>
					<li>
						<a id="menu-scenarios" class="list-group-item text-center" href="/profile_user_scenarios">'._('Scenarios').'</a>
					</li>
					<li>
						<a id="menu-triggers" class="list-group-item text-center" href="/profile_user_trigger_events">'._('Triggers').'</a>
					</li>
					<li>
						<a id="menu-schedules" class="list-group-item text-center" href="/profile_user_trigger_schedules">'._('Schedules').'</a>
					</li>
				</ul>
			</div>
		</div>';

echo '<script type="text/javascript">

$(".change").click(function() {
	$(".bhoechie-tab-menu .list-group a").removeClass("active");
	$(this).addClass("active");
});

function ShowScenarios(){
	$("#dropdownScenarios").toggle("slow");
}

</script>';

?>