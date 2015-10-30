<?php

function showtimeSelect($months, $weekdays, $days, $hours, $mins) {
	$display =display_scripts();
	$display.= display_months($months);
	$display.='<hr>';
	$display.=display_weekdays($weekdays);
	$display.='<hr>';
	$display.=display_days($days);
	$display.='<hr>';
	$display.=display_hours($hours);
	$display.='<hr>';
	$display.=display_mins($mins);
	$display.=
				'<script type="text/javascript">
					monthSize();
					weekdaySize();
					$(".resetSaveBtn").bootstrapSwitch(\'onSwitchChange\', function(){
						changeSaveBtnState("#saveTS_btn", 0);
					});
				</script>';
	return $display;
}

function display_months($months) {
	$all_activate = (array_sum($months) == 12) ? 1 : 0;
	$display =
	'<div id="months">
		'._('Months').'
		<div id="MonthsBtn" class="triggerScheduleBtns">
			<input data-on-color="greenleaf"
			       data-off-color="error"
			       data-label-width="0"
			       data-on-text="'._('All&nbspyear').'"
			       data-off-text="'._('All&nbspyear').'"
			       type="checkbox"
			       id="toggleMonthList"
			       class="resetSaveBtn"
			       onchange="showMonthsList()" ';
			       if ($all_activate == 1) {
			       		$display.='checked';
			       }
			       $display.='>';
	$display.=display_toogle_all_btn("Months");
	$display.='
		</div>
		<div hidden class="MonthsListAndBtn">
			<ul id="monthsList">';
	for($i = 0 ; $i < 12; $i++) {
		$day = strftime('%B', strtotime(date('Y').'-'.($i+1).'-01'));
		$display.=
				'<li class="triggerScheduleElems checkbox-inline">
					<input data-on-color="greenleaf"
					       data-off-color="error"
					       data-label-width="0"
					       data-on-text="'.$day.'"
					       data-off-text="'.$day.'"
					       id="month-'.$i.'"
					       type="checkbox"
					       onchange=""
					       class="monthElemWidth resetSaveBtn" ';
					       if ($months[$i] == 1) {
					       		$display.='checked';
					       }
					       $display.='>
				</li>';
		$display.=
				'<script type="text/javascript">
					$("#month-'.$i.'").bootstrapSwitch();
				</script>';
	}
	$display.='
			</ul>
		</div>
	</div>';
	
	$display.=
	'<script type="text/javascript">
		activateMonthSwitch('.$all_activate.');
		
		function activateMonthSwitch(all_activate) {
			$("#toggleMonthList").bootstrapSwitch();
			if ('.$all_activate.' == 0) {
				$("#toggleMonthList").bootstrapSwitch(\'state\', false, true);
			}
		}
	</script>';

	return $display;
}

function display_weekdays($weekdays) {
	$all_activate = (array_sum($weekdays) == 7) ? 1 : 0;
	$display =
	'<div id="weekdays">
		'._('Weekdays').'
		<div id="WeekdaysBtn" class="triggerScheduleBtns">
			<input data-on-color="greenleaf"
			       data-off-color="error"
			       data-label-width="0"
			       data-on-text="'._('All&nbspweek').'"
			       data-off-text="'._('All&nbspweek').'"
			       type="checkbox"
			       id="toggleWeekdayList"
			       class="resetSaveBtn"
			       onchange="showWeekdaysList()" ';
			       if ($all_activate == 1) {
			       		$display.='checked';
			       }
			       $display.='>';
	$display.=display_toogle_all_btn("Weekdays");
	$display.='
		</div>
		<div hidden class="WeekdaysListAndBtn">
			<ul id="weekdaysList">';
	for($i = 0 ; $i < 7; $i++) {
		$day = strftime('%A', strtotime( 'next Monday +' . $i . ' days' ));
		$display.=
				'<li class="triggerScheduleElems checkbox-inline">
					<input data-on-color="greenleaf"
					       data-off-color="error"
					       data-label-width="0"
					       data-handle-width="auto"
					       data-on-text="'.$day.'"
					       data-off-text="'.$day.'"
					       id="weekday-'.$i.'"
					       type="checkbox"
					       class="weekdayElemWidth resetSaveBtn" ';
					       if ($weekdays[$i] == 1) {
					       		$display.='checked';
					       }
					       $display.='>
				</li>';
		$display.=
				'<script type="text/javascript">
					$("#weekday-'.$i.'").bootstrapSwitch();
				</script>';
	}
	$display.='
			</ul>
		</div>
	</div>';

	$display.=
	'<script type="text/javascript">
		activateWeekdaySwitch('.$all_activate.');
		
		function activateWeekdaySwitch(all_activate) {
			$("#toggleWeekdayList").bootstrapSwitch();
			if ('.$all_activate.' == 0) {
				$("#toggleWeekdayList").bootstrapSwitch(\'state\', false, true);
			}
		}
	</script>';
	
	return $display;
}

function display_days($days) {
	$all_activate = (array_sum($days) == 31) ? 1 : 0;
	$display =
	'<div id="days">
		'._('Days').'
		<div id="DaysBtn" class="triggerScheduleBtns">
			<input data-on-color="greenleaf"
			       data-off-color="error"
			       data-label-width="0"
			       data-on-text="'._('All&nbspdays').'"
			       data-off-text="'._('All&nbspdays').'"
			       type="checkbox"
			       id="toggleDayList"
			       class="resetSaveBtn"
			       onchange="showDaysList()" ';
			       if ($all_activate) {
			       		$display.='checked';
			       }
			       $display.='>';
	$display.=display_toogle_all_btn("Days");
	$display.='
		</div>
		<div hidden class="DaysListAndBtn">
			<ul id="daysList">';
	for($i = 1 ; $i <= 31; $i++) {
		if ($i < 10) {
			$j = '0'.$i;
		}
		else {
			$j = $i;
		}
		$display.=
				'<li class="triggerScheduleElems checkbox-inline">
					<input data-on-color="greenleaf"
					       data-off-color="error"
					       data-label-width="0"
					       data-on-text="'.$j.'"
					       data-off-text="'.$j.'"
					       id="day-'.$i.'"
					       type="checkbox"
					       class="dayElemWidth resetSaveBtn" ';
					       if ($days[$i-1] == 1) {
					       		$display.='checked';
					       }
					       $display.='>
				</li>';
		$display.=
				'<script type="text/javascript">
					$("#day-'.$i.'").bootstrapSwitch();
				</script>';
	}
	$display.='
			</ul>
		</div>
	</div>';
	
	$display.=
	'<script type="text/javascript">
		activateDaySwitch('.$all_activate.');
	
		function activateDaySwitch(all_activate) {
			$("#toggleDayList").bootstrapSwitch();
			if ('.$all_activate.' == 0) {
				$("#toggleDayList").bootstrapSwitch(\'state\', false, true);
			}
		}
	</script>';

	return $display;
}

function display_hours($hours) {
	$all_activate = (array_sum($hours) == 24) ? 1 : 0;
	$display =
	'<div id="hours">
		'._('Hours').'
		<div id="HoursBtn" class="triggerScheduleBtns">
			<input data-on-color="greenleaf"
			       data-off-color="error"
			       data-label-width="0"
			       data-on-text="'._('All&nbsphours').'"
			       data-off-text="'._('All&nbsphours').'"
			       type="checkbox"
			       id="toggleHourList"
			       class="resetSaveBtn"
			       onchange="showHoursList()" ';
			       if ($all_activate == 1) {
			       		$display.='checked';
			       }
			       $display.='>';
	$display.=display_toogle_all_btn("Hours");
	$display.='
		</div>
		<div hidden class="HoursListAndBtn">
			<ul id="hoursList">';
	for($i = 0 ; $i <= 23; $i++) {
		if ($i < 10) {
			$j = '0'.$i;
		}
		else {
			$j = $i;
		}
		$display.=
				'<li class="triggerScheduleElems checkbox-inline">
					<input data-on-color="greenleaf"
					       data-off-color="error"
					       data-label-width="0"
					       data-on-text="'.$j.'"
					       data-off-text="'.$j.'"
					       id="hour-'.$i.'"
					       type="checkbox"
					       class="hourElemWidth resetSaveBtn" ';
					       if ($hours[$i] == 1) {
					       		$display.='checked';
					       }
					       $display.='>
				</li>';
		$display.=
				'<script type="text/javascript">
					$("#hour-'.$i.'").bootstrapSwitch();
				</script>';
	}
	$display.='
			</ul>
		</div>
	</div>';

	$display.=
	'<script type="text/javascript">
		activateHourSwitch('.$all_activate.');
	
		function activateHourSwitch(all_activate) {
			$("#toggleHourList").bootstrapSwitch();
			if ('.$all_activate.' == 0) {
				$("#toggleHourList").bootstrapSwitch(\'state\', false, true);
			}
		}
	</script>';
	
	return $display;
}

function display_mins($mins) {
	$all_activate = (array_sum($mins) == 60) ? 1 : 0;
	$display =
	'<div id="mins">
		'._('Mins').'
		<div id="MinsBtn" class="triggerScheduleBtns">
			<input data-on-color="greenleaf"
			       data-off-color="error"
			       data-label-width="0"
			       data-on-text="'._('All&nbspminutes').'"
			       data-off-text="'._('All&nbspminutes').'"
			       type="checkbox"
			       id="toggleMinList"
			       class="resetSaveBtn"
			       onchange="showMinsList()" ';
			       if ($all_activate == 1) {
			       		$display.='checked';
			       }
			       $display.='>';
	$display.=display_toogle_all_btn("Mins");
	$display.='
		</div>
		<div hidden class="MinsListAndBtn">
			<ul id="minsList">';
	for($i = 0 ; $i <= 59; $i++) {
		if ($i < 10) {
			$j = '0'.$i;
		}
		else {
			$j = $i;
		}
		$display.=
				'<li class="triggerScheduleElems checkbox-inline">
					<input data-on-color="greenleaf"
					       data-off-color="error"
					       data-label-width="0"
					       data-on-text="'.$j.'"
					       data-off-text="'.$j.'"
					       id="min-'.$i.'"
					       type="checkbox"
					       class="minElemWidth resetSaveBtn" ';
					       if ($mins[$i] == 1) {
					       		$display.='checked';
					       }
					       $display.='>
				</li>';
		$display.=
				'<script type="text/javascript">
					$("#min-'.$i.'").bootstrapSwitch();
				</script>';
	}
	$display.='
			</ul>
		</div>
	</div>';
	
	$display.=
	'<script type="text/javascript">
		activateMinSwitch('.$all_activate.');
	
		function activateMinSwitch(all_activate) {
			$("#toggleMinList").bootstrapSwitch();
			if ('.$all_activate.' == 0) {
				$("#toggleMinList").bootstrapSwitch(\'state\', false, true);
			}
		}
	</script>';

	return $display;
}

function  display_scripts() {
	$display=
	'<script type="text/javascript">
				function showMonthsList() {
					$(".MonthsListAndBtn").toggle("slow");
				}

				function showWeekdaysList() {
					$(".WeekdaysListAndBtn").toggle("slow");
				}

				function showDaysList() {
					$(".DaysListAndBtn").toggle("slow");
				}

				function showHoursList() {
					$(".HoursListAndBtn").toggle("slow");
				}

				function showMinsList() {
					$(".MinsListAndBtn").toggle("slow");
				}

				function monthSize(){
					$(".monthElemWidth").css("width", "auto");
					var width = 50;
					$(".monthElemWidth").each(function(index){
						if ($(this).width() > width){
							width = $(this).width();
						}
					});
					$(".monthElemWidth").bootstrapSwitch("handleWidth", (width+20)+"px");
				}

				function weekdaySize(){
					$(".weekdayElemWidth").css("width", "auto");
					var width = 50;
					$(".weekdayElemWidth").each(function(index){
						if ($(this).width() > width){
							width = $(this).width();
						}
					});
					$(".weekdayElemWidth").bootstrapSwitch("handleWidth", (width+20)+"px");
				}

				function toggleAllMonths(state) {
					if (state) {
						$(".monthElemWidth").bootstrapSwitch(\'state\', true, true);
					}
					else {
						$(".monthElemWidth").bootstrapSwitch(\'state\', false, true);
					}
				}

				function toggleAllWeekdays(state) {
					if (state) {
						$(".weekdayElemWidth").bootstrapSwitch(\'state\', true, true);
					}
					else {
						$(".weekdayElemWidth").bootstrapSwitch(\'state\', false, true);
					}
				}
		
				function toggleAllDays(state) {
					if (state) {
						$(".dayElemWidth").bootstrapSwitch(\'state\', true, true);
					}
					else {
						$(".dayElemWidth").bootstrapSwitch(\'state\', false, true);
					}
				}
		
				function toggleAllHours(state) {
					if (state) {
						$(".hourElemWidth").bootstrapSwitch(\'state\', true, true);
					}
					else {
						$(".hourElemWidth").bootstrapSwitch(\'state\', false, true);
					}
				}
		
				function toggleAllMins(state) {
					if (state) {
						$(".minElemWidth").bootstrapSwitch(\'state\', true, true);
					}
					else {
						$(".minElemWidth").bootstrapSwitch(\'state\', false, true);
					}
				}
			</script>';

	return $display;
}

function display_toogle_all_btn($id) {
	$display ='
				<div hidden class="'.$id.'ListAndBtn block-right margin-left">
					<input data-on-color="greenleaf"
					       data-off-color="error"
					       data-label-width="0"
					       data-on-text="<i class=\'fa fa-check-square-o\'></i>"
					       data-off-text="<i class=\'fa fa-check-square-o\'></i>"
					       type="checkbox"
					       id="toggleAll'.$id.'Btn"
					       class="resetSaveBtn"
					       checked>
					<script type="text/javascript">
						$("#toggleAll'.$id.'Btn").bootstrapSwitch();
						$("#toggleAll'.$id.'Btn").on(\'switchChange.bootstrapSwitch\',
													function (event, state) {
														toggleAll'.$id.'(state);
													}
												);
					</script>
				</div>';
	return $display;
}

?>