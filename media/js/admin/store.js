$(document).ready(function () {
	$('#dataTable-stores').on('click', '.btn-action-stock-item', function(e){
		e.preventDefault();

		var init = window.location.pathname.replace('stores', 'store/restock')+'/'+$(this).data('id');
		window.location.href = init;
	});

	var template = Hogan.compile(typeAhead_tpl.item, { delimiters: '<% %>' });
	$('#item-name').typeahead([
		{
			name: 'item',
			remote: '../admin/search/item/%QUERY.json',
			template: template.render.bind(template)
		}
	]);

	$('#modal-stores').on('clean', function(){
		$('#tab-inventory').addClass('hide');
		$('#store-tabs a:first').tab('show');
		$('#store-table-inventory > tbody').html('');
		$('.item-restock').removeClass('hide');
		store_id = '';
	});

	$('#input-type').change(function (e) {
		// steady stock stores don't need certain parameters
		if($('#input-type_id').val() == "steady")
		{
			$('.item-restock').addClass('hide');
		}
		else
		{
			$('.item-restock').removeClass('hide');
		}
	});

	//load in any predefined item restocks
	$('#modal-stores').on('load', function (e, data) {
		store_id = data.id;

		$('#tab-inventory').removeClass('hide');
		var items = '';
		$.each(data.restocks, function (type, v) {
			items += '<tr><td>'+ v.item_name+'</td><td>'+ v.min_price+' - '+ v.max_price+'</td><td>'+ v.min_amount+' - '+ v.max_amount+' ('+ v.cap_amount+')</td><td>'+ v.frequency+'</td><td><a href="#" class="restock-edit" data-id="'+ v.id+'"><i class="fa fa-pencil"></i></a> <a href="#" class="restock-remove" data-id="'+ v.id+'"><i class="fa fa-times"></i></a></td></tr>';
		});
		$('#store-table-inventory > tbody').html(items);
	});
});