//	Like
jQuery(document).ready(function($){
	$(document).on('click','.wpforo-like', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpflike", "");
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data: {
	   			postid: postid,
	   			likestatus: 1,
	   			action: 'wpforo_like_ajax'
	   		}
	   	}).done(function(response){
	   		try{
				response = $.parseJSON(response);
			} catch (e) {
				console.log(e);
			}
	   		if( response.stat == 1 ){
				$("#" + postid_value).removeClass('wpforo-like').addClass('wpforo-unlike');
		   		$("#likeicon" + postid).removeClass('fa-thumbs-o-up').addClass('fa-thumbs-o-down');
		   		$("#liketext" + postid).text(' ' + wpf_ajax_obj.phrases['unlike']);
		   		$("#post-" + postid + " .bleft").html(response.likers);
			}
	   		$('#wpforo-load').invisible();
			wpforo_notice_show(response.notice);
	   	});
	});
	
	$(document).on('click','.wpforo-unlike', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpflike", "");
		
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data: {
	   			postid: postid,
	   			likestatus: 0,
	   			action: 'wpforo_like_ajax'
	   		}
	   	}).done(function(response){
	   		try{
				response = $.parseJSON(response);
			} catch (e) {
				console.log(e);
			}
	   		if( response.stat == 1 ){
				$("#" + postid_value).removeClass('wpforo-unlike').addClass('wpforo-like');
		   		$("#likeicon" + postid).removeClass('fa-thumbs-o-down').addClass('fa-thumbs-o-up');
		   		$("#liketext" + postid).text(' ' + wpf_ajax_obj.phrases['like']);
		   		$("#post-" + postid + " .bleft").html(response.likers);
			}
	   		$('#wpforo-load').invisible();
			wpforo_notice_show(response.notice);
	   	});
	});
	
	
//	Vote
	$(document).on('click','.voteup', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var vote = 'up';
		var itemtype = $(this).attr('itemtype');
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpfvote-up-", "");
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data: {
	   			itemtype: itemtype,
	   			postid: postid,
	   			votestatus: vote,
	   			action: 'wpforo_vote_ajax'
	   		}
	   	}).done(function(response){
	   		try{
			   	response = $.parseJSON(response);
			   } catch (e) {
			   	console.log(e);
			   }
	   		if( response.stat == 1 ){
	   			count = document.getElementById( 'wpfvote-num-' + postid ).innerHTML;
				$( '#wpfvote-num-' + postid ).replaceWith( ++count ).fadeIn();;
			}
	   		$('#wpforo-load').invisible();
			wpforo_notice_show(response.notice);
	   	});
	});
	
	$(document).on('click','.votedown', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var vote = 'down';
		var itemtype = $(this).attr('itemtype');
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpfvote-down-", "");
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data: {
	   			itemtype: itemtype,
	   			postid: postid,
	   			votestatus: vote,
	   			action: 'wpforo_vote_ajax'
	   		}
	   	}).done(function(response){
	   		try{
			   	response = $.parseJSON(response);
			   } catch (e) {
			   	console.log(e);
			   }
	   		if(response.stat == 1){
				count = document.getElementById( 'wpfvote-num-' + postid ).innerHTML;
				$( '#wpfvote-num-' + postid ).replaceWith( --count ).fadeIn();;
			}
	   		$('#wpforo-load').invisible();
	   		wpforo_notice_show(response.notice);
	   	});
	});
	
	
