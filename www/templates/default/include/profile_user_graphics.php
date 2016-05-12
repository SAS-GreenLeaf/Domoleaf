<?php 

include('profile-menu.php');

echo '
<div class="col-xs-10 col-xs-offset-2 navbar navbar-inverse navbar-fixed-top save-navbar">
	<div id="user-graphic-menu" class="navbar-brand">
		<div class="col-lg-3 col-md-3 col-sm-6 ">
			<select class="selectpicker span2" id="selectFloor-0" data-size="10"
			        onchange="listRoomsOfFloor(0, 0)">';
if (!empty($floorallowed)){
	foreach ($floorallowed as $floor) {
		echo '<option value="'.$floor->floor_id.'">'.$floor->floor_name.'</option>';
	}
}
else{
	echo 	 '<option value="0">'._('No selectable floor').'</option>';
}

echo '
			</select>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-6 ">
			<select class="selectpicker span2" id="selectRoom-0" data-size="10"
			        onchange="listDeviceOfRoom()">
				<option value="0">'._('No room selected').'</option>
			</select>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-6 ">
			<select id="selectDevice" class="selectpicker span2" data-size="10"
			        onchange="listOptionOfDevice()">
				<option value="0">'._('No selectable device').'</option>
			</select>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-6 ">
			<select id="selectOption" class="selectpicker span2" data-size="10"
			        onchange="displayOptionChart()">
				<option value="0">'._('No selectable option').'</option>
			</select>
		</div>
	</div>
</div>
<div class="visible-*-block"><p></p>
<div class="row visible-*-block"><p></p></div>
<div class="row visible-*-block"><p></p></div>
<div class="row visible-*-block"><p></p></div>
<div class="row visible-*-block"><p></p></div>
<div class="row visible-*-block"><p></p></div>
<div class="row visible-*-block"><p></p></div>
<div class="row visible-*-block"><p></p></div>
<div class="row visible-*-block"><p></p></div>
<div class="row visible-*-block"><p></p></div>
<div class="row visible-sm-block visible-xs-block"><p></p></div>
<div class="row visible-sm-block visible-xs-block"><p></p></div>
<div class="row visible-sm-block visible-xs-block"><p></p></div>
<div class="row visible-xs-block"><p></p></div>
<div class="row visible-xs-block"><p></p></div>
<div class="row visible-xs-block"><p></p></div>
<div class="row visible-xs-block"><p></p></div>
<div class="row visible-xs-block"><p></p></div>
<div class="row visible-xs-block"><p></p></div>
<div class="row visible-xs-block"><p></p></div>
</div>
<div class="col-lg-10 col-lg-offset-2 col-md-10 col-md-offset-2
col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2" >
	<div class="row col-xs-12 center">
		<button class="col-lg-1 col-md-1 col-sm-3 col-xs-6" id="day" type="button" onclick="displayGraphDate(\''.date('Y/m/d').'\', \''.date('Y/m/d').'\')">'._('Day').'</button>
		<button class="col-lg-1 col-md-1 col-sm-3 col-xs-6" id="week" type="button" onclick="displayGraphDate(\''.date('Y/m/d', strtotime('-1 week')).'\', \''.date('Y/m/d').'\')">'._('Week').'</button>
		<button class="col-lg-1 col-md-1 col-sm-3 col-xs-6" id="month" type="button" onclick="displayGraphDate(\''.date('Y/m/d', strtotime('-1 month')).'\', \''.date('Y/m/d').'\')">'._('Month').'</button>
		<button class="col-lg-1 col-md-1 col-sm-3 col-xs-6" id="year" type="button" onclick="displayGraphDate(\''.date('Y/m/d', strtotime('-1 year')).'\', \''.date('Y/m/d').'\')">'._('Year').'</button>
		<button class="col-lg-1 col-md-1 col-xs-12" id="advanced" type="button" onclick="toggleAdvancedMenu()" title="'._('Advanced').'"><span class="glyphicon glyphicon-cog"></span></button>
		<div id="advanced_datepickers">
			<label class="col-lg-1 col-lg-offset-1 col-md-1 col-sm-3 col-xs-6" for="from_date">'._('From').'</label>
			<input class="col-lg-2 col-md-2 col-sm-3 col-xs-6" data-theme="b" id="from_date" type="button" value="'.date('Y/m/d').'" 
			       onclick="openFromDatepicker()" onchange="displayOptionChart()"></button>
			<label class="col-lg-1 col-md-1 col-sm-3 col-xs-6" for="to_date">'._('To').'</label>
			<input class="col-lg-2 col-md-2 col-sm-3 col-xs-6" data-theme="b" id="to_date" type="button" value="'.date('Y/m/d').'" 
			       onclick="openToDatepicker()" onchange="displayOptionChart()"></button>
		</div>
	</div>
	<div class="clearfix"></div>
	<div id="graph" style="position: relative;">
</div>';

echo '
<script type="text/javascript">
	$( "#advanced_datepickers" ).hide();
	$( "#from_date" ).datepicker();
	$( "#to_date" ).datepicker();
	$.datepicker.setDefaults( $.datepicker.regional[ "'.$request->getLanguage().'" ] );

	$(document).ready(function(){
		activateMenuElem(\'graphics\');
		listRoomsOfFloor(0, 0);
		setInterval(function() { displayOptionChart(); }, 120000);
	});
	
	function toggleAdvancedMenu() {
		$("#advanced_datepickers").toggle();
	}
	
	function openFromDatepicker() {
		jQuery("#from_date").datepicker("show");
	}	
	
	function openToDatepicker() {
		jQuery("#to_date").datepicker("show");
	}
	
	function displayGraphDate(begin, end) {
		$("#from_date").val(begin);
		$("#to_date").val(end);
		displayOptionChart();
	}
	
	function displayOptionChart() {
		var device_id = $("#selectDevice").val();
		var option_id = $("#selectOption").val();
		var to_dateval = $("#to_date").val();
		var from_dateval = $("#from_date").val();
		$.ajax({
			method: "GET",
			dataType: "JSON",
			url: "/templates/'.TEMPLATE.'/form/form_user_graphics.php", 
			data: "device_id="+device_id+"&option_id="+option_id+"&from_dateval="+from_dateval+"&to_dateval="+to_dateval,
			success: function(option) {
				$("#graph").empty();
				if (option) {
					var	Chart = Morris.Line({
						element: "graph",
						data: JSON.parse(option.data),
						xkey: "time",
						xLabels: option.xlabel,
						ykeys: option.ykey,
						labels:	option.ylabel,
						lineColors: option.color,
						resize: "true",
						pointSize: 0,
						postUnits: option.unit
					});
				}
			},
			error: function(e) {
				console.log(\'error displayOptionChart()\');
			}
		});
	};

</script>';

?>