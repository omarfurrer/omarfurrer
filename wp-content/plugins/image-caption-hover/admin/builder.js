jQuery(document).ready(function($) {
	$('.add-table').click(function(event) {
		var counter = $('#noofcolumns').val();
		var captions = $('.wrap-cap').html();

		var rawhtml = '<table class="wp-list-table widefat fixed"><tr>';
			for (var i = 1; i <= counter; i++) {
				rawhtml += '<td>'+captions+'</td>';
			};	
		rawhtml += '</tr></table>';

		$('.append-table').html(rawhtml);
	});

	$('.get-sc').click(function(event) {
		var ids = '';
		$('.append-table table').find('td').each(function(index, el) {
			ids += $(this).find('select').val() + ',';
		});
		prompt('Please copy and use following shortcode', '[wcp-row ids="'+ids+'"]');
	});
});