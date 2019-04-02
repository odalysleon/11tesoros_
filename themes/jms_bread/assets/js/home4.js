jQuery(document).ready(function($) {
		$('.jms-megamenu').jmsMegaMenu({    			
			event: jmmm_event,
			duration: jmmm_duration
		});	
});	
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
		if($(".flashsales-carousel").length) {		
			var flashsalesCarousel = $(".flashsales-carousel");		
			var rtl = false;
				if ($("body").hasClass("rtl")) rtl = true;				
				flashsalesCarousel.owlCarousel({
					responsiveClass:true,
					responsive:{			
						1199:{
							items:1
						},
						991:{
							items:1
						},
						768:{
							items:1
						},
						318:{
							items:1
						}
					},
					rtl: rtl,
					margin: 0,
				    nav: true,
				    dots: false,
					autoplay:false,
				    navigationText: ["", ""],
				    slideSpeed: 200	
				});
		}
		if($(".producttab-carousel1").length) {
			var producttabCarousel1 = $(".producttab-carousel1");			
			var rtl = false;
			if ($("body").hasClass("rtl")) rtl = true;				
			producttabCarousel1.owlCarousel({
				responsiveClass:true,
				responsive:{			
					1199:{
						items:tab1_itemsDesktop
					},
					991:{
						items:tab1_itemsDesktopSmall
					},
					768:{
						items:tab1_itemsTablet
					},
					481:{
						items:tab1_itemsMobile
					},
					361:{
						items:2
					},
					0: {
						items:1
					}
				},
				rtl: rtl,
					margin: 0,
				    nav: false,
				    dots: false,
					autoplay:false,
				    navigationText: ["", ""],
				    slideSpeed: 200
			});
		}
		if($(".producttab-carousel2").length) {
			var producttabCarousel2 = $(".producttab-carousel2");			
			var rtl = false;
			if ($("body").hasClass("rtl")) rtl = true;				
			producttabCarousel2.owlCarousel({
				responsiveClass:true,
				responsive:{			
					1199:{
						items:tab2_itemsDesktop
					},
					991:{
						items:tab2_itemsDesktopSmall
					},
					768:{
						items:tab2_itemsTablet
					},
					481:{
						items:tab2_itemsMobile
					},
					361:{
						items:2
					},
					0: {
						items:1
					}
				},
				rtl: rtl,
					margin: 0,
				    nav: false,
				    dots: false,
					autoplay:false,
				    navigationText: ["", ""],
				    slideSpeed: 200
			});
		}
		if($(".producttab-carousel3").length) {
			var producttabCarousel3 = $(".producttab-carousel3");			
			var rtl = false;
			if ($("body").hasClass("rtl")) rtl = true;				
			producttabCarousel3.owlCarousel({
				responsiveClass:true,
				responsive:{			
					1199:{
						items:tab3_itemsDesktop
					},
					991:{
						items:tab3_itemsDesktopSmall
					},
					768:{
						items:tab3_itemsTablet
					},
					481:{
						items:tab3_itemsMobile
					},
					361:{
						items:2
					},
					0: {
						items:1
					}
				},
				rtl: rtl,
					margin: 0,
				    nav: false,
				    dots: false,
					autoplay:false,
				    navigationText: ["", ""],
				    slideSpeed: 200
			});
		}
		if($(".producttab-carousel4").length) {
			var producttabCarousel4 = $(".producttab-carousel4");			
			var rtl = false;
			if ($("body").hasClass("rtl")) rtl = true;				
			producttabCarousel4.owlCarousel({
				responsiveClass:true,
				responsive:{			
					1199:{
						items:tab4_itemsDesktop
					},
					991:{
						items:tab4_itemsDesktopSmall
					},
					768:{
						items:tab4_itemsTablet
					},
					481:{
						items:tab4_itemsMobile
					},
					361:{
						items:2
					},
					0: {
						items:1
					}
				},
				rtl: rtl,
					margin: 0,
				    nav: false,
				    dots: false,
					autoplay:false,
				    navigationText: ["", ""],
				    slideSpeed: 200
			});
		}
		if($(".producttab-carousel5").length) {
			var producttabCarousel5 = $(".producttab-carousel5");			
			var rtl = false;
			if ($("body").hasClass("rtl")) rtl = true;				
			producttabCarousel5.owlCarousel({
				responsiveClass:true,
				responsive:{			
					1199:{
						items:tab5_itemsDesktop
					},
					991:{
						items:tab5_itemsDesktopSmall
					},
					768:{
						items:tab5_itemsTablet
					},
					481:{
						items:tab5_itemsMobile
					},
					361:{
						items:2
					},
					0: {
						items:1
					}
				},
				rtl: rtl,
					margin: 0,
				    nav: false,
				    dots: false,
					autoplay:false,
				    navigationText: ["", ""],
				    slideSpeed: 200
			});
		}
		if($(".blog-carousel").length) {
			var blogCarousel = $(".blog-carousel");		
			var rtl = false;
			if ($("body").hasClass("rtl")) rtl = true;				
			blogCarousel.owlCarousel({
				responsiveClass:true,
				responsive:{			
					1199:{
						items:blog_itemsDesktop
					},
					991:{
						items:blog_itemsDesktopSmall
					},
					768:{
						items:blog_itemsTablet
					},
					481:{
						items:blog_itemsMobile
					},
					0: {
						items:1
					}
				},
					rtl: rtl,
					margin:30,
					nav: true,
					dots: false,
					autoplay: false,
					slideSpeed: 800,	
			});
		}
		if($(".brand-carousel").length) {
			var brandCarousel = $(".brand-carousel");
			var rtl = false;
			if ($("body").hasClass("rtl")) rtl = true;				
			brandCarousel.owlCarousel({
				responsiveClass:true,
				responsive:{			
					1199:{
						items:brand_itemsDesktop
					},
					991:{
						items:brand_itemsDesktopSmall
					},
					768:{
						items:brand_itemsTablet
					},
					480:{
						items:brand_itemsMobile
					},
					318:{
						items:2
					}
				},
				rtl: rtl,
				margin: 30,
				nav: false,
				dots: false,
				autoplay: true,
				slideSpeed: 200,
				loop: true
			});
		}
});