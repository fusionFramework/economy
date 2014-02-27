$(document).ready(function () {
	var default_command = '';

	$('#modal-items').on('clean', function(){
		$('#item-table-cmd > tbody > tr').remove();
		default_command = '';
		$('#item-tabs a:first').tab('show');
	});

	//Add command to list
	$('.item-cmd').click(function (e) {
		e.preventDefault();
		var command = $(this).data('cmd');

		if (default_command != '' && cmd.definitions[default_command].pets == 0 && cmd.definitions[command].pets == 1) {
			//error, can't add a pet related command to an item that doesn't load the pet list
            $.fn.req.defaultRequestHandlers.success([{
                value: 'You can\'t add command "' + command.replace('_', ' ') + '" because it requires pets.',
                type: 'info'
            }]);

		}
		else if ($('#item-table-cmd > tbody').find('tr[data-cmd="' + command + '"]').length > 0 && cmd.definitions[command].multiple == 0) {
            $.fn.req.defaultRequestHandlers.success([{
                value: 'You can\'t add command "' + command.replace('_', ' ') + '" more than once to an item.',
                type: 'info'
            }]);
		}
		else {
			var tpl = $('#item-commands-input > table').find('tr[data-cmd="' + command + '"]').clone(true);

			//bind autocomplete search if needed
			if (cmd.definitions[command].search != 0) {
				var template = Hogan.compile(typeAhead_tpl[cmd.definitions[command].search], { delimiters: '<% %>' });
				tpl.find('.search').typeahead([
					{
						name: cmd.definitions[command].search,
						remote: '../admin/search/'+cmd.definitions[command].search+'/%QUERY.json',
						template: template.render.bind(template)
					}
				]);
			}

			//add the action to the row
			$('#item-table-cmd > tbody').append(tpl);
		}
	});


	//command row close
	$('.cmd-remove').click(function (e) {
		e.preventDefault();
		$(this).parents('tr').remove();
	});

	//load the command required by the item type
	$('#input-type_id').change(function (e) {
		var selected = $('#input-type_id option:selected').text();

		if(selected == "Select")
			return false;

		//remove the first tr
		$('#item-table-cmd > tbody > tr:first').remove();

		var command = cmd.type_map[selected];

		if (cmd.definitions[command].only == 1)
		{
			$('#item-cmd-add').addClass('disabled');
		}
		else
		{
			$('#item-cmd-add').removeClass('disabled');
		}

		var tpl = $('#item-commands-input > table').find('tr[data-cmd="' + command + '"]').clone(true);
		tpl.find('a.cmd-remove').remove();

		//if it's already in the action list, remove existing
		var existing = $('#item-table-cmd').find('tr[data-cmd="' + command + '"]');
		if (existing.length > 0)
		{
			//bind autocomplete search if needed
			if (cmd.definitions[command].search != 0) {
				var template = Hogan.compile(typeAhead_tpl[cmd.definitions[command].search], { delimiters: '<% %>' });
				tpl.find('.search').typeahead([
					{
						name: cmd.definitions[command].search,
						remote: '../admin/search/'+cmd.definitions[command].search+'/%QUERY.json',
						template: template.render.bind(template)
					}
				]);
			}

			//assign the value so we don't lose it
			tpl.find('[name="' + command + '"]').val(existing.val());
			existing.parents('tr').remove();
		}
		default_command = command;
		$('#item-table-cmd > tbody').prepend(tpl);
	});

	//load in any predefined commands
	$('#modal-items').on('load', function (e, data) {
		var first = true;

		$.each(data.commands, function (k, v) {
			var tpl = $('#item-commands-input > table').find('tr[data-cmd="' + k + '"]').clone(true);

			if (first == true) {
				tpl.find('a.cmd-remove').remove();
				default_command = k;
				first = false;
			}
			tpl.find('input').val(v);

			$('#item-table-cmd > tbody').append(tpl);
		});
	});

	//namespace the command input elements properly before sending for save
	$('#modal-items').on('save', function (e) {
		var counters = [];

		modalSubmitData.item = {};
		modalSubmitData.item.commands = {};

		$('#item-table-cmd').find(':input').each(function () {
			if (cmd.definitions[this.name].multiple == 1) {
				if (typeof counters[this.name] == 'undefined')
				{
					counters[this.name] = 0;
					modalSubmitData.item.commands[this.name] = [];
				}
				else
					counters[this.name]++;

				modalSubmitData.item.commands[this.name][counter] = $(this).val();
			}
			else
				modalSubmitData.item.commands[this.name] = $(this).val();
		});
	});
});