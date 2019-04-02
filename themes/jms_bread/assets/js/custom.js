/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */
 /*fixed menu*/
$(document).ready(function() {
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        if (scroll > 0 && $(window).width() > 991 && !$("body").hasClass("home3")) {
			$('.fixed-top').addClass('navbar-fixed-top');
            $('#header').addClass('change-style');
        }
        else if (scroll > 990 && $(window).width() > 1599 && $("body").hasClass("home3")) {
            $('#header').addClass('change-style');
        }
        else if (scroll > 850 && $(window).width() == 1599 && $("body").hasClass("home3")) {
            $('#header').addClass('change-style');
        }
        else if (scroll > 700 && $(window).width() == 1366 && $("body").hasClass("home3")) {
            $('#header').addClass('change-style');
        }
        else if (scroll > 0 && $(window).width() > 1599 && !$("body").hasClass("page-index")) {
            $('#header').addClass('change-style');
        }
        else {
            $('.fixed-top').removeClass('navbar-fixed-top');
             $('#header').removeClass('change-style');
        }
    });
});

if($(".jms-vermegamenu").length) {
  jQuery('.jms-vermegamenu').jmsVerMegaMenu({       
		event: jvmmm_event,
		duration: jvmmm_duration
     });  
 }

 jQuery(document).ready(function($) {
		$('.jms-megamenu').jmsMegaMenu({    			
			event: jmmm_event,
			duration: jmmm_duration
		});	
});	
$('body').on('click', '.ajax-add-to-cart', function (event) {	
	event.preventDefault();
	var query = 'id_product=' + $(this).attr('data-id-product') + '&qty='+ $(this).attr('data-minimal-quantity') + '&token=' + $(this).attr('data-token') + '&add=1&action=update';
	var actionURL = prestashop['urls']['base_url'] +  'index.php?controller=cart';		
	$.post(actionURL, query, null, 'json').then(function (resp) {
	    prestashop.emit('updateCart', {
	        reason: {
	          idProduct: resp.id_product,
	          idProductAttribute: resp.id_product_attribute,
	          linkAction: 'add-to-cart'
	        }
	    });
	}).fail(function (resp) {
	    prestashop.emit('handleError', { eventType: 'addProductToCart', resp: resp });
	});
});

function view_as() { 
    var viewGrid = $(".view-grid"),
        viewList = $(".view-list"),
        productList = $(".product_list");
		viewGrid.click(function (e) {       
        productList.removeClass("products-list-in-row");
        productList.addClass("products-list-in-column");
        $(this).addClass('active');
        viewList.removeClass("active");
        e.preventDefault()
    });
    viewList.click(function (e) {       
        productList.removeClass("products-list-in-column");
        productList.addClass("products-list-in-row");
        viewGrid.removeClass("active");
        $(this).addClass('active');        
        e.preventDefault()
    })
}
jQuery(function ($) {
    "use strict";
    $(".view-grid").addClass('active');
    view_as();
});

jQuery(function ($) {
	"use strict";
	if($(".accessories-carousel").length) {
		var accessoriesCarousel = $(".accessories-carousel");
		var rtl = false;
		if ($("body").hasClass("rtl")) rtl = true;

		 accessoriesCarousel.owlCarousel({
			responsiveClass:true,
			responsive:{            
				1366:{
					items:4
				},
				1199:{
					items:3
				},
				768:{
					items:3
				},
				480:{
					items:2
				},
				320:{
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
	if($(".propular-carousel").length) {
		  var propularCarousel = $(".propular-carousel");
		var rtl = false;
		if ($("body").hasClass("rtl")) rtl = true;
		propularCarousel.owlCarousel({
			responsiveClass:true,
			responsive:{            
				1366:{
               	 	items:5
				},
				1199:{
					items:4
				},
				768:{
					items:3
				},
				361:{
					items:2
				},
				0:{
					items:1
				}
			},
			rtl: rtl,
			margin: 30,
			nav: true,
			dots: false,
			autoplay: true,
			slideSpeed: 200,
			loop:false,
		});
	}
});
$(document).ready(function() {
	$('#ver-btn').click(function(event) {
		$('.ver-menu-box').toggleClass('open');
	});
	$('#ver-btn1').click(function(event) {
        $('.ver-menu-mobile').toggleClass('open');
    });
	$('.cat-btn').click(function(event) {
        $('.left-content').toggleClass('open');
    });
});