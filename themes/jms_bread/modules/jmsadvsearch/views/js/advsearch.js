/**
* 2007-2015 PrestaShop
*
* Jms Adv Search
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2015 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/
$(document).ready(function() {
	$('#selector_cat').click(function(event){
		var id_cat_search = $('#selector_cat').find(":selected").val();
		$('input[name="cat_id"]').val(id_cat_search);
	});
	
	$( "#ajax_search" ).keyup(function() {
		var id_cat_search = $('#selector_cat').find(":selected").val();		
		var search_key = $( "#ajax_search" ).val();
		$.ajax({
			type: 'GET',
			url: baseDir + 'modules/jmsadvsearch/adv_search.php',
			headers: { "cache-control": "no-cache" },
			async: true,
			data: 'search_key=' + search_key + '&cat_id=' + id_cat_search,
			success: function(data)
			{		
				$('#search_result').innerHTML = data;		
			}
		}) .done(function( msg ) {
		$( "#search_result" ).html(msg);
		});
	})	
	$('html').click(function() {
		$( "#search_result" ).html('');
	});

	$('#search_result').click(function(event){
		event.stopPropagation();
	});

});