var elementDragging, elementDragged;

$(document).ready(function(){
	$('.draggable').draggable({
		scroll: false,
		start: function(event, ui){
			elementDragging = $(this);
			posiDevices();
		},
		stop: function(event, ui){
			elementDragging = '';
			elementDragged = $(this);
			posiDevices();
		}
	})
	.click(function(){
		var element_id = $(this).attr('data-id');
		var title = $(this).find('p').text();
		
		$.ajax({
				type:"POST",
				url: "templates/"+global_template+"/popup/getDeviceContent.php",
				data: { id: element_id },
				success: function(data) {
					if (data.match('CALL::')){ 
						$.ajax({
							type:"POST",
							url: "templates/"+global_template+"/popup/"+data.split('CALL::')[1],
							data: { iddevice: element_id },
							success: function(data) {
								BootstrapDialog.show({
									title: title,
									message: data,
								});
							}
						});
					}	else { 
						BootstrapDialog.show({
							title: title,
							message: data,
						});
					}
				}
			});
	});
	
	$('#popBottom').droppable({
		drop: function(event, ui){
			var element_id = $(elementDragging).attr('data-id');
			
			$.ajax({
				type:"POST",
				url: "templates/"+global_template+"/popup/updateDragDevicePosition.php",
				data: { x: '0/0', y: '0/0', id: element_id },
				dataType: 'json',
				success: function(data) {
					if (data.error){
						BootstrapDialog.alert(data.error);
					} 
					$(elementDragged).css({ 'left': '0px', 'top': '0px' });
					posiDevices();
				},
				error: function(data){
					if (data.error){
						BootstrapDialog.alert(data.error);
					} 
					$(elementDragged).css({ 'left': '0px', 'top': '0px' });
					posiDevices();
				}
			});
		},
		over: function(event, ui){
			if ($('#popInnerBottom .innerSentence').is(':visible')){
				$('#popInnerBottom .innerSentence').css('display', 'none');
			}
		},
		out: function(event, ui){
			sentenceBotStatus();
		}
	})
	
	$('#popMiddle').droppable({
		drop: function(event, ui){
			var element_id = $(elementDragging).attr('data-id');
			var position = ui.offset;
			var data_x = position.left+"/"+$(window).width();
			var data_y = position.top+"/"+$(window).height();
			
			$.ajax({
				type:"POST",
				url: "templates/"+global_template+"/popup/updateDragDevicePosition.php",
				data: { x: data_x, y: data_y, id: element_id },
				dataType: 'json',
				success: function(data) {
					if (data.error){
						BootstrapDialog.alert(data.error);
					} 
				}
			});
		}
	});
});