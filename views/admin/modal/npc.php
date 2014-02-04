<form class="form-horizontal">
	<ul class="nav nav-tabs" id="npc-tabs">
		<li class="active"><a href="#npc-info" data-toggle="tab">Info</a></li>
		<li class="dropdown">
				<a class="pull-right" data-toggle="dropdown" href="#">
					<i class="fa fa-plus-circle"></i>
				</a>
				<ul class="dropdown-menu" id="npc-msg-add">
					<?php foreach($messages as $type => $msgs): ?>
						<?php foreach($msgs as $name => $def): ?>
							<li>
								<a href="#" data-type="<?=$type;?>" class="msg-list"><?=$name;?></a>
							</li>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</ul>
			<a href="#npc-messages" class="pull-left" data-toggle="tab">Messages</a></li>
	</ul>
	<br />
	<div class="tab-content">
		<div class="tab-pane active" id="npc-info">
			<?php
				echo $model->get_form(['name', 'type', 'image'])->render('bootstrap/form_fields');
			?>
		</div>
		<div class="tab-pane" id="npc-messages">
			<table id="npc-table-messages" class="table">
				<thead>
					<th class="col-sm-3">Type</th>
					<th></th>
					<th class="col-sm-7">Content</th>
					<th></th>
				</thead>
				<tbody>

				</tbody>
			</table>
		</div>
	</div>
	<div class="notifications top-right" id="store-notify"></div>
</form>

<script type="application/javascript">
	var messages = <?=json_encode($messages, JSON_PRETTY_PRINT);?>;
</script>