// JavaScript Document for image slidering
jQuery(document).ready(function(){
	'use strict';
	achieve_data();
	build_makeup();
	click_event();
	setInterval(slider_auto_loop,100);
});

var content={
	time_eclipse:0,
	change_time:30,
	mobie_size:760,
	screen_size:'',
	total_panel:0,
	current_panel:1,
	auto_play:true,
	panel_content:Array
};

/*obtain the data from slider post type*/
function achieve_data(){
	'use strict';
	$('.slider').each(function(index){
		var $this =$(this);
		var imageSize1 = $this.attr('image-size-1');
		var imageSize2 = $this.attr('image-size-2');
		var title=$this.attr('title');
		var data_content=$this.attr('data-content');
		content.total_panel = index +1;
		content.screen_size =$this.closest('.yee_slider_wrapper').outerWidth(true);
		if (content.screen_size >content.mobie_size){
			content.panel_content[index+1]="<div class='slider-image' style='background-image:url("+imageSize1+")'><div class='caption'><h3>"+title+"</h3><p>"+data_content+"</p></div></div>";
		}else{
			content.panel_content[index+1]="<div class='slider-image' style='background-image:url("+imageSize2+")'><div class='caption'><h3>"+title+"</h3></div></div>";
		}
	});
}

/** build new container for slidering image**/
function build_makeup(){
	'use strict';
	$('.yee_slider_wrapper').html('').append("<div class='slider_panel'/>");
	$('.slider_panel').append('<div class="container_1"/>').append('<div class="slider_nav"/>');
	for(var i=0;i<content.total_panel;i++){
		$('.slider_nav').append('<div class="dot_nav"/>');
	}
	
}

/*image sliders when clicking the nav*/
function click_event(){
	'use strict';
	$('.slider_nav .dot_nav').click(function(){
		var $this=$(this);
		$('.dot_nav').removeClass('selected');
		$this.addClass('selected');
	    content.current_panel = $(this).index()+1;
		$this.closest('.slider_panel').append('<div class="container_2" style="opacity:0"/>');
		$('.container_2').html(content.panel_content[content.current_panel ]).animate({'opacity':1},500,function(){
			$('.container_1').remove();
			$(this).addClass('container_1').removeClass('container_2');
		});			
	});
	click_trigger();
}

/*trigger the first slider show*/
function click_trigger(){
	'use strict';
	$('.dot_nav:first-child').trigger('click');
}

/*auto slider show based on time changed*/
function slider_auto_loop(){
	'use strict';
	mouse_in_out();
	if(content.auto_play === true){
		if(content.time_eclipse < content.change_time){
		    content.time_eclipse++;
	    }else{
		    if(content.current_panel === content.total_panel){
	           content.current_panel =1;
	        }else{
			   content.current_panel++;
		    }
		    content.time_eclipse =0;
		    $(".dot_nav:nth-child("+content.current_panel+")").trigger('click');
	    }
	}
	

}

/*detect user activities*/
function mouse_in_out(){
	'use strict';
	$('.slider_panel').on({
		mouseenter:function(){
			content.auto_play =false;
	    },
		mouseleave:function(){
			content.auto_play =true;
			 content.time_eclipse =0;
		}
	});
}
