jQuery(function(){
    var kKeys = [];
    function Kpress(e){
        kKeys.push(e.keyCode);
        if (kKeys.toString().indexOf("38,38,40,40,37,39,37,39,66,65") >= 0) {
            jQuery(this).unbind('keydown', Kpress);
            kExec();
        }
    }
    jQuery(document).keydown(Kpress);
});

String.prototype.replaceAt = function(index, character) {
	if (!character) {
		character = "0";
	}
	return this.substr(0, index) + character + this.substr(index+character.length);
}

function kExec(){
	alert("Draggable widget enable");
	$(".display-widget").addClass("cursor");
	$(".display-widget").draggable();
}

function ShowTimeline(id){
	$("#"+id).toggle();
}

function PopupLoading(){
	$.ajax({
		url: "/templates/default/popup/popup_loading.php",
		success: function(result){
			BootstrapDialog.show({
				closable: false,
				title: '<div id="popupLoading"></div>',
				message: result
			});
		}
	});
}

/*** Widget Audio ***/

function Volume(iddevice, optionid, step){

	var vol = $("#volume-"+iddevice).val();
	vol = parseInt(vol) + step;
	if (vol == 0 || vol > 0 && vol <= 100){
		$("#volume-"+iddevice).val(vol);
		UpdateVol(iddevice, vol);
		SetVolume(iddevice, optionid);
	}
}

function UpdateVol(iddevice, vol){
	document.querySelector("#vol-"+iddevice).value = vol+"%";
}

function SetVolume(iddevice, optionid){
	var tab = iddevice.split("-");
	var value = $("#volume-"+iddevice).val();
	
	if (tab[1]) {
		iddevice = tab[1];
	}
	$.ajax({
		type:"GET",
		url: "/form/form_mc_volume.php",
		data: "iddevice="+iddevice+"&val="+value+"&opt="+optionid,
		complete: function(result, status) {
		}
	});
}

function RemoteAudio(action, iddevice, optionid){
	if (iddevice != ""){
		$.ajax({
			type:"GET",
			url: "form/form_mc_audio.php",
			data: "iddevice="+iddevice+"&action="+action+"&optionid="+optionid,
			complete: function(result, status) {
			}
		});
	}
}

/*** Widget on/off varie ***/

function Variation(iddevice, optionid, step){
	var varie = $("#slider-value-"+iddevice).val();

	varie = parseInt(varie) + step;
	if (varie == 0 || varie > 0 && varie <= 255){
		$("#slider-value-"+iddevice).val(varie);
		outputUpdate(iddevice, varie);
		getVariation(iddevice, optionid);
	}
}
		
function outputUpdate(iddevice, val) {
	val = Math.round((parseInt(val)*100)/255);
	$("#range-"+iddevice).html(val+"%");
}
		
function onOffToggle(iddevice, optionid, popup = 0){
	var value;
	if (popup == 0){
		value = $("#onoff-"+iddevice).prop("checked") ? 1 : 0;
	}
	else{
		value = $("#onoff-popup-"+iddevice).prop("checked") ? 1 : 0;
	}
	onOff(iddevice, value, optionid);
}
		
function onOff(iddevice, value, optionid){
	
	$.ajax({
		type:"GET",
		url: "/form/form_mc_on_off.php",
		data: "iddevice="+iddevice+"&value="+value+"&optionid="+optionid,
		complete: function(result, status) {
		}
	});
}

function getVariation(iddevice, optionid){
	var value = $("#slider-value-"+iddevice).val();
	
	$.ajax({
		type:"GET",
		url: "/form/form_mc_varie.php",
		data: "iddevice="+iddevice
				+"&val="+value
				+"&optionid="+optionid,
		complete: function(result, status) {
		
		}
	});
}

function UpdateTemp(iddevice, idoption, action, popup = 0){
	
	$.ajax({
		type:"GET",
		url: "/templates/default/form/form_conf_temperature.php",
		data: "iddevice="+iddevice+"&idoption="+idoption+"&action="+action,
		success: function(result) {
			if (popup == 0){
				$("#output-mp-"+iddevice).html(result);
				$("#output-mp-popup-"+iddevice).html(result);
			}
			else{
				$("#output-mp-popup-"+iddevice).html(result);
			}
		},
		error: function(result, status){
			
		}
	});
}

