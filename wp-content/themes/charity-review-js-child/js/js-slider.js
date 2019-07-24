// for chartity review child theme slide auto

jQuery(document).ready(function(){
	'use strict';
    front_Page_slider();
	events_slider();
});

/************header sliders from there************/
function front_Page_slider(){
	'use strict';
	if(!$('#home-slider .slide-item').length){
		return;
	}
	setInterval(slider_auto_loop,100);
}
var content={
	time_eclipse:0,
	change_time:40,
	auto_play:true,
};


/*auto slider show based on time changed*/
function slider_auto_loop(){
	'use strict';
	mouse_in_out();
	if(content.auto_play === true){
		if(content.time_eclipse < content.change_time){
		    content.time_eclipse++;
	    }else{
		    content.time_eclipse =0;
		    $('.featured-slider .slick-prev').trigger('click');
	    }
	}
	
}

/*detect user activities*/
function mouse_in_out(){
	'use strict';
	$('.featured-slider').on({
		mouseenter:function(){
			content.auto_play =false;
	    },
		mouseleave:function(){
			content.auto_play =true;
			 content.time_eclipse =0;
		}
	});
}

/******************events sliders from here******************************/
function events_slider(){
	'use strict';

	$('#about.blogroll .col-md-4:gt(2)').wrapAll('<div id="event-slider1" class="event-slider event-slider-hidden clearfix"/>');
    $('#about.blogroll .col-md-4:lt(3)').wrapAll('<div id="event-slider2" class="event-slider clearfix"/>');
	$('.event-slider').wrapAll('<div class="events-slider-container"/>');
	if($(window).width()<600){	
		return;
	}
	$('.events-slider-container').append('<button class="right-arrow">').prepend('<button class="left-arrow"/>');
	$('.events-slider-container button').click(function(){
		$('.event-slider').toggleClass('event-slider-hidden').addClass('event-slider-fadein');
	});
}

