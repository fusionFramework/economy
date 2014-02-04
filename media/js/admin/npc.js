$(document).ready(function () {
	var message = function(def, type, value) {
		if(typeof value == "undefined")
		{
			var value = "";
		}

		var help = messages[type][def].help;
		var params = '<ul>';

		$.each(messages[type][def].params, function(i, d){
			params += '<li>'+d.name+' - '+ d.value+'<br /></li>';
		});
		params += '</ul>';
		return '<tr><td>'+def+'</td><td><a href="#" class="tt" title="'+help+'"><i class="fa fa-question-circle"></i></a> <a href="#" class="po" data-trigger="hover" data-content="'+params+'" data-html="true" title="Parameters"><i class="fa fa-bolt"></i></a></td><td><input type="text" class="form-control" name="'+def+'" value="'+value+'" /></td><td><a href="#" class="msg-remove"><i class="fa fa-times"></i></a></td></tr>';
	}

	$('#modal-npc').on('clean', function(){
		$('#npc-table-messages > tbody').html('');
		$('#npc-tabs a:first').tab('show');
	});

	//Add command to list
	$('.msg-list').click(function (e) {
		e.preventDefault();
		var def = $(this).text();
		var type = $('#input-type').val();

		if (typeof messages[type][def] == 'undefined') {
			$('#item-notify').notify({
				message:{ text:'You can\'t add message "' + def + '", since it does not exist.'},
				type:'info',
				fadeOut:{ enabled:true, delay:6000 }
			}).show();
		}
		else {
			//add the action to the row
			$('#npc-table-messages > tbody').append(message(def, type));
			$('#npc-table-messages').find('.tt').tooltip();
			$('#npc-table-messages').find('.po').popover();
		}
	});

	// message row remove
	$('#npc-table-messages').on('click', '.msg-remove', function (e) {
		e.preventDefault();
		$(this).parents('tr').remove();
	});

	//load the command required by the item type
	$('#input-type').change(function (e) {
		//remove the first tr
		$('#npc-table-messages > tbody').html('');

		$('.msg-list[data-type="'+$('#input-type_id').val()+'"]').removeClass('hide');
		$('.msg-list[data-type!="'+$('#input-type_id').val()+'"]').addClass('hide');
	});

	//load in any predefined messages
	$('#modal-npc').on('load', function (e, data) {
		var msgs = '';
		$.each(data.messages, function (type, v) {
			$.each(v, function(i,msg){
				msgs += message(type, $('#input-type').val(), msg);
			});
			$('#npc-table-messages > tbody').html(msgs);
			$('#npc-table-messages').find('.tt').tooltip();
			$('#npc-table-messages').find('.po').popover();
		});
	});

	//namespace the message input elements properly before sending for save
	$('#modal-npc').on('save', function (e) {
		var counters = [];
		modalSubmitData.npc = {};
		modalSubmitData.npc.messages = {};
		$('#npc-table-messages').find(':input').each(function () {
			if (typeof counters[this.name] == 'undefined')
			{
				modalSubmitData.npc.messages[this.name] = [];
				counters[this.name] = 0;
			}
			else
				counters[this.name]++;

			modalSubmitData.npc.messages[this.name][counters[this.name]] = $(this).val();
		});
	});
});