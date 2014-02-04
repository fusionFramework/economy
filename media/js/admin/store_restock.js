$(document).ready(function () {
	var template = Hogan.compile(typeAhead_tpl.item, { delimiters: '<% %>' });
	$('#input-item_name').typeahead([
		{
			name: 'item',
			remote: '../../search/item/%QUERY.json',
			template: template.render.bind(template)
		}
	]);

	$('#modal-store_restock').on('clean', function (e, data) {
		$('#input-store_id').val(store_id);
	});

	$('#modal-store_restock').on('load', function (e, data) {
		// steady stock stores don't need certain parameters
		if(data.store.stock_type == "steady")
		{
			$('.item-restock').addClass('hide');
		}
		else
		{
			$('.item-restock').removeClass('hide');
		}
	});
});