function updateRGBColor(iddevice, value){
	$.ajax({
		type:"GET",
		url: "/form/form_mc_rgb_color.php",
		data: "iddevice="+iddevice+"&value="+encodeURIComponent(value),
		success: function(result) {
		},
	});
}

function changeSpeedFan(iddevice, value, optionid){
	if (optionid == 0){
		optionid = $("#speed-fan").val();
	}

	$.ajax({
		type:"GET",
		url: "/form/form_mc_change_speed_fan.php",
		data: "iddevice="+iddevice+"&value="+value+"&optionid="+optionid,
		complete: function(result, status) {
		}
	});
}

/*** Widget return ***/

function WidgetReturn(iddevice, roomdeviceid, idopt, val){
	var lamp_device = ["3", "4", "6", "55", "56", "57"];
	
	if (idopt == 12){
		if (val.valeur > 0){
			$("#onoff-"+roomdeviceid).removeAttr("onchange");
			$("#onoff-"+roomdeviceid).prop("checked", true).change();
			$("#onoff-"+roomdeviceid).attr("onchange", "onOffToggle(\'"+roomdeviceid+"\', \'"+idopt+"\')");
			if ($.inArray(val.device_id, lamp_device) != -1){
				$("#image-widget-"+roomdeviceid).addClass("on-light");
			}
		}
		else {
			$("#onoff-"+roomdeviceid).removeAttr("onchange");
			$("#onoff-"+roomdeviceid).prop("checked", false).change();
			$("#onoff-"+roomdeviceid).attr("onchange", "onOffToggle(\'"+roomdeviceid+"\', \'"+idopt+"\')");
			if ($.inArray(val.device_id,lamp_device) != -1){
				$("#image-widget-"+roomdeviceid).removeClass("on-light");
			}
		}
	}
	else if (idopt == 13){
		if (val.valeur >= 0 && val.valeur < 256){
			$("#slider-value-"+roomdeviceid).removeAttr("onchange");
			outputUpdate(roomdeviceid, val.valeur);
			$("#slider-value-"+roomdeviceid).val(val.valeur);
			$("#slider-value-"+roomdeviceid).attr("onchange", "getVariation(\'"+roomdeviceid+"\', \'"+idopt+"\')");
		}
	}
	else if (idopt == 72){
		$("#widget-"+roomdeviceid+"-"+idopt).text(val.valeur);
	}
	else if (idopt == 79){
		$("#widget-"+roomdeviceid+"-"+idopt).text(val.valeur);
	}
	else if (idopt == 6){
		$("#widget-"+roomdeviceid+"-"+idopt).text(val.valeur);
	}
	else if (idopt == 388){
		$("#output-mp-"+roomdeviceid).html(val.valeur);
	}
	else if (idopt == 392){
		var current_color = getElemHexaColor("#icon-image-widget-"+roomdeviceid);
		var red = parseInt((val.valeur)).toString(16);
		current_color = current_color.replaceAt(1, red[0]);
		current_color = current_color.replaceAt(2, red[1]);
		$("#icon-image-widget-"+roomdeviceid).css("color", current_color);
	}
	else if (idopt == 393){
		var current_color = getElemHexaColor("#icon-image-widget-"+roomdeviceid);
		var green = parseInt((val.valeur)).toString(16);
		current_color = current_color.replaceAt(3, green[0]);
		current_color = current_color.replaceAt(4, green[1]);
		$("#icon-image-widget-"+roomdeviceid).css("color", current_color);
	}
	else if (idopt == 394){
		var current_color = getElemHexaColor("#icon-image-widget-"+roomdeviceid);
		var blue = parseInt((val.valeur)).toString(16);
		current_color = current_color.replaceAt(5, blue[0]);
		current_color = current_color.replaceAt(6, blue[1]);
		$("#icon-image-widget-"+roomdeviceid).css("color", current_color);
	}
}

