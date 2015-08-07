var current_global_width=$(window).width();
var current_global_height=$(window).height();

var allX;

$(document).ready(function(){
	popBottomSize();
	popMiddleSize();
	houseSize();
	posiLoadingPose();
	sentenceBotStatus();
	posiDevices();
	
	setInterval("popBottomSize()",500);
	setInterval("popMiddleSize()", 500);
	setInterval("houseSize()", 500);
	
	if (window.location.href.match('id')){
		$('.navbar .container-fluid .navbar-collapse ul.navbar-right').prepend('<li><a href="home"><span class="glyphicon glyphicon-chevron-left"></span> '+sentence_goBackFloor+'</a></li>');
	}
	$('body').css('overflow-x', 'hidden');
});

$(window).resize(function(){
	popBottomSize();
	popMiddleSize();
	devicesRepose();
	sentenceBotStatus();
	posiDevices();
});

function posiDevices(){
	var nb = 0;
	var nb_displayed = 0;
	var amount_deca = 0;
	
	var w = $('.device_self:first').outerWidth(true);
	
	var amount_max = Math.trunc( ($(window).width()-40)/w ) - 1;
	
	$('.device_self').each(function(){
		if (nb_displayed < amount_max){
		
			if ($(this).css('top').split('px')[0] != 0){ nb++; } else {
				$(this).css('display', 'table-cell');
				$(this).css('left', '-'+(w*(nb+amount_deca))+'px');
				amount_deca += nb; nb = 0;
				
				nb_displayed++;
			}
		} else {
			$(this).css({ 'display': 'none' });
		}
	});
}

function displayGlobalImageBackground(){
	if ($('#globalFloorBackground').length > 0){
		$('#globalFloorBackground').css({
			'height': $('#popMiddle').css('height').split('px')[0]+'px',
			'width': $('#popMiddle').css('width').split('px')[0]+'px'
		});
	}
}

function sentenceBotStatus(){
	var good = true;
	
	$('.device_self').each(function(){
		if ($(this).css('left').split('px')[0] == '0' && $(this).css('top').split('px')[0] == '0'){ good = false; }
	});
	
	if (good == true){
		$('#popInnerBottom .innerSentence').css('display', 'table-cell')
		.css('left', (($('#popBottom').width() - $('#popInnerBottom .innerSentence').width())/2)+'px')
		.css('top', (($('#popBottom').height() - $('#popInnerBottom .innerSentence').height())/2)+'px');
	} else {
		$('#popInnerBottom .innerSentence').css('display', 'none');
	}
}

function popBottomSize(){
	if ($('#popBottom').is(':visible')){
		var this_height =  $(window).outerHeight(true) * 0.2;
		
		$('#popBottom').css({ 
			'width': $(window).outerWidth()+'px' ,
			'height': this_height+'px',
			'margin-top': ($(window).outerHeight(true)-this_height)+'px'
		});
		$('#popInnerBottom').css('max-width', $(window).width()+'px');
	}
}

function popMiddleSize(){
	var this_height;
	
	if ($('#popBottom').is(':visible')){
		this_height = $(window).height()-$('nav[role="navigation"]').height()-$('#popBottom').height() - 1;
	} else {
		this_height = $(window).height()-$('nav[role="navigation"]').height();
	}
	
	$('#popMiddle').css({
		'height': this_height+'px',
		'width': $(window).width()+'px',
		'margin-top': $('nav[role="navigation"]').height()+'px'
	});
	displayGlobalImageBackground();
}

function houseSize(){
	if ($('#home').length > 0){
		if ($(window).width() < ($('#home').width()+30)){
			var roof = $(window).width() - 30;
			var coef = roof/$('#home').width();
			var part_house = $('.partHouse').width()*coef;
			
			$('#home').css('width', roof+'px');
			$('#topHouse').css('width', roof+'px');
			$('.partHouse').css('width', part_house+'px');
			$('.partHouse').parent().css('width', part_house+'px');
			$('#baseHouse').css('width', ($('#baseHouse').width()*coef)+'px');
		}
		else if ($(window).width() > 556){
			$('#home').css('width', '526px');
			$('#topHouse').css('width', 'auto');
			$('.partHouse').css('width', 'auto');
			$('.partHouse').parent().css('width', '469px');
			$('#baseHouse').css('width', 'auto');
		}
		
		$('#innerPopMiddle').css('padding-top',($('#popMiddle').height() * 0.1)+'px');
	} 
}

function posiAdditionalElement(){
	if ($('#glob_title').length > 0){
		$('#glob_title').css('margin-top', ((($('#popMiddle').height()-$('#glob_title').height())/2)-50)+'px').css('display', 'inline-block');
		
		setTimeout('posiAdditionalElement()', 200);
	}
}

function posiLoadingPose(){
	var x, y, coefX, coefY;
	var pastSizeX, pastSizeY;
	var actualX, actualY;
	var effectiveX, effectiveY;
	var currentPos;
	
	$('.loadingPose').each(function(){
		x = $(this).attr('data-x');
		y = $(this).attr('data-y');
		
		pastSizeX = x.split('/')[1];
		pastSizeY = y.split('/')[1];
		
		coefX = $(window).width()/pastSizeX;
		coefY = $(window).height()/pastSizeY;
		
		actualX = x.split('/')[0]*coefX;
		actualY = y.split('/')[0]*coefY;
		
		currentPos = $(this).offset();
		
		effectiveX = actualX-currentPos.left;
		effectiveY = actualY - currentPos.top;
		
		$(this).removeClass('loadingPose').css({ 'position': 'relative', 'top': effectiveY+'px', 'left': effectiveX+'px' });
	});
}

function devicesRepose(){
	var new_width = $(window).width();
	var new_height = $(window).height();
	
	var coefX = new_width/current_global_width;
	var coefY = new_height/current_global_height;
	
	var this_posi,left;
	
	$('.device_self').each(function(){
		this_posi = $(this).position();
		
		left = parseInt($(this).css('left').split('px')[0]);
		
		$(this).css({ 'top': (this_posi.top*coefY)+'px', 'left': (left*coefX)+'px' });
	});
	
	current_global_width = $(window).width();
	current_global_height = $(window).height();
}