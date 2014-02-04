$(document).ready(function () {
	$('.haggle').click(function(e){
		e.preventDefault();
		console.log('click');
		var parent = $(this).parents('.item-entity');
		var price = parent.find('.item-price').val();
		var link = $(this).data('link');
		console.log(link);
		if(price != '' && price > 0)
		{
			bootbox.confirm("Are you sure you want to buy "+parent.find('.item-name').text()+" for "+price+"?", function(result) {
				if(result == true)
				{
					$(this).on('req.success', function(e, resp){
						$('#notify-container').notify({
							message:{ text: resp[0].value},
							type:'success',
							fadeOut:{ enabled:true, delay:6000 }
						}).show();

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
					.on('req.error', function(e, error){
							$('#notify-container').notify({
								message:{ text: error[0].value},
								type:'warning',
								fadeOut:{ enabled:true, delay:6000 }
							}).show();
					})
					.req({url: link, type: 'POST', CSRF: $('#csrf').text(), data: {price: price}});
				}
			});
		}
	});
});