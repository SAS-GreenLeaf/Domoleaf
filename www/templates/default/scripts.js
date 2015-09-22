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
		
function onOffToggle(iddevice, optionid){
	var value = $("#onoff-"+iddevice).prop("checked") ? 1 : 0;
	
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
		data: "iddevice="+iddevice+"&val="+value+"&optionid="+optionid,
		complete: function(result, status) {
		
		}
	});
}

function UpdateTemp(iddevice, idoption, action){
	
	$.ajax({
		type:"GET",
		url: "/templates/default/form/form_conf_temperature.php",
		data: "iddevice="+iddevice+"&idoption="+idoption+"&action="+action,
		success: function(result) {
			$("#output-mp-"+iddevice).html(result);
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
	seconds =parseInt($("#selectSeconds-"+exec_id).val());
	
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

function dropZoneAnimate() {
	$(".smartcmdElemDrop").addClass("drop-zone-activate", "100");
}

function dropZoneStop() {
	$(".smartcmdElemDrop").removeClass("drop-zone-activate");
}

function listRoomsLR(smartcmd_id) {
	var floor_id;
	
	floor_id = parseInt($("#selectFloor-"+smartcmd_id).val());
	$.ajax({
		type: "GET",
		url: "/templates/default/form/form_list_rooms_lr.php",
		data: "smartcmd_id="+smartcmd_id
				+"&floor_id="+floor_id,
		success: function(result) {
			if (result) {
				$("#selectRoom-"+smartcmd_id).html(result);
				$('.selectpicker').selectpicker('refresh');
			}
		}
	});
	changeSaveBtn();
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
				$("#saveLR_btn").removeClass("btn-primary");
				$("#saveLR_btn").addClass("btn-success");
				$("#saveLR_btn").text("Saved !");
			}
			else {
				$("#saveLR_btn").removeClass("btn-success");
				$("#saveLR_btn").addClass("btn-danger");
				$("#saveLR_btn").text("ERROR !");
			}
			
		}
	});
}

function changeSaveBtn() {
	$("#saveLR_btn").removeClass("btn-success");
	$("#saveLR_btn").addClass("btn-primary");
	$("#saveLR_btn").text("Save");
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