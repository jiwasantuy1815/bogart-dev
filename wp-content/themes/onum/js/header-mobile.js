(function($) {
  "use strict";

  	/* Header Default */
	if ( $('header').hasClass('cd-header') && ! $('header').hasClass('header__transparent') ) {
        var nav = $('.cd-header');
        var injectSpace = $('<div class="header__clone"></div>').insertAfter(nav);        
        $(window).on('load resize', function(){
        	var headerHeight = nav.outerHeight();
        	injectSpace.css( 'height', headerHeight).show();            
        });       
    } 

	/* ========================================== 
	Sticky Header 1
	========================================== */
	if ( $('header').hasClass('cd-header') ) {
	  	$(window).on( 'load scroll resize',
		{
	        previousTop: 0
	    }, 
	    function () {
	    	/*primary navigation slide-in effect*/	    	
	    	var headerHeight = $( '.cd-header' ).outerHeight();	
			var currentTop   = $(window).scrollTop();
			if ( $( '.header-topbar' ).length > 0 ) {
				if ( $(window).width() < 1199 ) {
		            var topbarHeight = 0;
		        } else {
		            var topbarHeight = $( '.header-topbar ' ).outerHeight();
		        }
			} else if ( $('.topbar__off').length > 0 ) {
				var topbarHeight = $( '.topbar__off' ).outerHeight();
	    	} else {
	    		var topbarHeight = 0;
	    	}	    	
	    	if ( $('#wpadminbar').length  > 0 ) {
	    		var adminbar     = $( '#wpadminbar' ).outerHeight();
	    	} else {
	    		var adminbar     = 0;
	    	}
		    /*check if user is scrolling up*/
		    if ( currentTop < this.previousTop ) {
		    	/*if scrolling up...*/
		    	if ( currentTop > 0 && $( '.cd-header' ).hasClass( 'is-fixed' ) ) {
		    		$( '.cd-header' ).addClass( 'is-visible' );
		    		$( '.mmenu__overlay, .mmenu_wrapper, .mmenu-overlay, .mmenu-wrapper' ).css( 'top', 0 );
		    	} else {
		    		$( '.cd-header' ).removeClass( 'is-visible is-fixed' ).css( 'top', 0 + adminbar );	
		    		$( '.cd-header .header__sticky' ).removeClass( 'is-header-sticky' );
                    $( '.cd-header .octf-btn-cta' ).removeClass( 'is-icon-sticky' );                    
		    	}
		    } else {
		    	/*if scrolling down...*/
		    	$( '.cd-header' ).removeClass( 'is-visible' );
		    	if ( currentTop > headerHeight && !$( '.cd-header' ).hasClass( 'is-fixed' ) ){
		    		$( '.cd-header' ).addClass( 'is-fixed' );		    		
		    		$( '.cd-header.is-fixed' ).css( 'top', -( headerHeight - topbarHeight - adminbar ) );
		    		$( '.mmenu__overlay, .mmenu_wrapper, .mmenu-overlay, .mmenu-wrapper' ).css( 'top', headerHeight );
		    		$( '.cd-header .header__sticky' ).addClass( 'is-header-sticky' );
                    $( '.cd-header .octf-btn-cta' ).addClass( 'is-icon-sticky' );
		    	} 
		    }
		    this.previousTop = currentTop;
		});
	}

    /* --------------------------------------------------
    * Side Panel
    * --------------------------------------------------*/
    var panel_btn = $('#panel-btn'),
    sidebar = $('#side-panel');
    function panel_handler() {
        var isActive = !panel_btn.hasClass('active');
        panel_btn.toggleClass('active', isActive);
        sidebar.toggleClass('side-panel-open', isActive);
        $('body').toggleClass('side-panel-active', isActive);
        return false;
    }
    $('#panel-btn, .side-panel-close, .panel-overlay').on('click', panel_handler);
	
	/* ========================================== 
	Search on Header
	========================================== */
	$('.toggle_click_search_1').on("click", function(){
		$(this).toggleClass( "active" );
        $('.h-search-form-field').toggleClass('show');
        if ($(this).find('i').hasClass( "flaticon-search" )) {
       		$('.toggle_search > i').removeClass( "flaticon-search" ).addClass("flaticon-delete");
        }else{
       		$('.toggle_search > i').removeClass( "flaticon-delete" ).addClass("flaticon-search");
        }
        $('.h-search-form-inner > form > input.search-field').focus();
    });

    /* ========================================== 
	Header Mobile
	========================================== */
	var mmenu_toggle = $('#mmenu_toggle'),
        mmenu        = $('#mmenu_wrapper');

    function mmenu_handler() {
        var isActive = !mmenu_toggle.hasClass('active');

        mmenu_toggle.toggleClass('active', isActive);
        mmenu.toggleClass('mmenu__open', isActive);
        $('body').toggleClass('mmenu__active', isActive);
        return false;
    }

    $('#mmenu_toggle, .mmenu__close, .mmenu__overlay').on('click', mmenu_handler);

	/* mmenu_wrapper create span */
	$('.mmenu_wrapper li:has(ul)').prepend('<span class="arrow"><i class="flaticon-arrow-point-to-right"></i></span>');
	$(".mmenu_wrapper .mobile_mainmenu > li span.arrow").click(function() {
        $(this).parent().find("> ul").stop(true, true).slideToggle()
        $(this).toggleClass( "active" ); 
    });

})(jQuery);
