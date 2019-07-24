jQuery.fn.extend({
    visible: function() {
        return this.css('visibility', 'visible');
    },
    invisible: function() {
        return this.css('visibility', 'hidden');
    },
    visibilityToggle: function() {
        return this.css('visibility', function(i, visibility) {
            return (visibility === 'visible') ? 'hidden' : 'visible';
        });
    },
    showFlex: function() {
        return this.css('display', 'flex');
    },
    wpfInsertAtCaret: function (myValue) {
        return this.each(function (i) {
            if (document.selection) {
                //For browsers like Internet Explorer
                this.focus();
                var sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
            } else if ( this.selectionStart || this.selectionStart == '0' ) {
                //For browsers like Firefox and Webkit based
                var startPos = this.selectionStart;
                var endPos = this.selectionEnd;
                var scrollTop = this.scrollTop;
                this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
                this.focus();
                this.selectionStart = startPos + myValue.length;
                this.selectionEnd = startPos + myValue.length;
                this.scrollTop = scrollTop;
            } else {
                this.value += myValue;
                this.focus();
            }
        });
    }
});

function wpforo_tinymce_initializeIt(selector, do_not_focus) {
    tinyMCE.init({
        forced_root_block: "",
        force_br_newlines: false,
        force_p_newlines: true,
        selector: selector,
        plugins: "hr,lists,textcolor,paste,wpautoresize,fullscreen,wpforo_pre_button,wpforo_link_button,wpforo_spoiler_button,wpforo_source_code_button,emoticons",
        menubar: "",
        toolbar: "fontsizeselect,bold,italic,underline,forecolor,bullist,numlist,alignleft,aligncenter,alignright,link,unlink,blockquote,pre,wpf_spoil,source_code,emoticons,fullscreen",
        content_style: 'p{color:#333333; font-weight: normal;} blockquote{border: #cccccc 1px dotted; background: #F7F7F7; padding:10px;font-size:12px; font-style:italic; margin: 20px 10px;}',
        branding: false,
        elementpath: false,
        autoresize_on_init: true,
        wp_autoresize_on: true,
        min_height: 100,
        height: 100,
        statusbar: true,
        fix_list_elements: true,
        browser_spellcheck: true,
        setup: 'wpforo_tinymce_setup'
    }).then(function (e) {
        if(!do_not_focus && e.length) {
            wpforo_editor.focus(e[0].id);
            wpforo_editor.set_active(e[0].id);
        }
    });
}

function wpforo_tinymce_setup(editor) {
    editor.on('focus', function(e) {
        wpforo_editor.set_active(editor.id);
    });
    editor.on('paste', function(e) {
        jQuery('form[data-textareaid='+editor.id+']').trigger({type: 'paste', delegatedEvent: {originalEvent: {clipboardData: e.clipboardData}},originalEvent: {clipboardData: e.clipboardData}});
    });
    editor.shortcuts.add('ctrl+13', 'submit', function(){jQuery('form[data-textareaid='+editor.id+']').find('[type=submit]').click();});
}

