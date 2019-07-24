jQuery.fn.visible = function() {
    return this.css('visibility', 'visible');
};

jQuery.fn.invisible = function() {
    return this.css('visibility', 'hidden');
};

jQuery.fn.visibilityToggle = function() {
    return this.css('visibility', function(i, visibility) {
        return (visibility == 'visible') ? 'hidden' : 'visible';
    });
};

function wpforo_notice_show(notice){
	var cls = '';
	if( notice === undefined || notice.text === undefined || notice.text == '' ) return;
	if( notice.type !== undefined ) cls = notice.type;
	jQuery("#wpf-msg-box").hide();
	jQuery("#wpf-msg-box p.wpf-msg-box-triangle-right").removeClass("error").removeClass("success").addClass(cls);
	jQuery("#wpf-msg-box p.wpf-msg-box-triangle-right").html("<span>" + notice.text + "</span>");
	jQuery("#wpf-msg-box").show(150).delay(1000);
	if(cls != ''){
		var dur = 2500;
		if(cls == 'error'){
			dur = 6000;
		}
		setTimeout(function(){ jQuery("#wpf-msg-box").hide(); }, dur);
	}
}

function wpforo_phrase(phrase_key){
	phrase_key = phrase_key.toLowerCase();
	var wpfaPhrases = wpf_ajax_obj.phrases;
	if( wpfaPhrases[phrase_key] !== undefined ) phrase_key = wpfaPhrases[phrase_key];
	return phrase_key;
}

