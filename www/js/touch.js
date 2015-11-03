(function ($) {
	//cache on ie
	$.ajaxSetup({ cache: false });
	
    // Detect touch support
    $.support.touch = 'ontouchend' in document;
    // Ignore browsers without touch support
    if (!$.support.touch) {
    return;
    }
    var mouseProto = $.ui.mouse.prototype,
        _mouseInit = mouseProto._mouseInit,
        touchHandled;

    function simulateMouseEvent (event, simulatedType) { //use this function to simulate mouse event
    // Ignore multi-touch events
        if (event.originalEvent.touches.length > 1) {
        return;
        }
    event.preventDefault(); //use this to prevent scrolling during ui use

    var touch = event.originalEvent.changedTouches[0],
        simulatedEvent = document.createEvent('MouseEvents');
    // Initialize the simulated mouse event using the touch event's coordinates
    simulatedEvent.initMouseEvent(
        simulatedType,    // type
        true,             // bubbles                    
        true,             // cancelable                 
        window,           // view                       
        1,                // detail                     
        touch.screenX,    // screenX                    
        touch.screenY,    // screenY                    
        touch.clientX,    // clientX                    
        touch.clientY,    // clientY                    
        false,            // ctrlKey                    
        false,            // altKey                     
        false,            // shiftKey                   
        false,            // metaKey                    
        0,                // button                     
        null              // relatedTarget              
        );

    // Dispatch the simulated event to the target element
    event.target.dispatchEvent(simulatedEvent);
    }
    mouseProto._touchStart = function (event) {
    var self = this;
    // Ignore the event if another widget is already being handled
    if (touchHandled || !self._mouseCapture(event.originalEvent.changedTouches[0])) {
        return;
        }
    // Set the flag to prevent other widgets from inheriting the touch event
    touchHandled = true;
    // Track movement to determine if interaction was a click
    self._touchMoved = false;
    // Simulate the mouseover event
    simulateMouseEvent(event, 'mouseover');
    // Simulate the mousemove event
    simulateMouseEvent(event, 'mousemove');
    // Simulate the mousedown event
    simulateMouseEvent(event, 'mousedown');
    };

    mouseProto._touchMove = function (event) {
    // Ignore event if not handled
    if (!touchHandled) {
        return;
        }
    // Interaction was not a click
    this._touchMoved = true;
    // Simulate the mousemove event
    simulateMouseEvent(event, 'mousemove');
    };
    mouseProto._touchEnd = function (event) {
    // Ignore event if not handled
    if (!touchHandled) {
        return;
    }
    // Simulate the mouseup event
    simulateMouseEvent(event, 'mouseup');
    // Simulate the mouseout event
    simulateMouseEvent(event, 'mouseout');
    // If the touch interaction did not move, it should trigger a click
    if (!this._touchMoved) {
      // Simulate the click event
      simulateMouseEvent(event, 'click');
    }
    // Unset the flag to allow other widgets to inherit the touch event
    touchHandled = false;
    };
    mouseProto._mouseInit = function () {
    var self = this;
    // Delegate the touch handlers to the widget's element
    self.element
        .on('touchstart', $.proxy(self, '_touchStart'))
        .on('touchmove', $.proxy(self, '_touchMove'))
        .on('touchend', $.proxy(self, '_touchEnd'));

    // Call the original $.ui.mouse init method
    _mouseInit.call(self);
    };
})(jQuery);

function disconnect() {
	$.ajax({
		type:"GET",
		url: "/form/form_disconnect.php",
		success: function(msg) {
			window.location.href='/';
		}
	});
}

function popup_close() {
    $.each(BootstrapDialog.dialogs, function(id, dialog){
        dialog.close();
    });
}

function popup_close_last() {
	var i = 0;
	var j = 0;
    $.each(BootstrapDialog.dialogs, function(id, dialog){
    	i++;
    });
    $.each(BootstrapDialog.dialogs, function(id, dialog){
    	if (++j == i){
    		dialog.close();
    	}
    });
}

$(document).keyup(function(e) {
    if($('#eventSave') && $('#eventSave').length > 0 && e.keyCode == 13) {
    	$('#eventSave').click();
    }
});

function redirect(url) {
	window.location.href=url;
}

function is_valid_password(password, passwordConfirm)
{
	if (password.length < 6)
	{
		return 0;
	}
	if (password != passwordConfirm)
	{
		return -1;
	}
	return 1;
}

function cmd_camera(iddevice, action, optionid){
	if (iddevice != ""){
		$.ajax({
			type:"GET",
			url: "form/form_mc_camera.php",
			data: "iddevice="+iddevice+"&action="+action+"&optionid="+optionid,
			complete: function(result, status) {
			}
		});
	}
}