/*** Custom Configuration ***/

function CustomPopup(type, iddevice, userid){
	$.ajax({
		type:"GET",
		url: "/templates/default/popup/popup_custom_device.php",
		data: "iddevice="+iddevice+"&userid="+userid,
		success: function(msg) {
			BootstrapDialog.show({
				title: '<div id="popupTitle" class="center"></div>',
				message: msg
			});
		}
	});
}

/*** Smartcommand ***/

function createSmartcmd(id_scenario) {
	$.ajax({
		type: "GET",
		url: "/templates/default/popup/popup_user_create_smartcmd.php",
		data: "id_scenario="+id_scenario,
		success: function(msg) {
			BootstrapDialog.show({
				title: '<div id="popupTitle" class="center"></div>',
				message: msg
			});
		}
	});
}

function smartcmdOnOff(room_id_device) {
	var val;
	
	val = $("#smartcmdOnOff-"+room_id_device).bootstrapSwitch('state');
	if (val) {
		val = "1";
	}
	else {
		val = "0";
	}
	$("#smartcmdPopupValue-"+room_id_device).val(val);
}

function smartcmdVarie(room_id_device) {
	var val;
	
	val = $("#slider-value-"+room_id_device).val();
	$("#smartcmdPopupValue-"+room_id_device).val(val);
}

function smartcmdVolume(room_id_device) {
	var val;
	
	val = $("#volume-"+room_id_device).val();
	$("#smartcmdPopupValue-"+room_id_device).val(val);
}

function smartcmdUpdateTemp(room_id_device, modif) {
	var val;
	
	val = parseInt($("#temp-"+room_id_device).val()) + modif;
	$("#smartcmdPopupValue-"+room_id_device).val(val);
	$("#temp-"+room_id_device).val(val);
	$("#output-temp-"+room_id_device).html(val);
}

function smartcmdUpdateRGBColor(room_id_device, val) {
	$("#smartcmdPopupValue-"+room_id_device).val(encodeURIComponent(val));
}

function saveSmartcmdOption(id_smartcmd, room_id_device, id_option, id_exec, modif) {
	var val;

	val = $("#smartcmdPopupValue-"+room_id_device).val();
	$.ajax({
			type: "GET",
			url: "/templates/default/form/form_save_smartcmd_elem.php",
			data: "id_smartcmd="+id_smartcmd
					+"&room_id_device="+room_id_device
					+"&id_option="+id_option
					+"&option_value="+val
					+"&id_exec="+id_exec
					+"&time_lapse="+0
					+"&modif="+modif,
			success: function(result) {
				popup_close();
				displaySmartcmd(id_smartcmd);
			}
		});
}

function saveSmartcmdWithoutParam(id_smartcmd, room_id_device, id_option, id_exec) {
	var val;

	if (id_option == 363) {
		val = 'play';
	}
	if (id_option == 364) {
		val = 'pause';
	}
	if (id_option == 365) {
		val = 0;
	}
	if (id_option == 366) {
		val = 'next';
	}
	if (id_option == 367) {
		val = 'prev';
	}
	if (id_option == 368) {
		val = 'mute';
	}
	$.ajax({
			type: "GET",
			url: "/templates/default/form/form_save_smartcmd_elem.php",
			data: "id_smartcmd="+id_smartcmd
					+"&room_id_device="+room_id_device
					+"&id_option="+id_option
					+"&option_value="+val
					+"&id_exec="+id_exec
					+"&time_lapse="+0
					+"&modif="+1,
			success: function(result) {
				displaySmartcmd(id_smartcmd);
			}
		});
}

function selectDelay(smartcmd_id, exec_id) {
	
	var hours;
	var minutes;
	var seconds;
	var delay;
	
	hours = parseInt($("#selectHours-"+exec_id).val());
	minutes = parseInt($("#selectMinutes-"+exec_id).val());
	seconds = parseInt($("#selectSeconds-"+exec_id).val());
	
	delay = hours * 3600;
	delay = delay + minutes * 60;
	delay = delay + seconds;
	
	$.ajax({
		type: "GET",
		url: "/templates/default/form/form_smartcmd_update_delay.php",
		data: "smartcmd_id="+smartcmd_id
				+"&exec_id="+exec_id
				+"&delay="+delay,
		success: function(result) {
		}
	});
}

