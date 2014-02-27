$(document).ready(function () {
	$('.haggle').click(function(e){
		e.preventDefault();

		var parent = $(this).parents('.item-entity');
		var price = parent.find('.item-price').val();
		var link = $(this).data('link');

		if(price != '' && price > 0)
		{
			bootbox.confirm("Are you sure you want to buy "+parent.find('.item-name').text()+" for "+price+"?", function(result) {
				if(result == true)
				{
					$(this).on('req.success', function(e, resp){
                        $.growl({message: resp[0].value, icon: 'fa fa-dollar', title: 'Sale'}, {type: 'success'});

						//update the item's stock count
						if(resp[0].data.stock > 0)
						{
							parent.find('.item-stock').text(resp[0].data.stock);
						}
						else
						{
							parent.fadeOut();
						}

					})
					.req({url: link, type: 'POST', CSRF: $('#csrf').text(), data: {price: price}});
				}
			});
		}
	});
});