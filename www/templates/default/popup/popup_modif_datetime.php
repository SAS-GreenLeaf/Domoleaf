<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

$curr_day = date('j');
$curr_month = date('n');
$curr_year = date('Y');
$curr_hour = date('G');
$curr_minute = intval(date('i'));

echo '<div class="center">'._('Warning if the box is connected to Internet the date will be daily updated').'';
echo '<div id="error_modif_datetime"></div>';

echo '
<div class="control-group">
	<div class="row">'.
		'<div class="col-xs-6 col-sm-4">'.
			'<label class="control-label" for="days">'._('Day').'</label>
			<select id="days" name="daysSelector" class="form-control">';
				for ($day=1; $day < 32; $day++){
					if ($day == $curr_day){
						echo '<option value="'.$day.'" selected="selected">'.str_pad($day, 2, '0', STR_PAD_LEFT).'</option>';							
					}
					else
						echo '<option value="'.$day.'">'.str_pad($day, 2, '0', STR_PAD_LEFT).'</option>';					
				}
				echo
			'</select>'.
		'</div>'.
		'<div class="col-xs-6 col-sm-4">'.
			'<label class="control-label" for="months">'._('Month').'</label>
			<select id="months" name="monthsSelector" class="form-control selectpicker ">';
				for ($month=1; $month < 13; $month++){
					if ($month == $curr_month){
						echo '<option value="'.$month.'" selected="selected">'.str_pad($month, 2, '0', STR_PAD_LEFT).'</option>';
					}
					else
						echo '<option value="'.$month.'">'.str_pad($month, 2, '0', STR_PAD_LEFT).'</option>';
				}
				echo
			'</select>'.
		'</div>'.
		'<div class="col-xs-6 col-xs-offset-3 col-sm-4 col-sm-offset-0">'.
			'<label class="control-label" for="year">'._('Year').'</label>'.
			'<input id="years" name="yearsSelector" title="'._('Date').'" type="number" min="1970" value="'. $curr_year .'" class="form-control">'.
		'</div>'.
	'</div>
	<div class="row">'.
		'<div class="col-sm-5 col-sm-offset-1 col-xs-6 ">'.
			'<label class="control-label" for="hours">'._('Hour').'</label>
			<select id="hours" name="hoursSelector" class="form-control selectpicker ">';
				for ($hour=0; $hour < 24; $hour++){
					if ($hour == $curr_hour){
						echo '<option value="'.$hour.'" selected="selected">'.str_pad($hour, 2, '0', STR_PAD_LEFT).'</option>';
					}
					else
						echo '<option value="'.$hour.'">'.str_pad($hour, 2, '0', STR_PAD_LEFT).'</option>';							
				}
				echo
			'</select>'.
		'</div>'.
		'<div class="col-sm-5 col-xs-6">'.
			'<label class="control-label" for="minutes">'._('Minute').'</label>
			<select id="minutes" name="minutesSelector" class="form-control selectpicker ">';
				for ($minute=0; $minute < 60; $minute++){
					if ($minute == $curr_minute){
						echo '<option value="'.$minute.'" selected="selected">'.str_pad($minute, 2, '0', STR_PAD_LEFT).'</option>';
					}
					else
						echo '<option value="'.$minute.'">'.str_pad($minute, 2, '0', STR_PAD_LEFT).'</option>';														
				}
				echo
			'</select>'.
		'</div>'.
	'</div>'.
'</div>'.
'<br/><br/>
<div class="controls center">'.
	'<button onclick="ChangeDateTime()" id="Save" class="btn btn-success">'._('Save').' <span class="glyphicon glyphicon-ok"></span></button>'.
	'<button onclick="popup_close()" id="Cancel" class="btn btn-danger">'._('Cancel').' <span class="glyphicon glyphicon-remove"></span></button>'.
'</div>'.

'<script type="text/javascript">'.
	'$(document).ready(function(){'.
		'$("#popupTitle").html("'._("Configure date and time").'");'.
	'});'.
	
	'function ChangeDateTime(){'.
		'var dayval = $("#days").val();'.
		'var monthval = $("#months").val();'.
		'var yearval = $("#years").val();'.
		'var hourval = $("#hours").val();'.
		'var minuteval = $("#minutes").val();'.
		'$.ajax({'.
			'type:"GET",'.
			'url: "/templates/'.TEMPLATE.'/form/form_general_date_time.php",'.
			'data: "dayval="+dayval+"&monthval="+monthval+"&yearval="+yearval+"&hourval="+hourval+"&minuteval="+minuteval,'.
			'success: function(result){'.
				'popup_close();'.
				'location.reload();'.
			'}'.
		'});'.
	'}'.
'</script>';
?>