//	Answer
	$(document).on('click','.wpf-toggle-answer', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpf-answer-", "");
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data: {
	   			postid: postid,
	   			answerstatus: 0,
	   			action: 'wpforo_answer_ajax'
	   		}
	   	}).done(function(response){
	   		try{
				response = $.parseJSON(response);
			} catch (e) {
				console.log(e);
			}
	   		if( response.stat == 1 ){
				$("#wpf-answer-" + postid).removeClass('wpf-toggle-answer').addClass('wpf-toggle-not-answer');
			}
	   		$('#wpforo-load').invisible();
			wpforo_notice_show(response.notice);
	   	});
	});
	
	$(document).on('click','.wpf-toggle-not-answer', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpf-answer-", "");
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data: {
	   			postid: postid,
	   			answerstatus: 1,
	   			action: 'wpforo_answer_ajax'
	   		}
	   	}).done(function(response){
	   		try{
				response = $.parseJSON(response);
			} catch (e) {
				console.log(e);
			}
	   		if( response.stat == 1 ){
	   			$("#wpf-answer-" + postid).removeClass('wpf-toggle-not-answer').addClass('wpf-toggle-answer');
			}
	   		$('#wpforo-load').invisible();
			wpforo_notice_show(response.notice);
	   	});
	});
	
	
	
//	Quote
	$(".wpforo-quote").click(function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		$("#wpf-reply-form-title").html('Reply with quote');
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpfquotepost", "");
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data: {
	   			postid: postid,
	   			action: 'wpforo_quote_ajax'
	   		}
	   	}).done(function(response){
	   		tinyMCE.activeEditor.setContent(response);
	   		$( ".wpf-topic-sbs" ).show();
			$( "#wpf-topic-sbs" ).prop("disabled", false);
	   		
   			$( "#formaction" ).attr('name', 'post[action]');
			$( "#formbutton" ).attr('name', 'post[save]');
			$( "#formtopicid" ).attr('name', 'post[topicid]');
			$( "#title" ).attr('name', 'post[title]');
	   		$( "#formbutton" ).val( wpf_ajax_obj.phrases.save );
	   		$( "#title").val( wpf_ajax_obj.phrases['re'] + ": " + $("#title").attr('placeholder').replace( wpf_ajax_obj.phrases['re'] + ": ", "").replace( wpf_ajax_obj.phrases['answer to'] + ": ", "") );
	   		$('html, body').animate({scrollTop: $("#wpf-form-wrapper").offset().top}, 1000);
	   		
			tinymce.execCommand('mceFocus',false,'postbody');
			tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
			tinyMCE.activeEditor.selection.collapse(false);
	   		$('#wpforo-load').invisible();
	   	});
	});
	
	
	
//	Report
	$( ".wpforo-report" ).click(function(){
		$("#wpf-msg-box").hide();  
		$('#wpforo-load').visible();
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpfreport", "");
		$('#reportpostid').attr('value', postid);
		
		var dialog;
		var w = jQuery(window).width();
	    var h = jQuery(window).height();
	    var dialogWidth = 600;
	    var dialogHeight = 250;
	    H = ( dialogHeight < h ) ? dialogHeight : (h-40);
	    W = ( dialogWidth < w ) ? dialogWidth : (w-20);
		
		dialog = jQuery( "#reportdialog" ).dialog({
			create: function(event, ui) {
		        jQuery(event.target).parent().css('position', 'fixed');
		    },
		    close: function( event, ui ) {
		    	jQuery("#wpf_attach_dialog").html('<div style="margin-top: 20%; margin-left: auto; margin-right: auto; display: table;"><i class="fa fa-spinner fa-spin fa-5x"></i></div>');
		    },
			autoOpen: false,
		    height: H,
		    width: W,
		    modal: true,
			dialogClass:'wpforo-dialog wpforo-dialog-report'
		});
		
		dialog.dialog( "open" );
		$('#wpforo-load').invisible();
	});
	
	$( "#sendreport" ).click(function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var postid = $('#reportpostid').attr('value');
		var messagecontent = $('#reportmessagecontent').attr('value'); 
		
		$.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data:{
				postid: postid,
				reportmsg: messagecontent,
				action: 'wpforo_report_ajax'
			}
	   	}).done(function(response){
	   		try{
			   	response = $.parseJSON(response);
		    } catch (e) {
		   		console.log(e);
		    }
	   		jQuery( "#reportdialog" ).dialog('close');
	   		$('#wpforo-load').invisible();
	   		wpforo_notice_show(response);
	   	});
	});
	
	