function dropZoneAnimate(trigger = 0) {
	if (trigger == 0) {
		$(".smartcmdElemDrop").addClass("drop-zone-activate", "100");
	}
	else {
		$(".triggerElemDrop").addClass("drop-zone-activate", "100");
	}
}

function dropZoneStop(trigger = 0) {
	if (trigger == 0) {
		$(".smartcmdElemDrop").removeClass("drop-zone-activate");
	}
	else {
		$(".triggerElemDrop").removeClass("drop-zone-activate");
	}
}

function listRoomsOfFloor(elem_id, smartcmd = 0) {
	var floor_id;
	
	floor_id = parseInt($("#selectFloor-"+elem_id).val());
	$.ajax({
		type: "GET",
		url: "/templates/default/form/form_list_rooms_of_floor.php",
		data: "floor_id="+floor_id,
		success: function(result) {
			if (result) {
				$("#selectRoom-"+elem_id).html(result);
				$('.selectpicker').selectpicker('refresh');
			}
		}
	});
	if (smartcmd == 1) {
		changeSaveBtnState("#saveLR_btn");
	}
}

function listDevicesOfRoom(elem_id) {
	var floor_id;
	var room_id;
	
	floor_id = parseInt($("#selectFloor-"+elem_id).val());
	room_id = parseInt($("#selectRoom-"+elem_id).val());
	$.ajax({
		type: "GET",
		url: "/templates/default/form/form_list_devices_of_room.php",
		data: "floor_id="+floor_id+"&room_id="+room_id,
		success: function(result) {
			if (result) {
				$("#selectDevice-"+elem_id).html(result);
				$('.selectpicker').selectpicker('refresh');
			}
		}
	});
}

function saveLinkedRoom(smartcmd_id) {
	var room_id;
	
	room_id = parseInt($("#selectRoom-"+smartcmd_id).val());
	$.ajax({
		type: "GET",
		url: "/templates/default/form/form_save_linked_room.php",
		data: "smartcmd_id="+smartcmd_id+"&room_id="+room_id,
		success: function(result) {
			if (result == 0) {
				changeSaveBtnState("#saveLR_btn", 1);
			}
			else {
				changeSaveBtnState("#saveLR_btn", 2);
			}
			
		}
	});
}

function launchSmartcmd(smartcmd_id){
	
	$.ajax({
		type:"GET",
		url: "/form/form_mc_smartcmd.php",
		data: "smartcmd_id="+smartcmd_id,
		complete: function(result, status) {
		}
	});
}

/*** Smartcommands and Triggers ***/

function ShowRoomList(floor_id){
	
	if ($("#roomList-"+floor_id).hasClass("open")) {
		$("#roomList-"+floor_id).removeClass("open");
		$("#roomList-"+floor_id+" ul.open").toggle("slow");
		$("#roomList-"+floor_id+" ul.open").removeClass("open");
		$("li div.active").removeClass("active");
		$("#floor-"+floor_id+" #arrow-icon").removeClass("fa-caret-up");
		$("#floor-"+floor_id+" #arrow-icon").addClass("fa-caret-down");
	}
	else {
		$("#roomList-"+floor_id).addClass("open");
		$("#floor-"+floor_id+" #arrow-icon").addClass("fa-caret-up");
		$("#floor-"+floor_id+" #arrow-icon").removeClass("fa-caret-down");
	}
	$("#roomList-"+floor_id).toggle("slow");
}

function ShowDeviceList(room_id){
	if ($("#deviceList-"+room_id).hasClass("open")) {
		$("#deviceList-"+room_id).removeClass("open");
		$("li div.active").removeClass("active");
	}
	else {
		$("#deviceList-"+room_id).addClass("open");
	}
	$("#deviceList-"+room_id).toggle("slow");
}

