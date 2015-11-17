<?php

include('header.php');

if (empty($_GET['device_id'])){
	echo '<script type="text/javascript">popup_close();</script>';
}

if (!empty($_GET['manufacturer_id']) && $_GET['manufacturer_id'] != 0){
	$request =  new Api();
	$request -> add_request('confProductList', array($_GET['device_id'], $_GET['manufacturer_id']));
	$result  =  $request -> send_request();
	
	$productList = $result->confProductList;
	
	echo '<div class="clearfix"></div>
		<br/><div class="control-group" >
			<label class="control-label" for="productList">'._('Product list').'</label>
			<select class="selectpicker form-control" id="productList" onchange="">
				<option value="0">-- '._('Nothing selected').' --</option>';
				foreach ($productList as $product){
					echo '<option value="'.$product->product_id.'">'.$product->name.'</option>';
				}
	echo 	'</select>
		</div>';
}
?>