//	Sticky
	$(document).on('click','.wpforo-sticky', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var status_value = 'sticky';
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpfsticky", "");
		
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data:{
	   			postid: postid,
	   			status: status_value,
	   			action: 'wpforo_sticky_ajax'
	   		}
	   	}).done(function(response){
	   		if(response != 0){
		   		$("#" + postid_value).removeClass('wpforo-sticky').addClass('wpforo-unsticky');
		   		$("#stickytext" + postid).text(' ' + wpf_ajax_obj.phrases.unsticky);
		   	}
	   		$('#wpforo-load').invisible();
	   	});
	});
	
	$(document).on('click','.wpforo-unsticky', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var status_value = 'unsticky';
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpfsticky", "");
		
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data:{
	   			postid: postid,
	   			status: status_value,
	   			action: 'wpforo_sticky_ajax'
	   		}
	   	}).done(function(response){
	   		if(response != 0){
		   		$("#" + postid_value).removeClass('wpforo-unsticky').addClass('wpforo-sticky');
		   		$("#stickytext" + postid).text(' ' + wpf_ajax_obj.phrases.sticky);
		   	}
	   		$('#wpforo-load').invisible();
	   	});
	});
	
//	Private
	$(document).on('click','.wpforo-private', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var status_value = 'private';
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpfprivate", "");
		
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data:{
	   			postid: postid,
	   			status: status_value,
	   			action: 'wpforo_private_ajax'
	   		}
	   	}).done(function(response){
	   		if(response != 0){
		   		$("#" + postid_value).removeClass('wpforo-private').addClass('wpforo-public');
				$("#privateicon" + postid).removeClass('fa-eye-slash').addClass('fa-eye');
		   		$("#privatetext" + postid).text(' ' + wpf_ajax_obj.phrases.public);
		   	}
	   		$('#wpforo-load').invisible();
	   	});
	});
	
	$(document).on('click','.wpforo-public', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var status_value = 'public';
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpfprivate", "");
		
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data:{
	   			postid: postid,
	   			status: status_value,
	   			action: 'wpforo_private_ajax'
	   		}
	   	}).done(function(response){
	   		if(response != 0){
		   		$("#" + postid_value).removeClass('wpforo-public').addClass('wpforo-private');
				$("#privateicon" + postid).removeClass('fa-eye').addClass('fa-eye-slash');
		   		$("#privatetext" + postid).text(' ' + wpf_ajax_obj.phrases.private);
		   	}
	   		$('#wpforo-load').invisible();
	   	});
	});
	
//	Solved
	$(document).on('click','.wpforo-solved', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var status_value = 'solved';
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpfsolved", "");
		
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data:{
	   			postid: postid,
	   			status: status_value,
	   			action: 'wpforo_solved_ajax'
	   		}
	   	}).done(function(response){
	   		if(response != 0){
		   		$("#" + postid_value).removeClass('wpforo-solved').addClass('wpforo-unsolved');
		   		$("#solvedtext" + postid).text(' ' + wpf_ajax_obj.phrases.unsolved);
		   	}
	   		$('#wpforo-load').invisible();
	   	});
	});
	
	$(document).on('click','.wpforo-unsolved', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var status_value = 'unsolved';
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpfsolved", "");
		
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data:{
	   			postid: postid,
	   			status: status_value,
	   			action: 'wpforo_solved_ajax'
	   		}
	   	}).done(function(response){
	   		if(response != 0){
		   		$("#" + postid_value).removeClass('wpforo-unsolved').addClass('wpforo-solved');
		   		$("#solvedtext" + postid).text(' ' + wpf_ajax_obj.phrases.solved);
		   	}
	   		$('#wpforo-load').invisible();
	   	});
	});
	
	
