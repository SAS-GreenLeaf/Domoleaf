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

function ShowTimeline(id, opt, optional_id){
	$("#"+id).toggle();
	if (opt == 2) {
		RoomBgSize(optional_id);
	}
	else if (opt == 1) {
		$(".timeline-rooms").each(function(index){
			room_id = $(this).attr("id").split("current-room-")[1];
			RoomBgSize(room_id);
		});
	}
	
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

function Variation(iddevice, optionid, step, popup){
	popup = typeof popup !== 'undefined' ? popup : 0;
	if (popup == 1){
		var varie = $("#slider-value-"+iddevice+"-popup").val();
	}
	else{
		var varie = $("#slider-value-"+iddevice).val();
	}

	varie = parseInt(varie) + step;
	if (varie == 0 || varie > 0 && varie <= 255){
		if (popup == 1){
			$("#slider-value-"+iddevice+"-popup").val(varie);
		}
		else{
			$("#slider-value-"+iddevice).val(varie);
		}
		outputUpdate(iddevice, varie, popup);
		getVariation(iddevice, optionid, popup);
	}
}
		
function outputUpdate(iddevice, val, popup) {
	val = Math.round((parseInt(val)*100)/255);
	popup = typeof popup !== 'undefined' ? popup : 0;
	if (popup == 1){
		$("#range-"+iddevice+"-popup").html(val+"%");
	}
	else{
		$("#range-"+iddevice).html(val+"%");
	}
}
		
function onOffToggle(iddevice, optionid, popup){
	popup = typeof popup !== 'undefined' ? popup : 0;
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

function getVariation(iddevice, optionid, popup){
	popup = typeof popup !== 'undefined' ? popup : 0;
	if (popup == 1){
		var value = $("#slider-value-"+iddevice+"-popup").val();
	}
	else{
		var value = $("#slider-value-"+iddevice).val();	
	}
	
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

function UpdateTemp(iddevice, idoption, action, popup){
	popup = typeof popup !== 'undefined' ? popup : 0;
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

function resetError(room_device_id, device_opt){
	$.ajax({
		type:"GET",
		url: "/form/form_mc_reset_error.php",
		data: "room_device_id="+room_device_id+"&device_opt="+device_opt,
		complete: function(result) {
			popup_close();
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
	else if (idopt == 399){
		$("#widget-"+roomdeviceid+"-"+idopt).text(val.valeur);
		$("#widget-"+roomdeviceid+"-"+idopt+"-cost").html("&nbsp;-&nbsp;" + (val.valeur * val.highCost).replace(".", ",") + val.currency);
	}
	else if (idopt == 407){
		$("#widget-"+roomdeviceid+"-"+idopt).text(val.valeur);
	}
	else if (idopt == 153){
		if (val.valeur == 0){
			$("#widget_info-"+roomdeviceid+"-"+idopt).removeClass("btn-danger");
			$("#widget_info-"+roomdeviceid+"-"+idopt).addClass("btn-greenleaf");
		}
		else{
			$("#widget_info-"+roomdeviceid+"-"+idopt).removeClass("btn-greenleaf");
			$("#widget_info-"+roomdeviceid+"-"+idopt).addClass("btn-danger");
		}
	}
}

/*** Custom Configuration ***/

function RoomBgSize(id) {
	$("#room-bg-"+id).height(0);
	height = $("#timeline-room-"+id).height();
	$("#room-bg-"+id).height(height);
}

function RoomBgSizeSidebar() {
	$(".list-group a").each(function(index) {
		room_id = $(this).attr("id").split("room-")[1];
		$("#room-bg-"+room_id).height(0);
		height = $("#room-"+room_id).height();
		height = height+10+15+2-5;// padding & border size
		$("#room-bg-"+room_id).height(height);
		$("#room-bg-"+room_id).css("margin-top", "-"+(height)+"px");
		if ($("#room-bg-"+room_id).hasClass("image-ok")) {
			$("#room-"+room_id).css("background-color", "transparent");
		}
	});
	
}

function CustomPopup(type_elem, id_elem, userid){
	$.ajax({
		type:"GET",
		url: "/templates/default/popup/popup_custom_device.php",
		data: "type_elem="+type_elem+"&idelem="+id_elem+"&userid="+userid,
		success: function(msg) {
			BootstrapDialog.show({
				title: '<div id="popupTitle" class="center"></div>',
				message: msg
			});
		}
	});
}

function popupChromaWheel(iddevice, bg_color, userid, white){
	white = typeof white !== 'undefined' ? white : 0;
	$.ajax({
		type:"GET",
		url: "/templates/default/popup/popup_chroma_wheel.php",
		data: "iddevice="+iddevice+"&bg_color="+bg_color+"&userid="+userid+"&white="+white,
		success: function(msg) {
			BootstrapDialog.show({
				title: '<div id="popupTitle" class="center"></div>',
				message: msg
			});
		}
	});
}

function submitFormUpload(event) {
	event.stopPropagation();
	event.preventDefault();
	if (files != ""){
		$("#uploadFileForm").submit();
	}
	else {
		$("#uploadFileForm").click();
	}
}

function uploadElemImg(event) {
	if (files != ""){
		userid = $("#userid").val();
		type_elem = $("#type_elem").val();
		id_elem = $("#id_elem").val();

		var data = new FormData();
		data.append("userid", userid);
		data.append("id_elem", id_elem);
		data.append("fileToUpload", files[0]);
		if (type_elem == 1) {
			$.ajax({
				url: "/templates/default/form/form_custom_upload_device.php",
				type: "POST",
				data: data,
				processData: false,
				contentType: false,
				beforeSend:function(result, status){
					PopupLoading();
				},
				success: function(data, textStatus){
					popup_close_last();
					if (data == "0"){
						customUploadFail();
					} else {
						customUploadSuccess(type_elem, id_elem, data);
					}
				},
			});
		}
		else {
			$.ajax({
				url: "/templates/default/form/form_custom_upload_room.php",
				type: "POST",
				data: data,
				processData: false,
				contentType: false,
				beforeSend:function(result, status){
					PopupLoading();
				},
				success: function(data, textStatus){
					popup_close_last();
					if (data == "0"){
						customUploadFail();
					} else {
						customUploadSuccess(type_elem, id_elem, data);
					}
				},
			});
		}
	}
}

function customUploadFail() {
	$("#uploadSuccess").hide();
	$("#uploadFail").show();
}

function customUploadSuccess(type_elem, id_elem, data) {
	$("#uploadFail").hide();
	$("#uploadSuccess").show();
	$('#deleteBtn').show();
	$("#uploadBtn").hide();
	$("#uploadMsg").hide();
	$("#previewImg").addClass("aspect-square");
	$("#previewImg").removeClass("aspect-square-little");
	if (type_elem == 1) {
		$('#widget-bg-'+id_elem).css("background-image", "url(\""+data+"\")");
	}
	else {
		$('#room-bg-'+id_elem).css("background-image", "url(\""+data+"\")");
	}
}

function deleteDeviceImg(type_elem, id_elem, event) {
	event.stopPropagation();
	event.preventDefault();
	
	if (type_elem == 1) {
		$.ajax({
			url: "/templates/default/form/form_custom_delete_device.php",
			type: "POST",
			data: "device="+id_elem,
			success: function(data){
				customUploadDelete();
				$('#widget-bg-'+id_elem).css("background-image", "none");
			}
		});
	}
	else if (type_elem == 2){
		$.ajax({
			url: "/templates/default/form/form_custom_delete_room.php",
			type: "POST",
			data: "room="+id_elem,
			success: function(data){
				customUploadDelete();
				$('#room-bg-'+id_elem).css("background-image", "none");
			}
		});
	}
}

function customUploadDelete() {
	$("#uploadSuccess").hide();
	$('#deleteBtn').hide();
	$("#uploadBtn").show();
	$("#uploadMsg").show();
	$("#previewImg").removeClass("aspect-square");
	$("#previewImg").addClass("aspect-square-little");
	fileImage.css("background-image", "none");
	files = "";
}

function updateBGColor(color, userid, idelem){
	$.ajax({
		type:"GET",
		url: "/templates/default/form/form_update_bg_color.php",
		data: "color="+encodeURIComponent(color)+"&userid="+userid+"&idelem="+idelem,
		success: function(result) {
			if (result) {
				if (idelem == 1) {
					$("#colorUserInstallBg").css("background-color", result);
				}
				else {
					$("#colorUserMenusBorderBg").css("background-color", result);
				}
				popup_close();
			} 
		},
	});
}

/*** Scenarios ***/

function ElemToScenario(id_scenario, id_elem, nb_elem) {
	$.ajax({
		type:"GET",
		url: "/form/form_update_scenario.php",
		data: "id_scenario="+id_scenario
		     +"&id_elem="+id_elem
		     +"&elem="+nb_elem,
		success: function(result) {
			redirect("/profile_user_scenarios/"+id_scenario+"/"+nb_elem);
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
			url: "/form/form_save_smartcmd_elem.php",
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

	var option_vals = {
		363 : 'play',
		364 : 'pause',
		365 : 0,
		366 : 'next',
		367 : 'prev',
		368 : 'mute',
		400 : 1,
		401 : 1,
		402 : 1,
		403 : 1,
		404 : 1,
		405 : 1,
		406 : 1
	};
	
	if (id_option in option_vals) {
		val = option_vals[id_option];
	}
	$.ajax({
			type: "GET",
			url: "/form/form_save_smartcmd_elem.php",
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
		url: "/form/form_smartcmd_update_delay.php",
		data: "smartcmd_id="+smartcmd_id
				+"&exec_id="+exec_id
				+"&delay="+delay,
		success: function(result) {
		}
	});
}

function dropZoneAnimate(trigger) {
	trigger = typeof trigger !== 'undefined' ? trigger : 0;
	if (trigger == 0) {
		$(".smartcmdElemDrop").addClass("drop-zone-activate", "100");
	}
	else {
		$(".triggerElemDrop").addClass("drop-zone-activate", "100");
	}
}

function dropZoneStop(trigger) {
	trigger = typeof trigger !== 'undefined' ? trigger : 0;
	if (trigger == 0) {
		$(".smartcmdElemDrop").removeClass("drop-zone-activate");
	}
	else {
		$(".triggerElemDrop").removeClass("drop-zone-activate");
	}
}

function listRoomsOfFloor(elem_id, opt) {
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
				if (opt == 0){
					listDeviceOfRoom();
				}
			}
		}
	});
	if (floor_id == 0 && opt == 1) {
		$("#selectRoom-"+elem_id).val(0);
		saveLinkedRoom(elem_id);
	}
}

function listDeviceOfRoom() {
	var room_id;

	room_id = parseInt($("#selectRoom-0").val());
	$.ajax({
		type: "GET",
		url: "/templates/default/form/form_list_devices_of_room.php",
		data: "room_id="+room_id,
		success: function(result) {
			if (result) {
				$("#selectDevice").html(result);
				$('.selectpicker').selectpicker('refresh');
				listOptionOfDevice();
			}
		}
	});
}

function listOptionOfDevice() {
	var room_device_id;

	room_device_id = parseInt($("#selectDevice").val());
	$.ajax({
		type: "GET",
		url: "/templates/default/form/form_list_options_of_device.php",
		data: "room_device_id="+room_device_id,
		success: function(result) {
			if (result) {
				$("#selectOption").html(result);
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
		url: "/form/form_save_linked_room.php",
		data: "smartcmd_id="+smartcmd_id+"&room_id="+room_id,
		success: function(result) {
			if (room_id == 0) {
				$("#alert-linked-room").show();
			}
			else {
				$("#alert-linked-room").hide();
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

function openDivs(floor_id, room_id) {
	floor_id = typeof floor_id !== 'undefined' ? floor_id : 0;
	room_id = typeof room_id !== 'undefined' ? room_id : 0;
	if (floor_id == 0) {
		floor_id = $(".list-group").find("div").attr("id");
		if (typeof floor_id === 'undefined') {
			return;
		}
		floor_id = floor_id.split("floor-")[1];
	}
	ShowRoomList(floor_id);
	if (room_id == 0) {
		room_id = $("#roomList-"+floor_id).find("div").attr("id");
		if (typeof room_id === 'undefined') {
			return;
		}
		room_id = room_id.split("room-")[1];
	}
	ShowDeviceList(room_id);
}

function selectDevice(id_trigger, room_id_device) {
	$("li div.active").removeClass("active");
	$("#device-"+room_id_device).addClass("active");
	
	if ($("#optionList-"+room_id_device).hasClass("open")) {
		$("#optionList-"+room_id_device).removeClass("open");
	}
	else {
		getDeviceOptions(room_id_device, id_trigger);
		$("#optionList-"+room_id_device).addClass("open");
	}
	$("#optionList-"+room_id_device).toggle("fast");
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
		url: "/form/form_save_trigger_elem.php",
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

	$.ajax({
		type: "GET",
		url: "/form/form_save_trigger_schedule.php",
		data: "id_schedule="+schedule_id
		      +"&months="+months
		      +"&weekdays="+weekdays
		      +"&days="+days
		      +"&hours="+hours
		      +"&mins="+mins,
		success: function(result) {
		}
	});
}

/*** Color ***/

function getElemHexaColor(selector) {
	hex = '#00000';
	if (typeof $(selector).attr("id") !== 'undefined') {
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
	}
	return (hex);
}

/*** Menu ***/

function activateMenuElem(id_elem) {
	$(".bhoechie-tab-menu .list-group a").removeClass("active");
	$("#menu-"+id_elem).addClass("active");
}