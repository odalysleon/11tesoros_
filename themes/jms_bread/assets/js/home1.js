$(window).load(function(){
		if($('.slider').length > 0)
		$('.slider').fractionSlider({	
			'slideTransition' : jmsslider_trans,
			'slideEndAnimation' : jmsslider_end_animate,
			'transitionIn' : jmsslider_trans_in,
			'transitionOut' : jmsslider_trans_out,
			'fullWidth' : jmsslider_full_width,
			'delay' : jmsslider_delay,
			'timeout' : jmsslider_duration,
			'speedIn' : jmsslider_speed_in,
			'speedOut' : jmsslider_speed_out,
			'easeIn' : jmsslider_ease_in,
			'easeOut' : jmsslider_ease_out,
			'controls' : jmsslider_navigation,
			'pager' : jmsslider_pagination,
			'autoChange' : jmsslider_autoplay,
			'pauseOnHover' : jmsslider_pausehover,
			'backgroundAnimation' : jmsslider_bg_animate,
			'backgroundEase' : jmsslider_bg_ease,
			'responsive' : jmsslider_responsive,
			'dimensions' : jmsslider_dimensions,
			'fullscreen' : true
		});
});
jQuery(function ($) {
    "use strict";
    if($(".categorytab-carousel").length) {		
		var categoryCarousel = $(".categorytab-carousel");			
		var rtl = false;
		if ($("body").hasClass("rtl")) rtl = true;				
		categoryCarousel.owlCarousel({
			responsiveClass:true,
			responsive:{			
				1367:{
					items:cattab_itemsDesktop
				},
				992:{
					items:cattab_itemsDesktopSmall
				},
				768:{
					items:cattab_itemsTablet
				},
				481:{
					items:cattab_itemsMobile
				},
				318:{
					items:1
				},
			},
			rtl: rtl,
			margin:30,
			nav: false,
			dots: true,
			autoplay: false,
			slideSpeed: 800,
			loop:true
		});
	}
	if($(".blog-carousel").length) {		
		var blogCarousel = $(".blog-carousel");			
		var rtl = false;
		if ($("body").hasClass("rtl")) rtl = true;				
		blogCarousel.owlCarousel({
			responsiveClass:true,
			responsive:{			
				1367:{
					items:blog_itemsDesktop
				},
				991:{
					items:blog_itemsDesktopSmall
				},
				768:{
					items:blog_itemsTablet
				},
				318:{
					items:blog_itemsMobile
				},
			},
			rtl: rtl,
			margin:30,
			nav: false,
			dots: false,
			autoplay: false,
			slideSpeed: 800,
			loop:true
		});
	}
	if($(".testimonial-carousel").length) {		
		var testimonialCarousel = $(".testimonial-carousel");			
		var rtl = false;
		if ($("body").hasClass("rtl")) rtl = true;				
		testimonialCarousel.owlCarousel({
			responsiveClass:true,
			responsive:{			
				1199:{
					items:tes_itemsDesktop
				},
				991:{
					items:tes_itemsDesktopSmall
				},
				768:{
					items:tes_itemsTablet
				},
				318:{
					items:tes_itemsMobile
				},
			},
			rtl: rtl,
			nav: false,
			dots: true,
			autoplay: false,
			slideSpeed: 800,
			loop:true
		});
	}
});