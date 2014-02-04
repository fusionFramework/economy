<form class="form-horizontal">
	<ul class="nav nav-tabs" id="item-tabs">
		<li class="active"><a href="#item-info" data-toggle="tab">Info</a></li>
		<li class="dropdown">
				<a class="pull-right" data-toggle="dropdown" href="#">
					<i class="fa fa-plus-circle"></i>
				</a>
				<ul class="dropdown-menu" id="item-cmd-add">
					<?php foreach($menu_commands as $menu): ?>
						<li class="dropdown-submenu">
							<a href="#"><?=$menu['name'];?></a>
							<ul class="dropdown-menu">
								<?php foreach($menu['commands'] as $cmd): ?>
									<li><a href="#" class="item-cmd" data-cmd="<?=$cmd['cmd'];?>"><?=$cmd['name'];?></a></li>
								<?php endforeach; ?>
							</ul>
						</li>
					<?php endforeach; ?>
				</ul>
			<a href="#item-commands" class="pull-left" data-toggle="tab">Commands</a></li>
	</ul>
	<br />
	<div class="tab-content">
		<div class="tab-pane active" id="item-info">
			<?php
				echo $model->get_form(['type_id', 'name', 'description', 'image', 'status', 'unique', 'transferable'])->render('bootstrap/form_fields');
			?>
		</div>
		<div class="tab-pane" id="item-commands">
			<table id="item-table-cmd">
				<tbody>

				</tbody>
			</table>

			<div class="hide">
				<div class="hide" id="item-commands-input">
					<table>
						<?php foreach($input_commands as $name => $cmd): ?>
							<tr data-cmd="<?=$name;?>">
								<td class="col-sm-4"><?=$cmd['title'];?></td>
								<td class="col-sm-6">
									<?php foreach($cmd['fields'] as $field): ?>
										<input type="text" name="<?=$field['name'];?>" class="<?=$field['class'];?> form-control" />
									<?php endforeach; ?>
								</td>
								<td class="col-sm-1">
									<a href="#" class="nolink cmd-remove"><i class="fa fa-times"></i></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="notifications top-right" id="item-notify"></div>
</form>

<script type="application/javascript">
	var cmd = {
		"definitions": <?=json_encode($command_definitions, JSON_PRETTY_PRINT);?>,
		"type_map": <?=json_encode($type_map, JSON_PRETTY_PRINT);?>
	};

	var typeAhead_tpl = {
		<?php $tpls = Admin::typeAhead_tpl($searches); ?>
		<?php foreach($tpls as $name => $tpl): ?>
		"<?=$name;?>": '<?=$tpl;?>',
		<?php endforeach; ?>
	};
</script>