function openDivs(floor_id = 0, room_id = 0) {
	if (floor_id == 0) {
		floor_id = $(".list-group").find("div").attr("id");
		floor_id = floor_id.split("floor-")[1];
	}
	ShowRoomList(floor_id);
	if (room_id == 0) {
		room_id = $("#roomList-"+floor_id).find("div").attr("id");
		room_id = room_id.split("room-")[1];
	}
	ShowDeviceList(room_id);
}

function selectDevice(id_trigger, room_id_device) {
	
	$("#optionList").hide();
	$("li div.active").removeClass("active");
	$("#device-"+room_id_device).addClass("active");
	getDeviceOptions(room_id_device, id_trigger);
	$("#optionList").show("slow");	
}

/*** Triggers ***/

function createTrigger(id_scenario) {
	$.ajax({
		type: "GET",
		url: "/templates/default/popup/popup_user_create_trigger.php",
		data: "id_scenario="+id_scenario,
		success: function(msg) {
			BootstrapDialog.show({
				title: '<div id="popupTitle" class="center"></div>',
				message: msg
			});
		}
	});
}

function triggerOnOff(room_id_device) {
	var val;
	
	val = $("#triggerOnOff-"+room_id_device).bootstrapSwitch('state');
	if (val) {
		val = "1";
	}
	else {
		val = "0";
	}
	$("#triggerPopupValue-"+room_id_device).val(val);
}

function triggerVarie(room_id_device) {
	var val;
	
	val = $("#slider-value-"+room_id_device).val();
	$("#triggerPopupValue-"+room_id_device).val(val);
}

function triggerSetVal(room_id_device) {
	var val;
	
	val = $("#number-value-"+room_id_device).val();
	$("#triggerPopupValue-"+room_id_device).val(val);
}

function triggerSetOperator(room_id_device) {
	var op;
	
	op = $("#selectOperator-"+room_id_device).val();
	$("#triggerPopupOperator-"+room_id_device).val(op);
}

function triggerVolume(room_id_device) {
	var val;
	
	val = $("#volume-"+room_id_device).val();
	$("#triggerPopupValue-"+room_id_device).val(val);
}

function triggerUpdateTemp(room_id_device, modif) {
	var val;
	
	val = parseInt($("#temp-"+room_id_device).val()) + modif;
	$("#triggerPopupValue-"+room_id_device).val(val);
	$("#temp-"+room_id_device).val(val);
	$("#output-temp-"+room_id_device).html(val);
}

function triggerUpdateRGBColor(room_id_device, val) {
	$("#triggerPopupValue-"+room_id_device).val(encodeURIComponent(val));
}

function saveTriggerOption(id_trigger, room_id_device, id_option, id_condition, modif) {
	var val = 0;
	var op = 0;

	val = $("#triggerPopupValue-"+room_id_device).val();
	op = $("#triggerPopupOperator-"+room_id_device).val();
	$.ajax({
		type: "GET",
		url: "/templates/default/form/form_save_trigger_elem.php",
		data: "id_trigger="+id_trigger
				+"&room_id_device="+room_id_device
				+"&id_option="+id_option
				+"&option_value="+val
				+"&id_condition="+id_condition
				+"&operator="+op
				+"&modif="+modif,
		success: function(result) {
			popup_close();
			displayTrigger(id_trigger);
		}
	});
}

/*** Schedules ***/

function createSchedule(id_scenario) {
	$.ajax({
		type: "GET",
		url: "/templates/default/popup/popup_user_create_schedule.php",
		data: "id_scenario="+id_scenario,
		success: function(msg) {
			BootstrapDialog.show({
				title: '<div id="popupTitle" class="center"></div>',
				message: msg
			});
		}
	});
}

function showTimeSelect(id_schedule) {
	$.ajax({
		type: "GET",
		url: "/templates/default/form/form_display_trigger_time.php",
		data: "id_schedule="+id_schedule,
		success: function(result) {
			$("#selectSchedule").html(result);
		}
	});
}