//	Close
	$(document).on('click','.wpforo-close', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var status_value = 'close';
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpfclose", "");
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data:{
	   			postid: postid,
	   			status: status_value,
	   			action: 'wpforo_close_ajax'
	   		}
	   	}).done(function(response){
	   		if(response != 0){
		   		$("#" + postid_value).removeClass('wpforo-close').addClass('wpforo-open');
		   		$("#closeicon" + postid).removeClass('fa-lock').addClass('fa-unlock');
		   		$("#closetext" + postid).text(' ' + wpf_ajax_obj.phrases.open);
		   		$("#wpf-form-wrapper").remove();
		   		$(".wpforo-reply").remove();
		   		$(".wpforo-quote").remove();
		   		$(".wpforo-edit").remove();
			}
	   		$('#wpforo-load').invisible();
	   	});
	});
	
	$(document).on('click','.wpforo-open', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var status_value = 'closed';
		var postid_value = $(this).attr('id');
		var postid = postid_value.replace("wpfclose", "");
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data:{
	   			postid: postid,
	   			status: status_value,
	   			action: 'wpforo_close_ajax'
	   		}
	   	}).done(function(response){
	   		if(response != 0){
		   		/*$("#" + postid_value).removeClass('wpforo-open').addClass('wpforo-close');
		   		$("#closeicon" + postid).removeClass('fa-unlock').addClass('fa-lock');
		   		$("#closetext" + postid).text(' ' + wpf_ajax_obj.phrases.close);*/
		   		window.location.assign(response);
		   	}
	   		$('#wpforo-load').invisible();
	   		
	   	});
	});
	
	
//	Edit
	$(document).on('click','.wpforo-edit', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		$("#wpf-reply-form-title").html('Edit post');
		var postid_value = $(this).attr('id');
		var is_topic = postid_value.indexOf("topic");
		
		if(is_topic == -1){
			var postid = postid_value.replace("wpfedit", "");
		}else{
			var postid = postid_value.replace("wpfedittopicpid", "");
		}
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data: {
	   			postid: postid,
	   			action: 'wpforo_edit_ajax'
	   		}
	   	}).done(function(response){
	   		if(response != 0){
		   		try{
					response = $.parseJSON(response);
				}catch(e){
					console.log(e);
				}
		   		tinyMCE.activeEditor.setContent( response.body );
		   		$( ".wpf-topic-sbs" ).hide();
		   		$( "#wpf-topic-sbs" ).prop("disabled", true);
		   		
		   		$( "#formaction" ).val( 'edit' );
		   		$( "#formpostid" ).val( postid );
				$( "#formbutton" ).val( wpf_ajax_obj.phrases.update );
		   		$( 'html, body' ).animate({scrollTop: $("#wpf-form-wrapper").offset().top}, 1000);
		   		if(is_topic == -1){
	//	   			$( "#title" ).prop( "disabled", true );
		   			$( "#title").val( response.post_title );
					
					$( "#formaction" ).attr('name', 'post[action]');
					$( "#formbutton" ).attr('name', 'post[save]');
					$( "#formtopicid" ).attr('name', 'post[topicid]');
					$( "#title" ).attr('name', 'post[title]');
				}else{
	//				$( "#title" ).prop( "disabled", false );
					$( "#title").val( response.topic_title );
					
					$( "#formaction" ).attr('name', 'topic[action]');
					$( "#formbutton" ).attr('name', 'topic[save]');
					$( "#formtopicid" ).attr('name', 'topic[topicid]');
					$( "#title" ).attr('name', 'topic[title]');
				}
				
	   			tinymce.execCommand('mceFocus',false,'postbody');
				tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
				tinyMCE.activeEditor.selection.collapse(false);
			}
			
   			$('#wpforo-load').invisible();
   			
	   	});
	});
	
	
