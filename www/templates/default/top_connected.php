<?php 

$usrlvl = $request->getLevel(); 

echo
'<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">'._('Toggle navigation').'</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/home">
				<span class="glyphicon glyphicon-home"></span>
				'._('Master Command').'
			</a>
		</div>
		<div class="navbar-collapse collapse md">
			<ul class="nav navbar-nav navbar-right">
				<li>
					<a onclick="" href="/profile_user_smartcmd">
						<span class="fi flaticon-playbutton17"></span>
						<span class="hidden-sm"> '._('Smartcommand').'</span>
					</a>
				</li>';
			if ($usrlvl != 1){
				echo
				'<li>
					<a href="/conf_general">
						<span class="fa fa-cogs"></span>
						<span class="hidden-sm"> '._('Configuration').'</span>
					</a>
				</li>';
			}
			echo
				'<li>
					<a href="/profile">
						<span class="glyphicon glyphicon-user"></span>
						<span class="hidden-sm"> '._('Profile').'</span>
					</a>
				</li>
				<li>
					<a onclick="" target="_blank" href="http://greenleaf.fr/ressources">
						<span class="glyphicon glyphicon-question-sign"></span>
						<span class="hidden-sm"> '._('Help').'<span>
					</a>
				</li>
				<li>
					<a onclick="disconnect()" href="#">
						<span class="glyphicon glyphicon-log-out"></span>
						<span class="hidden-sm"> '._('Disconnect').'</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</nav>';

?>

