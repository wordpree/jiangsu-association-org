wp.customize( 'js_depart_bg', function( value ) {
		value.bind( function( newval ) {
			$('.js-depart .section-title').css('background-image', 'url('+newval+')' );
		} );
	} );