//	Delete
	$(document).on('click','.wpforo-delete', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		
		var ok = confirm(wpforo_ucwords(wpf_ajax_obj.phrases["are you sure you want to delete?"]));
		
		if (ok == true){
			var postid_value = $(this).attr('id');
			var is_topic = postid_value.indexOf("topic");
			
			if(is_topic == -1){
				var postid = postid_value.replace("wpfreplydelete", "");
				var status_value = 'reply';
			}else{
				var postid = postid_value.replace("wpftopicdelete", "");
				var status_value = 'topic';
			}
			
			var forumid = $("input[type='hidden']#parent").val();
			
		  	$.ajax({
		   		type: 'POST',
		   		url: wpf_ajax_obj.url,
		   		data:{
		   			forumid: forumid,
		   			postid: postid,
		   			status: status_value,
		   			action: 'wpforo_delete_ajax'
		   		}
		   	}).done(function(response){
		   		try{
					response = $.parseJSON(response);
				} catch (e) {
					console.log(e);
				}
		   		if( response.stat == 1 ){
					if(is_topic == -1){
						$('#post-'+response.postid).fadeOut().delay(200);
					}else{
						window.location.assign(response.location);	
					}
					$('#wpforo-load').invisible();
				}else{
					$('#wpforo-load').invisible();
				}
				
				wpforo_notice_show(response.notice);
		   	});
		}else{
			$('#wpforo-load').invisible();
		}
	});
	
	
//	Subscribe
	$(document).on('click','.wpf-subscribe-forum, .wpf-subscribe-topic', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var type = '';
		var status = 'subscribe';
		var clases = $(this).attr('class');
		
		if( clases.indexOf("wpf-subscribe-forum") > -1 ){
	    	type = 'forum';
		}
		if( clases.indexOf("wpf-subscribe-topic") > -1 ){
			type = 'topic';
		}
		
		var postid_value = $(this).attr('id');
		var itemid = postid_value.replace("wpfsubscribe-", "");
		
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data: {
	   			itemid: itemid,
	   			type: type,
	   			status: status,
	   			action: 'wpforo_subscribe_ajax'
	   		}
	   	}).done(function(response){
	   		try{
			   	response = $.parseJSON(response);
			   } catch (e) {
			   	console.log(e);
			   }
	   		if( response.stat == 1 ){
	   			$("#wpfsubscribe-" + itemid).removeClass('wpf-subscribe-' + type).addClass('wpf-unsubscribe-' + type);
	   			$("#wpfsubscribe-" + itemid).text( ' ' + wpf_ajax_obj.phrases.unsubscribe );
	   			$('#wpforo-load').invisible();
			}else{
				$('#wpforo-load').invisible();
			}
			
			wpforo_notice_show(response.notice);
	   	});
	   	
	});
	
	$(document).on('click','.wpf-unsubscribe-forum, .wpf-unsubscribe-topic', function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
		var type = '';
		var button_phrase = '';
		var status = 'unsubscribe';
		var clases = $(this).attr('class');
		if( clases.indexOf("wpf-unsubscribe-forum") > -1 ){
	    	type = 'forum';
	    	button_phrase = wpforo_ucwords(wpf_ajax_obj.phrases["subscribe for new topics"]);
		}
		if( clases.indexOf("wpf-unsubscribe-topic") > -1 ){
			type = 'topic';
			button_phrase = wpforo_ucwords(wpf_ajax_obj.phrases["subscribe for new replies"]);
		}
		var postid_value = $(this).attr('id');
		var itemid = postid_value.replace("wpfsubscribe-", "");
		
	   $.ajax({
	   		type: 'POST',
	   		url: wpf_ajax_obj.url,
	   		data: {
	   			itemid: itemid,
	   			type: type,
	   			status: status,
	   			action: 'wpforo_subscribe_ajax'
	   		}
	   	}).done(function(response){
	   		try{
			   	response = $.parseJSON(response);
			   } catch (e) {
			   	console.log(e);
			   }
	   		if( response.stat == 1 ){
	   			$("#wpfsubscribe-" + itemid).removeClass('wpf-unsubscribe-' + type).addClass('wpf-subscribe-' + type);
		   		$("#wpfsubscribe-" + itemid).text( ' ' + button_phrase );
		   		$('#wpforo-load').invisible();
			}else{
				$('#wpforo-load').invisible();
			}
			
			wpforo_notice_show(response.notice);
	   	});
	});
});

function wpforo_ucwords (str) {
    return (str + '').replace(/^([a-z])|\s+([a-z])/, function ($1) {
        return $1.toUpperCase();
    });
}