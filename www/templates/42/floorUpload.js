(function($){
	$.fn.floorUpload = function(args){ 
		var defaults = { 
			id: -1,
			action: "showContextMenu"
		};
		
		var settings = $.extend({}, defaults, args);
		
		eval(settings.action)(settings);
	};
}(jQuery));


// removal of the background

function deleteBgImageFloor(args){
	var this_id = args.id;
	
	BootstrapDialog.confirm(sentence_onConfirmRemoval, function(result){
		if (result){
			$.ajax({
				type:"POST",
				url: "templates/"+globalTemplate+"/popup/deleteFloorBackgroundUpload.php",
				data: { id: args.id },
				dataType: 'json',
				success: function(data) {
					if (data.error){
						BootstrapDialog.alert(data.error);
					} else {
						BootstrapDialog.alert(data.success);
					}
				}
			});
		} else {
			$(window).floorUpload({ id: args.id });
		}
	});
}

// AJAX UPLAOD

var this_set_ID = -1, files, this_dialog;

function loadUploader(args){
	BootstrapDialog.show({
		title: args.title,
		message: sentence_onUpload+'<center><form method="post" enctype="multipart/form-data" id="uploaderForm"><input id="uploader" type="file" name="file" class="margin-top" /> <button class="margin-left margin-top btn btn-info">Submit</button></form></center>',
		onshown: function(dialog){
			this_set_ID = args.id;
			this_dialog = dialog;
			
			$('#uploader').on('change', prepareUpload);
			$('#uploaderForm').on('submit', uploadProcess);
		}
	});
}

function prepareUpload(event){
	files = event.target.files;
}
function uploadProcess(event){
	event.stopPropagation();
	event.preventDefault();
	
	var loadingDialog;
	loadingDialog = loadingDialog || (function () {
		var loadingDiv = $("" +
            "<div class='modal' id='lpDialog' data-backdrop='static' data-keyboard='false'>" +
                "<div class='modal-dialog' >" +
                    "<div class='modal-content'>" +
                        "<div class='modal-header'><b>Processing...</b></div>" +
                        "<div class='modal-body'>" +
                            "<div class='progress'>" +
                                "<div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='100' aria-valuemax='100' style='width:100%'> "+
                                  "Please Wait..."+
                                "</div>"+
                              "</div>"+
                        "</div>" +
                    "</div>" +
                "</div>" +
            "</div>");
		return {
			show: function() {
				loadingDiv.modal('show');
			},
			hide: function () {
				loadingDiv.modal('hide');
			},

		};
	})();
	
	loadingDialog.show();
		
	var data = new FormData();
	data.append(0, files[0]);
	data.append('id', this_set_ID);
	
	$.ajax({
		url: 'templates/'+globalTemplate+'/popup/floorImageUploader.php',
		type: 'POST',
		data: data,
		cache: false,
		dataType: 'json',
		processData: false,
		contentType: false,
		success: function(data, textStatus){
			loadingDialog.hide();
			this_dialog.close();
			
			if (data.error){
				BootstrapDialog.alert(data.error);
			} else {
				BootstrapDialog.alert(data.success);
			}
		},
		error: function(data, textStatus){
			loadingDialog.hide();
			console.error('There was an error processing the request: '+data.responseText);
		}
	});
}
	

// Context menu generation

function showContextMenu(args){
	$.ajax({
		type:"POST",
		url: "templates/"+globalTemplate+"/popup/previewFloorUpload.php",
		data: { id: args.id },
		success: function(msg) {
			var results = msg.split('|||');
			var toShow, toAdd='';
			
			if (results.length == 4){
				toShow = [
					{ label: results[2], cssClass: 'btn-info', action: function(dialog){ dialog.close(false); $(this).floorUpload({ id: args.id, action: 'loadUploader', title: results[0] }); }  },
					{ label: results[3], cssClass: 'btn-default', action: function(dialog){ dialog.close(false); } }
				];
			} else {
				toShow = [
					{ label: results[3], cssClass: 'btn-info', action: function(dialog){ dialog.close(false); $(this).floorUpload({ id: args.id, action: 'loadUploader', title: results[0] }); } },
					{ label: results[4], cssClass: 'btn-danger', action: function(dialog){ dialog.close(false); $(this).floorUpload({ id: args.id, action: 'deleteBgImageFloor' }); } },
					{ label: results[5], cssClass: 'btn-default', action: function(dialog){ dialog.close(false); } }
				];
				toAdd = '<center><img src="'+results[2]+'" class="thumb" onclick="window.open(\''+results[2]+'\');" /></center>';
			}
			
			BootstrapDialog.show({
				title: results[0],
				message: results[1]+toAdd,
				buttons: toShow,
			});
		}
	});
}