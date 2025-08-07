( function( $ ) {
    'use strict';

    function rtl_slick() {
        if ( $( 'body' ).hasClass( 'rtl' ) ) {
           return true;
        } else {
           return false;
        }
    };

    function rtl_isotop() {
        if ( $( 'body' ).hasClass( 'rtl' ) ) {
           return false;
        } else {
           return true;
        }
    };

    /* --------------------------------------------------
     * Services Carousel
     * --------------------------------------------------*/
    var iserviceCarousel = function () {
        $('.service__carousel-outside').each(function() {
            var screen_width = $(window).width();
            if ( screen_width > 1200 ) {                
                var margin = screen_width - 1170;                
            } else {
                var margin = 0;
            }
            $('.service_carousel-wrap').css( 'margin-right', -margin/2 );
        });
    };

    var serviceCarousel = function () {
        iserviceCarousel();
        $(window).on( 'resize', function() {
            iserviceCarousel();
        });
    };
    
    /* --------------------------------------------------
	 * counter
	 * --------------------------------------------------*/
    var icounter = function () {
    	$('.ot-counter').each(function() {
    		var pos_y   = $(this).offset().top - window.innerHeight;
            var $this   = $(this).find('span.num'),
                countTo = $this.attr('data-to'),
                during  = parseInt( $this.attr('data-time') ),
                topOfWindow = $(window).scrollTop();

            if ( pos_y < topOfWindow ) {    
	            $({
	                countNum: $this.text()
	            }).animate({
	                countNum: countTo
	            },
	            {
	                duration: during,
	                easing: 'swing',
	                step: function() {
	                    $this.text(Math.floor(this.countNum));
	                },
	                complete: function() {
	                    $this.text(this.countNum);
	                }
	            });
	        }
        });
    };

    var counter = function () {
		icounter();
		$(window).on('scroll', function() {
			icounter();
		});
	};

    /* --------------------------------------------------
	 * progress bar
	 * --------------------------------------------------*/
	function lineProgress() {
		$('.ot-progress').each(function() {
			var pos_y = $(this).offset().top;
			var value = $(this).find(".progress-bar").data('percent');
			var topOfWindow = $(window).scrollTop();
			if (pos_y < topOfWindow + 900) {
				$(this).find(".progress-bar").css({
					'width': value
				}, "slow");
			}
		});
	};

    function circleProgress() {
        $('.circle-progress').each(function() {
            var bar_color1 = $(this).data('color1');
            var bar_color2 = $(this).data('color2');
            var pos_y = $(this).offset().top;
            var topOfWindow = $(window).scrollTop();
            if (pos_y < topOfWindow + 900) {
                $(this).find('.inner-bar').easyPieChart({
                    barColor: function(percent) {
                            var ctx = this.renderer.getCtx();
                            var gradient = ctx.createLinearGradient(45,0,0,90);
                                gradient.addColorStop(0, bar_color1);
                                gradient.addColorStop(1, bar_color2);
                            return gradient;
                        },
                    trackColor: false,
                    scaleColor: false,
                    lineCap: 'round',
                    lineWidth: 20,
                    size: 195,
                    animate: 1000,
                    onStart: $.noop,
                    onStop: $.noop,
                    onStep: function(from, to, percent) {
                        $(this.el).find('.percent').text(Math.round(percent)) + '%';
                    }
                });
            }
        });
    };
	
	var progressBar = function () {
		lineProgress();
		circleProgress();
		$(window).on('scroll', function() {
			lineProgress();
			circleProgress();
		});
	};

	/* --------------------------------------------------
	* tabs
	* --------------------------------------------------*/
	var customTabs = function () {

		$('.ot-tabs').each(function() {
			$(this).find('.tabs-heading li').first().addClass('current');
			$(this).find('.tab-content').first().addClass('current');
		});

		$('.tabs-heading li').on( 'click', function(){
			var tab_id = $(this).attr('data-tab');
			$(this).siblings().removeClass('current');
			$(this).parents('.ot-tabs').find('.tab-content').removeClass('current');
			$(this).addClass('current');
			$("#"+tab_id).addClass('current');
		});
	};

	/* --------------------------------------------------
	* accordions
	* --------------------------------------------------*/
	var customAccordions = function () {
        $('.ot-accordions').each( function () {
            var allPanels = $(this).find('.acc-content');

            $(this).find('.acc-toggle').each(function(){
                if ($(this).data('default') == 'yes') {
                    $(this).next().addClass('active').slideDown(300);
                    $(this).parent().addClass('current');
                }
            });

    		$(this).find('.acc-toggle').on( 'click', function(){

                var $this = $(this),
                    $target = $this.next();

                if(!$target.hasClass('active')){
                    allPanels.removeClass('active').slideUp(300);
                    allPanels.parent().removeClass('current');
                    $target.addClass('active').slideDown(300);
                    $target.parent().addClass('current');
                }

                return false;
            });
        });
	};

	/* --------------------------------------------------
	* testimonials
	* --------------------------------------------------*/
    var testimonialSlider = function ( $scope , $ ) {
        $scope.find('.ot-testimonials-slider').each( function () {
            var $selector = $(this),
                $arr      = $selector.data('arrow'),
                $dots     = $selector.data('dots'),
                $nav      = $selector.parents('.onum-row-flex').find('.testicustom-slider-nav'),
                $nav2     = $selector.parents('.onum-row-flex').find('.slider__arrows');
			    if( $nav.length > 0 ){ var customArrows = $nav; } else { var customArrows = $nav2; }
            $selector.not( '.slick-initialized' ).slick({
                rtl: rtl_slick(),
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: $arr,
                dots: $dots,
                autoplay: true,
                autoplaySpeed: 6000,
                adaptiveHeight: true,
                fade: true,                
                appendArrows: customArrows,                
                prevArrow: '<button type="button" class="prev-nav"><i class="flaticon-arrow-pointing-to-left"></i></button>',
                nextArrow: '<button type="button" class="next-nav"><i class="flaticon-arrow-pointing-to-right"></i></button>',
            });
        });
    };

    /* --------------------------------------------------
    * testimonials 2
    * --------------------------------------------------*/
    var testimonialSlider2 = function ( $scope , $ ) {
        $scope.find('.ot-testimonials-carousel-wrap').each( function () {
            var $selector  = $(this),
                $arr       = $selector.data('arrow'),
                $auto      = $selector.data('autoplay'),
                $auspeed   = $selector.data('autoplaySpeed'),
                $fade      = $selector.data('fade'),
                $nav       = $selector.find('.ot-testimonials-nav');
            $('.ot-testimonials-thumbnails').not( '.slick-initialized' ).slick({
                rtl: rtl_slick(),                
                slidesToShow: 3,
                slidesToScroll: 1,
                centerMode: true,
                arrows: false,
                asNavFor: '.ot-testimonials-carousel',
                focusOnSelect: true,
                variableWidth: true,
            });
            $('.ot-testimonials-carousel').not( '.slick-initialized' ).slick({
                rtl: rtl_slick(),
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: $arr,
                autoplay: $auto,
                autoplaySpeed: $auspeed, 
                fade: $fade,
                cssEase: 'linear',
                asNavFor: '.ot-testimonials-thumbnails',
                appendArrows: $nav,
                prevArrow: '<button type="button" class="prev-nav"><i class="flaticon-arrow-pointing-to-left"></i></button>',
                nextArrow: '<button type="button" class="next-nav"><i class="flaticon-arrow-pointing-to-right"></i></button>',
            });            
        });
    };

    /* --------------------------------------------------
    * Service slider
    * --------------------------------------------------*/
    var serviceSlider = function ( $scope , $ ) {
        $scope.find('.ot-service-slider').each( function () {
            var $selector = $(this),
                $show   = $selector.data('show'),
                $scroll = $selector.data('scroll'),
                $arr    = $selector.data('arrow'),
                $dots   = $selector.data('dots'),
                $auto      = $selector.data('autoplay'),
                $auspeed   = $selector.data('autoplaySpeed'),
                $mshow  = $show,
                $marr   = $arr,
                $mdots  = $dots,
                $nav      = $selector.parents('.ot-service_carousel').find('.service-slider-nav-1'),
                $nav2     = $selector.parents('.ot-service_carousel').find('.service-slider-nav-2');
                if( $nav.length > 0 ){ var customArrows = $nav; } else { var customArrows = $nav2; }
                if( $show == 4 ){ $mshow = $show - 1; }
            $selector.not( '.slick-initialized' ).slick({
                rtl: rtl_slick(),
                slidesToShow: $show,
                slidesToScroll: $scroll,
                arrows: $arr,
                dots: $dots,
                autoplay: $auto,
                autoplaySpeed: $auspeed,
                adaptiveHeight: true,
                appendArrows: customArrows, 
                prevArrow: '<button type="button" class="prev-nav"><i class="flaticon-arrow-pointing-to-left"></i></button>',
                nextArrow: '<button type="button" class="next-nav"><i class="flaticon-arrow-pointing-to-right"></i></button>',
                responsive: [
                    {
                        breakpoint: 1199,
                        settings: {
                            slidesToShow: $mshow,
                            slidesToScroll: 1,
                            infinite: true,
                            arrows: $arr,
                            dots: $dots
                        }
                    },
                    {
                        breakpoint: 991,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            infinite: true,
                            arrows: $marr,
                            dots: $mdots
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: false,
                            dots: true
                        }
                    }
                ]
            });
        });
    };

    /* --------------------------------------------------
	* message box
	* --------------------------------------------------*/
	var messageBox = function(){
		$('.message-box > i').on( 'click', function() {
			$(this).parent().fadeOut();
		});
	};

	/* --------------------------------------------------
	* projects filter isotope
	* --------------------------------------------------*/
    var projectsFilter = function ( $scope , $ ) {
        $scope.find('.projects-grid').each( function(){
            var $container = $(this); 
            var $grid = $container.isotope({ 
                    itemSelector : '.project-item', 
                    isOriginLeft: rtl_isotop(),
                    animationEngine : 'css',
                    masonry: {
                        columnWidth: '.project-grid-sizer'
                    },
                });

            /*Refresh the layout of the grid each time an image gets loaded*/
            $grid.imagesLoaded().progress( function() {
                $grid.isotope('layout');
            });  

            var $optionSets = $('.project_filters'),
                $optionLinks = $optionSets.find('a');

            $optionLinks.on('click', function(){
                var $this = $(this);

                if ( $this.hasClass('selected') ) {
                    return false;
                }
                var $optionSet = $this.parents('.project_filters');
                    $optionSets.find('.selected').removeClass('selected');
                    $this.addClass('selected');

                var selector = $(this).attr('data-filter');
                    $container.isotope({ 
                        filter: selector 
                    });
                return false;
            });
        });
    };

    /* --------------------------------------------------
	* project carousel
	* --------------------------------------------------*/
    var portfolioSlider = function ( $scope , $ ) {
        $scope.find('.project-slider').each( function(){
            var $selector = $(this),
                $show   = $selector.data('show'),
                $scroll = $selector.data('scroll'),
                $arr    = $selector.data('arrow'),
                $dots   = $selector.data('dots'),
                $mshow  = $show,
                $marr   = $arr,
                $mdots  = $dots;
                if( $show == 4 ){ $mshow = $show - 1; }
                if( $(this).hasClass('arrow-s2') ){ $marr = false; $mdots = true; }
            $selector.not( '.slick-initialized' ).slick({
                rtl: rtl_slick(),
                slidesToShow: $show,
                slidesToScroll: $scroll,
                arrows: $arr,
                dots: $dots,
                autoplay: true,
                autoplaySpeed: 6000,
                adaptiveHeight: true,
                prevArrow: '<button type="button" class="prev-nav"><i class="flaticon-arrow-pointing-to-left"></i></button>',
                nextArrow: '<button type="button" class="next-nav"><i class="flaticon-arrow-pointing-to-right"></i></button>',
                responsive: [
                    {
                        breakpoint: 1199,
                        settings: {
                            slidesToShow: $mshow,
                            slidesToScroll: 1,
                            infinite: true,
                            arrows: $arr,
                            dots: $dots
                        }
                    },
                    {
                        breakpoint: 991,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            infinite: true,
                            arrows: $marr,
                            dots: $mdots
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: false,
                            dots: true
                        }
                    }
                ]
            });
        });
    };

    /* --------------------------------------------------
    * blog carousel
    * --------------------------------------------------*/
    var blogSlider = function ( $scope , $ ) {
        $scope.find('.onum-blog-slider').each( function(){
            var $selector = $(this),
                $show   = $selector.data('show'),
                $scroll = $selector.data('scroll'),
                $arr    = $selector.data('arrow'),
                $dots   = $selector.data('dots'),
                $mshow  = $show,
                $marr   = $arr,
                $mdots  = $dots;
                if( $show == 4 ){ $mshow = $show - 1; }
                if( $(this).hasClass('arrow-s2') ){ $marr = false; $mdots = true; }
            $selector.not( '.slick-initialized' ).slick({
                rtl: rtl_slick(),
                slidesToShow: $show,
                slidesToScroll: $scroll,
                arrows: $arr,
                dots: $dots,
                autoplay: true,
                autoplaySpeed: 6000,
                adaptiveHeight: true,
                prevArrow: '<button type="button" class="prev-nav"><i class="flaticon-arrow-pointing-to-left"></i></button>',
                nextArrow: '<button type="button" class="next-nav"><i class="flaticon-arrow-pointing-to-right"></i></button>',
                responsive: [
                    {
                        breakpoint: 1199,
                        settings: {
                            slidesToShow: $mshow,
                            slidesToScroll: 1,
                            infinite: true,
                            arrows: $arr,
                            dots: $dots
                        }
                    },
                    {
                        breakpoint: 991,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            infinite: true,
                            arrows: $marr,
                            dots: $mdots
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: false,
                            dots: true
                        }
                    }
                ]
            });
        } );
    };

    /* --------------------------------------------------
    * client carousel
    * --------------------------------------------------*/
    var clientSlider = function ( $scope , $ ) {
        $scope.find('.onum-client-slider').each( function(){
            var $selector = $(this),
                $show     = $selector.data('show') ? $selector.data('show') : 3,
                $tshow    = $selector.data('show-tablet') ? $selector.data('show-tablet') : 2,
                $mshow    = $selector.data('show-mobile') ? $selector.data('show-mobile') : 1,
                $scro     = $selector.data('scroll') ? $selector.data('scroll') : 1,
                $tscro    = $selector.data('scroll-tablet') ? $selector.data('scroll-tablet') : 1,
                $mscro    = $selector.data('scroll-mobile') ? $selector.data('scroll-mobile') : 1,               
                $arr      = $selector.data('arrow'),
                $dots     = $selector.data('dots'),                
                $marr     = $arr,
                $mdots    = $dots;                
            $selector.not( '.slick-initialized' ).slick({
                rtl: rtl_slick(),
                slidesToShow: $show,
                slidesToScroll: $scro,
                arrows: $arr,
                dots: $dots,
                autoplay: true,
                autoplaySpeed: 6000,
                adaptiveHeight: true,
                prevArrow: '<button type="button" class="prev-nav"><i class="flaticon-arrow-pointing-to-left"></i></button>',
                nextArrow: '<button type="button" class="next-nav"><i class="flaticon-arrow-pointing-to-right"></i></button>',
                responsive: [
                    {
                        breakpoint: 1199,
                        settings: {
                            slidesToShow: $show,
                            slidesToScroll: $scro,
                            infinite: true,
                            arrows: $arr,
                            dots: $dots
                        }
                    },
                    {
                        breakpoint: 991,
                        settings: {
                            slidesToShow: $tshow,
                            slidesToScroll: $tscro,
                            infinite: true,
                            arrows: $marr,
                            dots: $mdots
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: $mshow,
                            slidesToScroll: $mscro,
                            arrows: false,
                            dots: true
                        }
                    }
                ]
            });
        } );
    };

    /* --------------------------------------------------
    * social team
    * --------------------------------------------------*/
    var teamSocial = function ( $scope , $ ) {
        $scope.find('.team-social > span').on('click', function(){
            $(this).parent().toggleClass('active');
        });
    };

    /* --------------------------------------------------
	* team carousel
	* --------------------------------------------------*/
    var teamSlider = function ( $scope , $ ) {
        $scope.find('.team-social > span').on('click', function(){
            $(this).parent().toggleClass('active');
        });
        $scope.find('.team-slider').each( function(){
            var $selector = $(this),
                $show   = $selector.data('show'),
                $scroll = $selector.data('scroll'),
                $arr    = $selector.data('arrow'),
                $dots   = $selector.data('dots'),
                $tshow  = $show,
                $mshow  = $show,
                $marr   = $arr,
                $mdots  = $dots;
                if( $show == 5 ){ $tshow = 4; }
                if( $show >= 4 ){ $mshow = 3; }
                if( $(this).hasClass('arrow-s2') ){ $marr = false; $mdots = true; }
            $selector.not( '.slick-initialized' ).slick({
                rtl: rtl_slick(),
                slidesToShow: $show,
                slidesToScroll: $scroll,
                arrows: $arr,
                dots: $dots,
                infinite: false,
                autoplay: true,
                autoplaySpeed: 6000,
                adaptiveHeight: true,
                prevArrow: '<button type="button" class="prev-nav"><i class="flaticon-arrow-pointing-to-left"></i></button>',
                nextArrow: '<button type="button" class="next-nav"><i class="flaticon-arrow-pointing-to-right"></i></button>',
                responsive: [
                    {
                        breakpoint: 1499,
                        settings: {
                            slidesToShow: $tshow,
                            slidesToScroll: 1,
                            infinite: true,
                            arrows: $arr,
                            dots: $dots
                        }
                    },
                    {
                        breakpoint: 1199,
                        settings: {
                            slidesToShow: $mshow,
                            slidesToScroll: 1,
                            infinite: true,
                            arrows: $arr,
                            dots: $dots
                        }
                    },
                    {
                        breakpoint: 991,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            infinite: true,
                            arrows: $marr,
                            dots: $mdots
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: false,
                            dots: true
                        }
                    }
                ]
            });
        } );
    };

    /* --------------------------------------------------
	* real numbers
	* --------------------------------------------------*/
	var realNumbers = function ( $scope , $ ) {
        $scope.find('.real-numbers').each( function(){
    	    var swt = $(this).find('.ot__switch input');
    	    swt.on( 'change', function() {
    	        var parent = $(this).parents('.real-numbers');
    	        if (this.checked) {
    	            parent.find('.a-switch').addClass('active');
    	            parent.find('.b-switch').removeClass('active');
    	            parent.find('h2.after').fadeIn();
    	            parent.find('h2.before').hide();
    	        } else {
    	            parent.find('.b-switch').addClass('active');
    	            parent.find('.a-switch').removeClass('active');
    	            parent.find('h2.after').hide();
    	            parent.find('h2.before').fadeIn();
    	        }
    	    });
        });
	};

    /* --------------------------------------------------
    * switchs widget
    * --------------------------------------------------*/
    var switchsWidget = function ( $scope , $ ) {
        $scope.find('.ot_switchs_wrap').each( function(){
            var swt = $(this).find('.ot__switch input');
            swt.on( 'change', function() {
                var parent = $(this).parents('.ot_switchs_wrap');
                if (this.checked) {
                    parent.find('.a-switch').addClass('active');
                    parent.find('.b-switch').removeClass('active');
                    parent.find('.ot_switchs_after').fadeIn();
                    parent.find('.ot_switchs_before').hide();
                } else {
                    parent.find('.b-switch').addClass('active');
                    parent.find('.a-switch').removeClass('active');
                    parent.find('.ot_switchs_after').hide();
                    parent.find('.ot_switchs_before').fadeIn();
                }
            });
        });
    };

	/* --------------------------------------------------
	* video popup
	* --------------------------------------------------*/
	var videoPopup = function ( $scope , $ ) {
        $scope.find('.video-popup').each( function(){
            var $video_play = $('.video-popup a');
            if ($video_play.length > 0 ) {
                $video_play.magnificPopup({
                    type: 'iframe',
                    removalDelay: 160,
                    preloader: true,
                    fixedContentPos: true,
                    callbacks: {
                    beforeOpen: function() {
                            this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
                            this.st.mainClass = this.st.el.attr('data-effect');
                        }
                    },
                });
            }
        });
    };

    /* --------------------------------------------------
    * post grid isotope
    * --------------------------------------------------*/
    var postGrid = function ( $scope , $ ) {    
        $scope.find('.blog-grid').each( function(){
            var $blog__grid = $(this).isotope({ 
                    itemSelector : '.masonry-post-item', 
                    animationEngine : 'css',
                    masonry: {
                        columnWidth: '.post-grid-sizer'
                    },
                });

            /*Refresh the layout of the grid each time an image gets loaded*/
            $blog__grid.imagesLoaded().progress( function() {
                $blog__grid.isotope('layout');
            });  
        });        
    };

    /* --------------------------------------------------
    * big tabs
    * --------------------------------------------------*/

    var tabSlider = function () {

        $('.tab-titles .title-item').on( 'click', function(){
            $('.tab-active').removeClass('tab-active');
            $(this).addClass('tab-active');
            $('#content-tabs .elementor-inner-section').removeClass('active');
            $('.content-tab-section').removeClass('active');
            $($(this).attr('href')).addClass('active');
            return false;
        });
        $('.tab-titles .title-item:first').trigger('click');

        $('.tab-titles').each( function(){
            var $selector = $(this);

            $selector.on('afterChange', function(event, slick, currentSlide){
                $('.tab-active').removeClass('tab-active');
                $selector.find('.slick-current .title-item').addClass('tab-active');
                $('#content-tabs .elementor-inner-section').removeClass('active');
                $('.content-tab-section').removeClass('active');
                $($selector.find('.slick-current .title-item').attr('href')).addClass('active');
            });

            $selector.not( '.slick-initialized' ).slick({
                rtl: rtl_slick(),
                slidesToShow: 6,
                slidesToScroll: 1,
                arrows: false,
                infinite: false,
                autoplay: false,
                adaptiveHeight: true,
                prevArrow: '<button type="button" class="prev-nav"><i class="flaticon-arrow-pointing-to-left"></i></button>',
                nextArrow: '<button type="button" class="next-nav"><i class="flaticon-arrow-pointing-to-right"></i></button>',
                responsive: [
                    {
                        breakpoint: 1199,
                        settings: {
                            slidesToShow: 5,
                            slidesToScroll: 1,
                            dots: true,
                        }
                    },
                    {
                        breakpoint: 991,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 1,
                            dots: true,
                        }
                    },
                    {
                        breakpoint: 767,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 1,
                            dots: true
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            dots: true
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            dots: true
                        }
                    }
                ]
            });
        } );
    };

    /* --------------------------------------------------
    * count down
    * --------------------------------------------------*/
    var countDown = function(){
        $('.ot-countdown').each( function() {
            var date   = $(this).data('date'),
                zone   = $(this).data('zone'),
                day    = $(this).data('day'),
                days   = $(this).data('days'),
                hour   = $(this).data('hour'),
                hours  = $(this).data('hours'),
                min    = $(this).data('min'),
                mins   = $(this).data('mins'),
                second = $(this).data('second'),
                seconds = $(this).data('seconds');
            $(this).countdown({
                date: date,
                offset: zone,
                day: day,
                days: days,
                hour: hour,
                hours: hours,
                minute: min,
                minutes: mins,
                second: second,
                seconds: seconds
            }, function () {
                alert('Done!');
            });
        });
    };

    /* --------------------------------------------------
    * header search box
    * --------------------------------------------------*/
    var searchBox = function ( $scope , $ ) {      
        $scope.find('.toggle_click_search_2').each( function(){  
            $(this).on("click", function(){
                $(this).toggleClass( "active" );
                $('.h-search-form-field').toggleClass('show');
                if ($(this).find('i').hasClass( "flaticon-search" )) {
                    $('.toggle_search > i').removeClass( "flaticon-search" ).addClass("flaticon-delete");
                }else{
                    $('.toggle_search > i').removeClass( "flaticon-delete" ).addClass("flaticon-search");
                }
                $('.h-search-form-inner > form > input.search-field').focus();
            });
        });
    };

    /* --------------------------------------------------
    * mobile menu
    * --------------------------------------------------*/
    var mmenuPanel  = function(){
        var mmenu_toggle = $('#mmenu-toggle'),
            mmenu   = $('#mmenu-wrapper');

        function mmenu_handler() {
            var isActive = !mmenu_toggle.hasClass('active');

            mmenu_toggle.toggleClass('active', isActive);
            mmenu.toggleClass('mmenu-open', isActive);
            $('body').toggleClass('mmenu-active', isActive);
            return false;
        }

        $('#mmenu-toggle, .mmenu-close, .mmenu-overlay').on('click', mmenu_handler);

        $('.mmenu-wrapper li:has(ul)').prepend('<span class="arrow"><i class="flaticon-arrow-point-to-right"></i></span>');  
        $(".mmenu-wrapper .mobile_mainmenu > li span.arrow").on('click',function() {
            $(this).parent().find("> ul").stop(true, true).slideToggle();
            $(this).toggleClass( "active" ); 
        });
    };

    /**
     * Elementor JS Hooks
     */
    $(window).on("elementor/frontend/init", function () {

        /*Service Carousel*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/iservice_slider.default",
            serviceCarousel
        );

        /*header search box*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/isearch.default",
            searchBox
        );

        /*mobile menu*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/imenu_mobile.default",
            mmenuPanel
        );

    	/*counter*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/icounter.default",
            counter
        );

        /*progress bar + counter*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/iprogress.default",
            progressBar
        );

        /*custom tabs*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/itabs.default",
            customTabs
        );

        /*title tabs slider*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/itabtitle.default",
            tabSlider
        );

        /*custom accordions*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/iaccordions.default",
            customAccordions
        );

        /*testimonials*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/itestimonials.default",
            testimonialSlider
        );

        /*testimonials 2*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/itestimonials2.default",
            testimonialSlider2
        );

        /*service slider*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/iservice_slider.default",
            serviceSlider
        );

        /*message box*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/imessagebox.default",
            messageBox
        );

        /*real numbers*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/ichartnumber.default",
            realNumbers
        );

         /*switchs widget*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/iswitchs.default",
            switchsWidget
        );

        /*video popup*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/ivideopopup.default",
            videoPopup
        );

        /*projects filter isotope*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/ipfilter.default",
            projectsFilter
        );

        /*projects carousel*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/irprojects.default",
            portfolioSlider
        );

        /*projects carousel 2*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/irprojects2.default",
            portfolioSlider
        );

        /*team carousel*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/iteams.default",
            teamSlider
        );

        /*team social*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/imember.default",
            teamSocial
        );

        /*post grid*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/ipost_grid.default",
            postGrid
        );

        /*blog carousel*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/ipost_carousel.default",
            blogSlider
        );

        /*client carousel*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/onum_image_carousel.default",
            clientSlider
        );

        /*countdown*/
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/icountdown.default",
            countDown
        );
    });

} )( jQuery );