function SaveSchedule(schedule_id) {
	var months = [];
	var weekdays = [];
	var days = [];
	var hours = [];
	var mins = [];
	
	if ($("#MonthsBtn #toggleMonthList").bootstrapSwitch('state') == true) {
		months = Array(13).join(1).split('');
	}
	else {
		$("#monthsList .monthElemWidth").each(function(index) {
			if ($(this).bootstrapSwitch('state')) {
				months.push(1);
			}
			else {
				months.push(0);
			}
		});
	}
	
	if ($("#WeekdaysBtn #toggleWeekdayList").bootstrapSwitch('state') == true) {
		weekdays = Array(8).join(1).split('');
	}
	else {
		$("#weekdaysList .weekdayElemWidth").each(function(index) {
			if (index == 6) {
				if ($(this).bootstrapSwitch('state')) {
					weekdays.unshift(1);
				}
				else {
					weekdays.unshift(0);
				}
			}
			else {
				if ($(this).bootstrapSwitch('state')) {
					weekdays.push(1);
				}
				else {
					weekdays.push(0);
				}
			}
		});
	}
	
	if ($("#DaysBtn #toggleDayList").bootstrapSwitch('state') == true) {
		days = Array(32).join(1).split('');
	}
	else {
		$("#daysList .dayElemWidth").each(function(index) {
			if ($(this).bootstrapSwitch('state')) {
				days.push(1);
			}
			else {
				days.push(0);
			}
		});
	}
	
	if ($("#HoursBtn #toggleHourList").bootstrapSwitch('state') == true) {
		hours = Array(25).join(1).split('');
	}
	else {
		$("#hoursList .hourElemWidth").each(function(index) {
			if ($(this).bootstrapSwitch('state')) {
				hours.push(1);
			}
			else {
				hours.push(0);
			}
		});
	}
	
	if ($("#MinsBtn #toggleMinList").bootstrapSwitch('state') == true) {
		mins = Array(61).join(1).split('');
	}
	else {
		$("#minsList .minElemWidth").each(function(index) {
			if ($(this).bootstrapSwitch('state')) {
				mins.push(1);
			}
			else {
				mins.push(0);
			}
		});
	}
	months = parseInt(months.join(''), 2);
	weekdays= parseInt(weekdays.join(''), 2);
	days = parseInt(days.join(''), 2);
	hours = parseInt(hours.join(''), 2);
	mins = mins.join('');

	if (!$("#saveTS_btn").hasClass("m-progress")){
		$("#saveTS_btn").addClass("m-progress");
		$.ajax({
			type: "GET",
			url: "/templates/default/form/form_save_trigger_schedule.php",
			data: "id_schedule="+schedule_id
			      +"&months="+months
			      +"&weekdays="+weekdays
			      +"&days="+days
			      +"&hours="+hours
			      +"&mins="+mins,
			success: function(result) {
				$("#saveTS_btn").removeClass("m-progress");
				changeSaveBtnState("#saveTS_btn", 1);
			}
		});
	}
}

function changeSaveBtnState(id_btn, state = 0) {
	
	if (state == 0) {
		$(id_btn).removeClass("btn-success");
		$(id_btn).addClass("btn-primary");
		$(id_btn).text("Save");
	}
	if (state == 1) {
		$(id_btn).removeClass("btn-primary");
		$(id_btn).addClass("btn-success");
		$(id_btn).text("Saved !");
	}
	if (state == 2) {
		$(id_btn).removeClass("btn-success");
		$(id_btn).addClass("btn-danger");
		$(id_btn).text("ERROR !");
	}
}

/*** Color ***/

function getElemHexaColor(selector) {
	var rgb = $(selector).css("color").match(/\d+/g);
	var r   = parseInt(rgb[0], 10).toString(16);
	if (r == 0) {
		r = "00";
	}
	if (r.lenght < 2) {
		r = "0" + r;
	}
	var g   = parseInt(rgb[1], 10).toString(16);
	if (g == 0) {
		g = "00";
	}
	if (g.lenght < 2) {
		g = "0" + g;
	}
	var b   = parseInt(rgb[2], 10).toString(16);
	if (b == 0) {
		b = "00";
	}
	if (b.lenght < 2) {
		b = "0" + b;
	}
	var hex = '#'+ r + g + b;
	
	return (hex);
}