<?php 

include('profile-menu.php');

$request = new Api();
$request -> add_request('mcAllowed');
$result  =  $request -> send_request();

$listAllAllowed = $result->mcAllowed;

$floorallowed = $listAllAllowed->ListFloor;
$roomallowed = $listAllAllowed->ListRoom;
$deviceallowed = $listAllAllowed->ListDevice;

echo '<title>'._('User Graphics').'</title>'

?>