var wpforo_editor = {
    active_textareaid: '',
    main_textareaid: '',
    fix_textareaid: function (textareaid) {
        if( typeof textareaid !== 'undefined' ){
            return textareaid;
        }else if( this.active_textareaid ){
            return this.active_textareaid;
        }else{
            var tinymce_active_editor_id = this.get_tinymce_active_editor_id();
            if( tinymce_active_editor_id ){
                this.active_textareaid = tinymce_active_editor_id;
                return tinymce_active_editor_id;
            }
        }
        return '';
    },
    set_active: function(textareaid){
        if( this.is_exists(textareaid) ){
            this.active_textareaid = textareaid;
            if( this.is_tinymce(textareaid) ) tinymce.setActive( tinymce.get(textareaid) );
        }
    },
    clear_active: function(){
        this.active_textareaid = '';
    },
    set_main: function(textareaid, also_set_active){
        if( !textareaid ){
            var wpforo_main_form = jQuery( 'form.wpforo-main-form[data-textareaid]' );
            if( wpforo_main_form.length ) textareaid = wpforo_main_form.data('textareaid');
        }
        if( this.is_exists(textareaid) ){
            this.main_textareaid = textareaid;
            if(also_set_active) this.set_active(textareaid);
        }
    },
    get_main: function(){
        if( !this.main_textareaid ) this.set_main();
        return this.main_textareaid;
    },
    clear_main: function(){
        this.main_textareaid = '';
    },
    get_tinymce_active_editor_id: function(){
        if( this.is_tinymce_loaded() && typeof tinymce.activeEditor === "object" && tinymce.activeEditor.id ){
            return tinymce.activeEditor.id;
        }
        return '';
    },
    is_tinymce_loaded: function (){
        return typeof tinymce !== "undefined";
    },
    is_tinymce: function (textareaid){
        textareaid = this.fix_textareaid(textareaid);
        return !!( textareaid && this.is_tinymce_loaded() && tinymce.get(textareaid) );
    },
    is_textarea: function (textareaid){
        textareaid = this.fix_textareaid(textareaid);
        return !!( textareaid && !this.is_tinymce(textareaid) && jQuery( 'textarea#' + textareaid ).length );
    },
    is_exists: function(textareaid){
        return !!( textareaid && this.is_tinymce(textareaid) || this.is_textarea(textareaid) );
    },
    tinymce_focus: function(textareaid, caret_to_end){
        textareaid = this.fix_textareaid(textareaid);
        if( this.is_tinymce(textareaid) ){
            var focus_mce = tinymce.get(textareaid);
            focus_mce.focus();
            if(caret_to_end){
                focus_mce.selection.select(focus_mce.getBody(), true);
                focus_mce.selection.collapse(false);
            }
        }
    },
    textarea_focus: function(textareaid, caret_to_end){
        textareaid = this.fix_textareaid(textareaid);
        if( this.is_textarea(textareaid) ){
            var textarea = jQuery( 'textarea#' + textareaid );
            var textarea_val = textarea.val();
            textarea.focus();
            if( caret_to_end ){
                textarea.val('');
                textarea.val(textarea_val);
            }
        }
    },
    focus: function(textareaid, caret_to_end){
        textareaid = this.fix_textareaid(textareaid);
        if( this.is_tinymce(textareaid) ){
            this.tinymce_focus(textareaid, caret_to_end)
        }else if( this.is_textarea(textareaid) ){
            this.textarea_focus(textareaid, caret_to_end);
        }
    },
    insert_content: function (content, textareaid){
        textareaid = this.fix_textareaid(textareaid);
        if( this.is_tinymce(textareaid) ){
            tinymce.get(textareaid).insertContent(content);
            this.tinymce_focus(textareaid);
        }else if( this.is_textarea(textareaid) ){
            jQuery( 'textarea#' + textareaid ).wpfInsertAtCaret(content);
            this.textarea_focus(textareaid);
        }
    },
    set_content: function (content, textareaid){
        textareaid = this.fix_textareaid(textareaid);
        if( this.is_tinymce(textareaid) ){
            tinymce.get(textareaid).setContent(content);
            this.tinymce_focus(textareaid, true);
        }else if( this.is_textarea(textareaid) ){
            jQuery( 'textarea#' + textareaid ).val(content);
            this.textarea_focus(textareaid, true);
        }
    },
    get_content: function (format, textareaid){
        textareaid = this.fix_textareaid(textareaid);
        format = format ? format : 'text';
        var content = '';
        if( this.is_tinymce(textareaid) ){
            content = tinymce.get(textareaid).getContent({format: format});
        }else if( this.is_textarea(textareaid) ){
            content = jQuery( 'textarea#' + textareaid ).val();
            if( format === 'text' && content ) {
                content = content.replace(/<(iframe|embed)[^<>]*?>.*?<\/\1>/gi, "");
                content = content.replace(/(<([^<>]+?)>)/gi, "");
            }
        }
        return content.trim();
    },
    get_stats: function (textareaid){
        textareaid = this.fix_textareaid(textareaid);

        var text = this.get_content('text', textareaid);
        var raw_text = this.get_content('raw', textareaid);

        return {
            chars: text.length,
            words: text.split(/[\w\u2019'-]+/).length - 1,
            imgs: (raw_text.match(/<img[^<>]*?src=['"][^'"]+?['"][^<>]*?>/gi) || []).length,
            links: (raw_text.match(/<a[^<>]*?href=['"][^'"]+?['"][^<>]*?>.+?<\/a>/gi) || []).length,
            embeds: (raw_text.match(/<(iframe|embed)[^<>]*?>.*?<\/\1>/gi) || []).length
        };
    }
};

function wpforo_notice_clear() {
    var msg_box = jQuery("#wpf-msg-box");
    msg_box.hide();
    msg_box.empty();
}

function wpforo_notice_show(notice, type){
    if( !notice ) return;
    type = ( type === 'success' || type === 'error' ? type : 'neutral' );

    var n = notice.search(/<p(?:\s[^<>]*?)?>/i);
    if( n < 0 ){
        var phrase = wpforo_phrase(notice);
        if( arguments.length > 2 ){
            var i;
            for( i = 2; i < arguments.length; i++ ){
                if( arguments[i] !== undefined ) phrase = phrase.replace(/%[dfs]/, arguments[i]);
            }
        }
        notice = '<p class="'+ type +'">' + phrase + '</p>';
    }

    notice = jQuery(notice);
	var msg_box = jQuery("#wpf-msg-box");
	msg_box.append(notice);
	msg_box.show(150);
    notice.delay(type === 'error' ? 6500 : 2500).fadeOut(200, function () {
        jQuery(this).remove();
    });
}

function wpforo_phrase(phrase_key){
    if( typeof wpforo_phrases === 'object' && Object.keys(wpforo_phrases).length ){
        phrase_key = phrase_key.toLowerCase();
        if( wpforo_phrases[phrase_key] !== undefined ) phrase_key = wpforo_phrases[phrase_key];
    }
    return phrase_key;
}

function wpforo_getTextSelection(){
    jQuery("#wpf_multi_quote").remove();
    if (window.getSelection) {
        var sel = window.getSelection();
        if ( sel && sel.anchorNode && sel.anchorNode.parentNode && sel.anchorNode.parentNode.tagName !== 'A' ) {
            var selectedText = sel.toString().trim();
            if ( sel.rangeCount && selectedText.length ) {
                var getRangeAt_0 = sel.getRangeAt(0);
                var rangeBounding = getRangeAt_0.getBoundingClientRect();
                var bodyBounding = document.documentElement.getBoundingClientRect();
                var left = rangeBounding.left + rangeBounding.width/2 + Math.abs( bodyBounding.left ) - 15;
                var top = rangeBounding.bottom + Math.abs( bodyBounding.top ) + 50;

                var parent = jQuery(getRangeAt_0.commonAncestorContainer).closest('.wpforo-post-content, .wpforo-comment-content');
                var noNeedParent = jQuery(getRangeAt_0.commonAncestorContainer).closest('.wpforo-post-signature, .wpforo-post-content-bottom, .wpf-post-button-actions');
                var noNeedChild = jQuery(getRangeAt_0.endContainer).closest('.wpforo-post-signature, .wpforo-post-content-bottom, .wpf-post-button-actions');

                if( parent.length && !noNeedParent.length && !noNeedChild.length ){
                    var toolTip = jQuery('<div id="wpf_multi_quote"></div>');
                    toolTip.css({top: top, left: left});
                    var link = jQuery('<span class="wpf-multi-quote" title="'+ wpforo_phrase('Quote this text') +'"><i class="fas fa-quote-left"></i></span>').on('mousedown touchstart', function () {
                        var container = document.createElement("div");
                        for (var i = 0; i < sel.rangeCount; ++i) container.appendChild(sel.getRangeAt(i).cloneContents());
                        var post_wrap = jQuery(getRangeAt_0.startContainer).parents('[data-postid]');
                        var userid = post_wrap.data('userid');
                        if( !userid ) userid = 0;
                        var postid = post_wrap.data('postid');
                        if( !postid ) postid = 0;
                        var mention_html = '';
                        var mention = post_wrap.data('mention');
                        if( mention ){
                            mention_html = '<div class="wpforo-post-quote-author"><strong> '+ wpforo_phrase('Posted by') +': @' + mention +' </strong></div>';
                        }else{
                            mention = '';
                        }
                        var editorContent = '<blockquote data-userid="'+ userid +'" data-postid="'+ postid +'" data-mention="'+ mention +'">'+ mention_html +'<p>' + container.innerHTML + '</p></blockquote><p></p>';
                        wpforo_editor.insert_content( editorContent, wpforo_editor.get_main() );
                        jQuery('html, body').animate({ scrollTop: jQuery("form.wpforo-main-form").offset().top }, 500);
                        jQuery(this).remove();
                    });
                    toolTip.append(link);
                    jQuery('body').append(toolTip);
                }
            }

        }
    }
}

jQuery(document).ready(function($){
	var wpforo_wrap = $('#wpforo-wrap');

    if ($('form.wpforo-main-form').length) {
        document.onselectionchange = function () {
            wpforo_getTextSelection();
        };
    }

    window.onbeforeunload = function(e) {
        var forms = $('form[data-textareaid]');
        if( forms.length ){
            var i, textareaid, textarea_stat;
            for( i = 0; i < forms.length; i++ ){
                textareaid = $( forms[i] ).data('textareaid');
                textarea_stat = wpforo_editor.get_stats(textareaid);
                if( textarea_stat.chars || textarea_stat.imgs || textarea_stat.links || textarea_stat.embeds ){
                    e = e || window.event;
                    e.returnValue = wpforo_phrase("Write something clever here..");
                    return wpforo_phrase("Write something clever here..");
                }
            }
        }
    };

    setTimeout(function () {
        wpforo_editor.fix_textareaid();
        wpforo_editor.set_main('', true);
    }, 1000);

	wpforo_wrap.on('click drop', 'form[data-textareaid]', function () {
        var textareaid = $(this).data('textareaid');
        wpforo_editor.set_active(textareaid);
    });

	wpforo_wrap.on('focus', 'form[data-textareaid] textarea', function () {
	    var textareaid = $(this).parents('form[data-textareaid]').data('textareaid');
        if( textareaid === this.id ) wpforo_editor.set_active(this.id);
    });

    wpforo_wrap.on('keydown', 'form[data-textareaid]', function (e) {
        if (e.ctrlKey && e.keyCode === 13) {
            $('[type=submit]', $(this)).click();
        }
    });

    /**
     * prevent multi submitting
     * disable form elements for 10 seconds
     */
    var wpforo_prev_submit_time = 0;
    wpforo_wrap.on('submit', 'form', function () {
        if( wpforo_prev_submit_time ){
            if( Date.now() - wpforo_prev_submit_time < 10000 ) return false;
        }else{
            var textareaid = $(this).data('textareaid');
            if( textareaid ){
                var bodyminlength = $(this).data('bodyminlength');
                var bodymaxlength = $(this).data('bodymaxlength');
                if( bodyminlength || bodymaxlength ){
                    var body_stat = wpforo_editor.get_stats(textareaid);
                    if( bodyminlength ){
                        if( body_stat.chars < bodyminlength && !body_stat.embeds && !body_stat.links && !body_stat.imgs ){
                            wpforo_notice_show('Content characters length must be greater than %d', 'error', bodyminlength);
                            return false;
                        }
                    }
                    if( bodymaxlength ){
                        if( body_stat.chars > bodymaxlength ){
                            wpforo_notice_show('Content characters length must be smaller than %d', 'error', bodymaxlength);
                            return false;
                        }
                    }
                }
            }

            $("#wpf-msg-box").hide();  $('#wpforo-load').visible();
            wpforo_prev_submit_time = Date.now();
            window.onbeforeunload = null;
            setTimeout(function () {
                wpforo_prev_submit_time = 0;
                $('#wpforo-load').invisible();
            }, 10000);
        }
    });

    wpforo_wrap.on('click', '.wpf-spoiler-head', function(){
        var spoiler_wrap = $(this).parents('.wpf-spoiler-wrap');
        if( spoiler_wrap.length ){
            spoiler_wrap = $(spoiler_wrap[0]);
            if( !spoiler_wrap.hasClass('wpf-spoiler-processing') ){
                spoiler_wrap.toggleClass("wpf-spoiler-open").addClass("wpf-spoiler-processing");
                var spoiler_body = $('.wpf-spoiler-body', spoiler_wrap);
                if( spoiler_body.length ){
                    var spoiler_chevron = $('.wpf-spoiler-chevron', spoiler_wrap);
                    $(spoiler_chevron[0]).toggleClass('fa-chevron-down fa-chevron-up');
                    $(spoiler_body[0]).slideToggle(500, function () {
                        spoiler_wrap.removeClass("wpf-spoiler-processing");
                        if( !spoiler_wrap.hasClass('wpf-spoiler-open') ){
                            $('.wpf-spoiler-wrap.wpf-spoiler-open .wpf-spoiler-head', spoiler_wrap).click();
                        }
                    });
                }
            }
        }
    });

	var _m = $("#m_");
	if( _m !== undefined && _m.length ){
        $('html, body').scrollTop(_m.offset().top - 25);
	}

    wpforo_wrap.on('click', '#add_wpftopic:not(.not_reg_user)', function(){
        var stat = $( ".wpf-topic-create" ).is( ":hidden" );
        $( ".wpf-topic-create" ).slideToggle( "slow" );
        $('#wpf_title').focus();
        wpforo_editor.set_content('');
        var add_wpftopic = '<i class="fas fa-times" aria-hidden="true"></i>';
        if( !stat ) add_wpftopic = $("#wpf_formbutton").val();
        $( "#add_wpftopic" ).html(add_wpftopic);
        $('html, body').animate({ scrollTop: ($("#add_wpftopic").offset().top - 35) }, 415);
	});

    wpforo_wrap.on('click', '.wpf-answer-button .wpf-button:not(.not_reg_user)', function(){
        $(this).closest('.wpf-bottom-bar').hide();
    });

    wpforo_wrap.on('click', '.wpfl-4 .add_wpftopic:not(.not_reg_user)', function(){
    	var wrap = $(this).parents('div.wpfl-4');
    	var form_wrap = $( ".wpf-topic-form-extra-wrap", wrap );
        var stat = form_wrap.is( ":hidden" );
        $(".wpfl-4 .add_wpftopic").html($(".wpfl-4 .add_wpftopic").data('phrase'));
        $(".wpf-topic-form-extra-wrap").slideUp("slow");
        var add_wpftopic = '';
        if( stat ){
            add_wpftopic = '<i class="fas fa-times" aria-hidden="true"></i>';
            form_wrap.slideDown("slow");
        }else{
            add_wpftopic = $(this).data('phrase');
            form_wrap.slideUp("slow");
        }
        var option_no_selected = $( 'option.wpf-topic-form-no-selected-forum' );
        option_no_selected.show();
        option_no_selected.prop('selected', true);
        $( '.wpf-topic-form-ajax-wrap').empty();
        $( this ).html(add_wpftopic);
        $('html, body').animate({ scrollTop: (wrap.offset().top -30 ) }, 415);
    });

    wpforo_wrap.on('click', '.not_reg_user', function(){
		$("#wpf-msg-box").hide();
		$('#wpforo-load').visible();
		$('#wpf-msg-box').show(150).delay(1000);
		$('#wpforo-load').invisible();
	});

    $(document).on('click', '#wpf-msg-box', function(){
		$(this).hide();
	});

	/* Home page loyouts toipcs toglle */
    wpforo_wrap.on('click', ".topictoggle", function(){
		var wpfload = $('#wpforo-load');
        wpfload.visible();
		
		var id = $(this).attr( 'id' );
		
		id = id.replace( "img-arrow-", "" );
		$( ".wpforo-last-topics-" + id ).slideToggle( "slow" );
		if( $(this).hasClass('topictoggle') && $(this).hasClass('fa-chevron-down') ){
            $( '#img-arrow-' + id ).removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }else{
            $( '#img-arrow-' + id ).removeClass('fa-chevron-up').addClass('fa-chevron-down');
        }
		
		id = id.replace( "button-arrow-", "" );
		$( ".wpforo-last-posts-" + id ).slideToggle( "slow" );
		if( $(this).hasClass('topictoggle') && $(this).hasClass('wpfcl-a') && $(this).hasClass('fa-chevron-down') ){
			$( '#button-arrow-' + id ).removeClass('fa-chevron-down').addClass('fa-chevron-up');
		}else{
			$( '#button-arrow-' + id ).removeClass('fa-chevron-up').addClass('fa-chevron-down');
		}

        wpfload.invisible();
	});
	
	/* Home page loyouts toipcs toglle */
    wpforo_wrap.on('click', ".wpforo-membertoggle", function(){
		var id = $(this).attr( 'id' );
		id = id.replace( "wpforo-memberinfo-toggle-", "" );
		$( "#wpforo-memberinfo-" + id ).slideToggle( "slow" );
		if( $(this).find( "i" ).hasClass('fa-caret-down') ){
			$(this).find( "i" ).removeClass('fa-caret-down').addClass('fa-caret-up');
		}else{
			$(this).find( "i" ).removeClass('fa-caret-up').addClass('fa-caret-down');
		}
	});

    /* Threaded Layout Hide Replies */
    wpforo_wrap.on('click', ".wpf-post-replies-bar", function(){
        var id = $(this).attr( 'id' );
        id = id.replace( "wpf-ttgg-", "" );
        $( "#wpf-post-replies-" + id ).slideToggle( "slow" );
        if( $(this).find( "i" ).hasClass('fa-angle-down') ){
            $(this).find( "i" ).removeClass('fa-angle-down').addClass('fa-angle-up');
            $(this).find( ".wpforo-ttgg" ).attr('wpf-tooltip', wpforo_phrase('Hide Replies'));
        }else{
            $(this).find( "i" ).removeClass('fa-angle-up').addClass('fa-angle-down');
            $(this).find( ".wpforo-ttgg" ).attr('wpf-tooltip', wpforo_phrase('Show Replies'));
        }
    });
	
	
    //Reply
    wpforo_wrap.on('click', ".wpforo-reply:not(.wpforo_layout_4)", function(){
		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();
        $('#wpf-form-wrapper').show();

		$("#wpf-reply-form-title").html( wpforo_phrase('Leave a reply') );

		var parentpostid = $(this).attr('id');
		parentpostid = parentpostid.replace("parentpostid", "");
		$("#wpf_postparentid").val( parentpostid );
		$( ".wpf-topic-sbs" ).show();
		$( "#wpf-topic-sbs" ).prop("disabled", false);
		
		$( "#wpf_formaction" ).attr('name', 'post[action]');
		$( "#wpf_formbutton" ).attr('name', 'post[save]');
		$( "#wpf_formtopicid" ).attr('name', 'post[topicid]');
		$( "#wpf_title" ).attr('name', 'post[title]');
        $(".wpforoeditor textarea.wpeditor").attr('name', 'post[body]');
		$( "#wpf_formaction" ).val( 'add' );
		$( "#wpf_formpostid" ).val( '' );
		$( "#wpf_formbutton" ).val( wpforo_phrase('Save') );
		$( "#wpf_title").val( wpforo_phrase('re') + ": " + $("#wpf_title").attr('placeholder').replace( wpforo_phrase('re') + ": ", ""));

		var mention = $(this).data('mention');
        var content = ( mention ? '<p>@' + mention + '</p><p></p>' : '' );

        wpforo_editor.set_content( content, wpforo_editor.get_main() );

        $('html, body').animate({ scrollTop: $("#wpf-form-wrapper").offset().top }, 500);

		$('#wpforo-load').invisible();
		
	});
	
	//Answer
    wpforo_wrap.on('click', ".wpforo-answer", function(){
		var phrase = wpforo_phrase('Save') ;
		if( $(this).data('phrase') !== undefined ) phrase = $(this).data('phrase');

		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();

        $('#wpf-form-wrapper').show();

		$("#wpf-reply-form-title").html( wpforo_phrase('Your answer') );

		$( "#wpf_formaction" ).attr('name', 'post[action]');
		$( "#wpf_formbutton" ).attr('name', 'post[save]');
		$( "#wpf_formtopicid" ).attr('name', 'post[topicid]');
		$( "#wpf_title" ).attr('name', 'post[title]');
        $(".wpforoeditor textarea.wpeditor").attr('name', 'post[body]');
		$( "#wpf_formaction" ).val( 'add' );
		$( "#wpf_formpostid" ).val( '' );
		$( "#wpf_formbutton" ).val( phrase );
		$( "#wpf_title").val( wpforo_phrase('Answer to') + ": " + $("#wpf_title").attr('placeholder').replace( wpforo_phrase('re') + ": ", "").replace( wpforo_phrase('Answer to') + ": ", ""));

        var mention = $(this).data('mention');
        var content = ( mention ? '<p>@' + mention + '</p><p></p>' : '' );

        wpforo_editor.set_content( content, wpforo_editor.get_main() );

        $('html, body').animate({ scrollTop: $("#wpf-form-wrapper").offset().top }, 500);

		$('#wpforo-load').invisible();
		
	});
	
	//Comment
    wpforo_wrap.on('click', ".wpforo-childreply", function(){
        var phrase = wpforo_phrase('Save') ;
        if( $(this).data('phrase') !== undefined ) phrase = $(this).data('phrase');

		$("#wpf-msg-box").hide();  $('#wpforo-load').visible();

        // $('#wpf-form-wrapper').show();

		$("#wpf-reply-form-title").html( wpforo_phrase('Leave a comment') );
		
		var parentpostid = $(this).attr('id');
		var postid = parentpostid.replace("parentpostid", "");
		$("#wpf_postparentid").val( postid );
		$( ".wpf-topic-sbs" ).show();
		$( "#wpf-topic-sbs" ).prop("disabled", false);
		
		$( "#wpf_formaction" ).attr('name', 'post[action]');
		$( "#wpf_formbutton" ).attr('name', 'post[save]');
		$( "#wpf_formtopicid" ).attr('name', 'post[topicid]');
		$( "#wpf_title" ).attr('name', 'post[title]');
        $(".wpforoeditor textarea.wpeditor").attr('name', 'post[body]');
		$( "#wpf_formaction" ).val( 'add' );
		$( "#wpf_formpostid" ).val( '' );
		$( "#wpf_formbutton" ).val( phrase );
		$( "#wpf_title").val( wpforo_phrase('re') + ": " + $("#wpf_title").attr('placeholder').replace( wpforo_phrase('re') + ": ", "").replace( wpforo_phrase('Answer to') + ": ", "") );

        var mention = $(this).data('mention');
        var content = ( mention ? '<p>@' + mention + '</p><p></p>' : '' );

        wpforo_editor.set_content( content, wpforo_editor.get_main() );

        $('html, body').animate({ scrollTop: $("#wpf-form-wrapper").offset().top }, 800);

		$('#wpforo-load').invisible();
	});

    wpforo_wrap.on('click', '.wpforo-qa-comment, .wpforo-reply.wpf-action.wpforo_layout_4', function () {
        var wrap = $(this).parents('.reply-wrap,.wpforo-qa-item-wrap');
        var post_wrap = $('.post-wrap', wrap);
        if( !post_wrap.length ) post_wrap = wrap;
        var parentid = post_wrap.data('postid');
        if (!parentid) parentid = post_wrap.attr('id').replace('post-', '');
        if (!parentid) parentid = 0;
        var form = $('.wpforo-post-form');
        var textareaid = form.data('textareaid');
        var textarea_wrap = $('.wpf_post_form_textarea_wrap', form);
        var textarea = $('#' + textareaid, textarea_wrap);
        var textareatype = textarea_wrap.data('textareatype');
        $('.wpf_post_parentid').val(parentid);
        $('.wpforo-qa-comment,.wpforo-reply.wpf-action.wpforo_layout_4').show();
        $(this).hide();
        $('.wpforo-portable-form-wrap', wrap).show();
        if( ! $('.wpforo-post-form', wrap).length ) form.appendTo($('.wpforo-portable-form-wrap', wrap));

        form.show();
        if( textareatype && textareatype === 'rich_editor' ){
            textarea_wrap.html('<textarea id="' + textareaid + '" class="wpf_post_body" name="post[body]"></textarea>');
            wpforo_tinymce_initializeIt( '#' + textareaid );
        }else{
            textarea.val('');
            textarea.focus();
        }

        var mention = $(this).data('mention');
        var content = ( mention ? '<p>@' + mention + '</p><p></p>' : '' );

        wpforo_editor.set_content( content, textareaid );
    });

    wpforo_wrap.on('click', '.wpf-button-close-form', function () {
        $(this).parents('.wpforo-portable-form-wrap').hide();
        $('.wpforo-post-form').hide();
        $('.wpforo-qa-comment,.wpforo-reply.wpf-action.wpforo_layout_4').show();
        wpforo_editor.set_content('');
    });
	
	//mobile menu responsive toggle
    wpforo_wrap.on('click', "#wpforo-menu .wpf-res-menu", function(){
		$("#wpforo-menu .wpf-menu").toggle();
	});
	var wpfwin = $(window).width();
	var wpfwrap = wpforo_wrap.width();
	if( wpfwin >= 602 && wpfwrap < 700 ){
        wpforo_wrap.on('focus', "#wpforo-menu .wpf-search-field", function(){
			$("#wpforo-menu .wpf-menu li").hide();
            wpforo_wrap.find("#wpforo-menu .wpf-res-menu").show();
			$("#wpforo-menu .wpf-search-field").css('transition-duration', '0s');
		});
        wpforo_wrap.on('blur', "#wpforo-menu .wpf-search-field", function(){
            wpforo_wrap.find("#wpforo-menu .wpf-res-menu").hide();
			$("#wpforo-menu .wpf-menu li").show();
			$("#wpforo-menu .wpf-search-field").css('transition-duration', '0.4s');
		});
	}
	
	// password show/hide switcher */
    wpforo_wrap.on('click', '.wpf-show-password', function () {
        var btn = $(this);
        var parent = btn.parents('.wpf-field-wrap');
        var input = $(':input', parent);
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            btn.removeClass('fa-eye-slash');
            btn.addClass('fa-eye');
        } else {
            input.attr('type', 'password');
            btn.removeClass('fa-eye');
            btn.addClass('fa-eye-slash');
        }
    });
	
	//Turn off on dev mode
	//$(window).bind('resize', function(){ if (window.RT) { clearTimeout(window.RT); } window.RT = setTimeout(function(){ this.location.reload(false);}, 100); });

    wpforo_wrap.on("change", "#wpforo_split_form #wpf_split_create_new", function () {
		var checked = $("#wpf_split_create_new").is(":checked"),
		target_url 	= $("#wpf_split_target_url"),
		append 		= $("#wpf_split_append"),
		new_title 	= $("#wpf_split_new_title"),
		forumid 	= $("#wpf_split_forumid");
		if( checked ){
            target_url.children("input").prop("disabled", true);
            target_url.hide();
            append.children("input").prop("disabled", true);
            append.hide();
            new_title.children("input").prop("disabled", false);
            new_title.show();
            forumid.children("select").prop("disabled", false);
            forumid.show();
		}else{
            target_url.children("input").prop("disabled", false);
            target_url.show();
            append.children("input").prop("disabled", false);
            append.show();
            new_title.children("input").prop("disabled", true);
            new_title.hide();
            forumid.children("select").prop("disabled", true);
            forumid.hide();
		}
    });

	//Facebook Share Buttons
	wpforo_wrap.on('click','.wpf-fb', function(){
        var item_url = $(this).data('wpfurl');
        var item_quote = $(this).parents('.post-wrap').find('.wpforo-post-content').text();
        FB.ui({
            method: 'share',
            href: item_url,
            quote: item_quote,
            hashtag: null
        }, function (response) {});
    });
    //Share Buttons Toggle
    wpforo_wrap.on('mouseover', '.wpf-sb', function(){
        $(this).find(".wpf-sb-toggle").find("i").addClass("wpfsa");
        $(this).find(".wpf-sb-buttons").show();
    });
    wpforo_wrap.on('mouseout', '.wpf-sb', function() {
        $(this).find(".wpf-sb-toggle").find("i").removeClass("wpfsa");
        $(this).find(".wpf-sb-buttons").hide();
    });
    wpforo_wrap.on('mouseover', '.wpf-sb-toggle', function(){
        $(this).next().filter('.wpf-sb-buttons').parent().find("i").addClass("wpfsa");
    });
    wpforo_wrap.on('mouseout', '.wpf-sb-toggle', function(){
        $(this).next().filter('.wpf-sb-buttons').parent().find("i").removeClass("wpfsa");
    });

    //Forum Rules
    wpforo_wrap.on('click', "#wpf-open-rules", function(){
        $(".wpforo-legal-rules").toggle();
        return false;
    });
    wpforo_wrap.on('click','#wpflegal-rules-yes', function(){
        $('#wpflegal_rules').prop('checked', true);
        $('#wpflegal-rules-not').removeClass('wpflb-active-not');
        $(this).addClass('wpflb-active-yes');
        setTimeout(function(){ $(".wpforo-legal-rules").slideToggle( "slow" ); }, 500);
    });
    wpforo_wrap.on('click','#wpflegal-rules-not', function(){
        $('#wpflegal_rules').prop('checked', false);
        $('#wpflegal-rules-yes').removeClass('wpflb-active-yes');
        $(this).addClass('wpflb-active-not');
    });

    //Forum Privacy Buttons
    wpforo_wrap.on('click', "#wpf-open-privacy", function(){
        $(".wpforo-legal-privacy").toggle();
        return false;
    });
    wpforo_wrap.on('click','#wpflegal-privacy-yes', function(){
        $('#wpflegal_privacy').prop('checked', true);
        $('#wpflegal-privacy-not').removeClass('wpflb-active-not');
        $(this).addClass('wpflb-active-yes');
        setTimeout(function(){ $(".wpforo-legal-privacy").slideToggle( "slow" ); }, 500);
    });
    wpforo_wrap.on('click','#wpflegal-privacy-not', function(){
        $('#wpflegal_privacy').prop('checked', false);
        $('#wpflegal-privacy-yes').removeClass('wpflb-active-yes');
        $(this).addClass('wpflb-active-not');
    });

    //Facebook Login Button
    wpforo_wrap.on('click', '#wpflegal_fblogin', function() {
        if( $(this).is(':checked') ){
            $('.wpforo_fb-button').attr('style','pointer-events:auto; opacity:1;');
        } else{
            $('.wpforo_fb-button').attr('style','pointer-events: none; opacity:0.6;');
        }
    });

    wpforo_wrap.on('click', '.wpf-load-threads .wpf-forums', function () {
		$( '.wpf-cat-forums', $(this).parents('div.wpfl-4') ).slideToggle('slow');
		$('i', $(this)).toggleClass('fa-chevron-down fa-chevron-up');
    });

});