jQuery(document).ready(function($){
	
	$( "#add_wpftopic" ).click(function(){
		$( ".wpf-topic-create" ).slideToggle( "slow" );
	});
	
	$(document).on('click','.not_reg_user', function(){
		$("#wpf-msg-box").hide();
		$('#wpforo-load').visible();
		$('#wpf-msg-box').show(150).delay(1000);
		$('#wpforo-load').invisible();
	});

	$(document).on('click','#wpf-msg-box', function(){
		$("#wpf-msg-box").hide();
	});

	/* Home page loyouts toipcs toglle */
	$( ".topictoggle" ).click(function(){
		$('#wpforo-load').visible();
		
		var id = $(this).attr( 'id' );
		
		id = id.replace( "img-arrow-", "" );
		$( ".wpforo-last-topics-" + id ).slideToggle( "slow" );
		if($(this).attr('class') == 'topictoggle fa fa-chevron-down' ){
			$( '#img-arrow-' + id ).removeClass('fa-chevron-down').addClass('fa-chevron-up');
		}else{
			$( '#img-arrow-' + id ).removeClass('fa-chevron-up').addClass('fa-chevron-down');
		}
		
		id = id.replace( "button-arrow-", "" );
		$( ".wpforo-last-posts-" + id ).slideToggle( "slow" );
		if($(this).attr('class') == 'topictoggle wpfcl-a fa fa-chevron-down' ){
			$( '#button-arrow-' + id ).removeClass('fa-chevron-down').addClass('fa-chevron-up');
		}else{
			$( '#button-arrow-' + id ).removeClass('fa-chevron-up').addClass('fa-chevron-down');
		}
		
		$('#wpforo-load').invisible();
	});
	
	/* Home page loyouts toipcs toglle */
	$( ".wpforo-membertoggle" ).click(function(){
		var id = $(this).attr( 'id' );
		id = id.replace( "wpforo-memberinfo-toggle-", "" );
		$( "#wpforo-memberinfo-" + id ).slideToggle( "slow" );
		if($(this).find( "i" ).attr('class') == 'fa fa-caret-down' ){
			$(this).find( "i" ).removeClass('fa-caret-down').addClass('fa-caret-up');
		}else{
			$(this).find( "i" ).removeClass('fa-caret-up').addClass('fa-caret-down');
		}
	});
	
	
//	Reply
	$( ".wpforo-reply" ).click(function(){
		
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		$("#wpf-reply-form-title").html(wpf_ajax_obj.phrases['leave a reply']);
		
		var parentpostid = $(this).attr('id');
		parentpostid = parentpostid.replace("parentpostid", "");
		$("#postparentid").val( parentpostid );
		
		tinyMCE.activeEditor.setContent('');
		$( ".wpf-topic-sbs" ).show();
		$( "#wpf-topic-sbs" ).prop("disabled", false);
		
		$( "#formaction" ).attr('name', 'post[action]');
		$( "#formbutton" ).attr('name', 'post[save]');
		$( "#formtopicid" ).attr('name', 'post[topicid]');
		$( "#title" ).attr('name', 'post[title]');
		$( "#formaction" ).val( 'add' );
		$( "#formpostid" ).val( '' );
		$( "#formbutton" ).val( wpf_ajax_obj.phrases.save );
		$( "#title").val( wpf_ajax_obj.phrases['re'] + ": " + $("#title").attr('placeholder').replace( wpf_ajax_obj.phrases['re'] + ": ", ""));
		
		$('html, body').animate({ scrollTop: $("#wpf-form-wrapper").offset().top }, 500);
		
		tinymce.execCommand('mceFocus',false,'postbody');
		tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
		tinyMCE.activeEditor.selection.collapse(false);
		
		$('#wpforo-load').invisible();
		
	});
	
	//Answer
	$( ".wpforo-answer" ).click(function(){
		
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		$("#wpf-reply-form-title").html(wpf_ajax_obj.phrases['your answer']);
		
		tinyMCE.activeEditor.setContent('');
		$( "#formaction" ).attr('name', 'post[action]');
		$( "#formbutton" ).attr('name', 'post[save]');
		$( "#formtopicid" ).attr('name', 'post[topicid]');
		$( "#title" ).attr('name', 'post[title]');
		$( "#formaction" ).val( 'add' );
		$( "#formpostid" ).val( '' );
		$( "#formbutton" ).val( wpf_ajax_obj.phrases.save );
		$( "#title").val( wpf_ajax_obj.phrases['answer to'] + ": " + $("#title").attr('placeholder').replace(wpf_ajax_obj.phrases['re'] + ": ", "").replace(wpf_ajax_obj.phrases['answer to'] + ": ", ""));
		$('html, body').animate({ scrollTop: $("#wpf-form-wrapper").offset().top }, 500);
		
		tinymce.execCommand('mceFocus',false,'postbody');
		tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
		tinyMCE.activeEditor.selection.collapse(false);
		
		$('#wpforo-load').invisible();
		
	});
	
	//Comment
	$( ".wpforo-childreply" ).click(function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		$("#wpf-reply-form-title").html(wpf_ajax_obj.phrases['leave a comment']);
		
		var parentpostid = $(this).attr('id');
		var postid = parentpostid.replace("parentpostid", "");
		$("#postparentid").val( postid );
		
		tinyMCE.activeEditor.setContent('');
		$( ".wpf-topic-sbs" ).show();
		$( "#wpf-topic-sbs" ).prop("disabled", false);
		
		$( "#formaction" ).attr('name', 'post[action]');
		$( "#formbutton" ).attr('name', 'post[save]');
		$( "#formtopicid" ).attr('name', 'post[topicid]');
		$( "#title" ).attr('name', 'post[title]');
		$( "#formaction" ).val( 'add' );
		$( "#formpostid" ).val( '' );
		$( "#formbutton" ).val( wpf_ajax_obj.phrases.save );
		$( "#title").val( wpf_ajax_obj.phrases['re'] + ": " + $("#title").attr('placeholder').replace( wpf_ajax_obj.phrases['re'] + ": ", "").replace( wpf_ajax_obj.phrases['answer to'] + ": ", "") );
		$('html, body').animate({ scrollTop: $("#wpf-form-wrapper").offset().top }, 800);
		
		tinymce.execCommand('mceFocus',false,'postbody');
		tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
		tinyMCE.activeEditor.selection.collapse(false);
		
		$('#wpforo-load').invisible();
	});
	
//	Move
	jQuery( ".wpforo-move" ).click(function(){
		jQuery( "#movedialog" ).dialog({dialogClass:'wpforo-dialog wpforo-dialog-move'});
	});
	
//mobile menu responsive toggle
	$("#wpforo-menu .wpf-res-menu").click(function(){
		$("#wpforo-menu .wpf-menu").toggle();
	});
	var wpfwin = $(window).width();
	var wpfwrap = $('#wpforo-wrap').width();
	if( wpfwin >= 602 && wpfwrap < 700 ){
		$("#wpforo-menu .wpf-search-field").focus(function(){
			$("#wpforo-menu .wpf-menu li").hide();
			$("#wpforo-wrap #wpforo-menu .wpf-res-menu").show();
			$("#wpforo-menu .wpf-search-field").css('transition-duration', '0s');
		});
		$("#wpforo-menu .wpf-search-field").blur(function(){
			$("#wpforo-wrap #wpforo-menu .wpf-res-menu").hide();
			$("#wpforo-menu .wpf-menu li").show();
			$("#wpforo-menu .wpf-search-field").css('transition-duration', '0.4s');
		});
	}
	
	//Turn off on dev mode
	//$(window).bind('resize', function(){ if (window.RT) { clearTimeout(window.RT); } window.RT = setTimeout(function(){ this.location.reload(false);}, 100); });
	
});