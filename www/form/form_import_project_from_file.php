<?php
  include ('header.php');

  $request = new Api();
  $request -> add_request('importProjectFromFile', array($_POST['content']));
  $result = $request -> send_request();
?>
