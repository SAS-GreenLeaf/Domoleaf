<?php 

include('header.php');

if (!empty($_GET['iddevice'])){
	
	$request = new Api();
	$request->send_request();
	$result = $request->send_request();
	
	
	echo '<div class="center">';
	
	//if option 'type' actived
	echo '<div class="btn-group">';
	echo '<button onclick="" class="btn btn-info">'._('Confort').'</button>';
	echo '<button onclick="" class="btn btn-info">'._('Nuit').'</button>';
	echo '<button onclick="" class="btn btn-info">'._('Eco').'</button>';
	echo '<button onclick="" class="btn btn-info">'._('Hors Gel').'</button>';
	echo '</div>';
	
	//if fan > 0
	echo '<br/><br/><b>'._('Fans').'</b><br/>';
	
	//if 1 fan
	echo '<button onclick="" class="btn btn-info">'._('1').'</button> ';
	//if 2 fans
	echo '<button onclick="" class="btn btn-info">'._('2').'</button> ';
	//if 3 fan
	echo '<button onclick="" class="btn btn-info">'._('3').'</button> ';
	//if 4 fan
	echo '<button onclick="" class="btn btn-info">'._('4').'</button> ';
	//if x fans...
		
	 echo '</div>'.
		'</div>
		<div class="center">'.
			'<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
		'</div>';
}

?>