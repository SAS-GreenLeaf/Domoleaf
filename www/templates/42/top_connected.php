<?php 

$usrlvl = $request->getLevel(); 

echo '<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/home">'._('Master Command').'</a>
    </div>
      		
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav navbar-right">';
		if ($usrlvl != 1){
      		echo '<li><a href="/conf_general"><span class="fa fa-cogs"></span> '._('Configuration').'</a></li>';
		}
      echo  '<li><a href="/profile"><span class="glyphicon glyphicon-user"></span> '._('Profile').'</a></li>
        <li><a onclick="disconnect()" href="#"><span class="glyphicon glyphicon-log-out"></span> '._('Disconnect').'</a></li>
      </ul>
    </div>
  </div>
</nav>';

?>

