<form class="form-horizontal">
	<ul class="nav nav-tabs" id="store-tabs">
		<li class="active"><a href="#store-info" data-toggle="tab">Info</a></li>
		<li id="tab-inventory">
			<a href="#store-inventory" data-toggle="tab">Inventory</a>
		</li>
	</ul>
	<br />
	<div class="tab-content">
		<div class="tab-pane active" id="store-info">
			<?php
				echo $model->get_form(['title', 'npc_id', 'status', 'stock_type', 'stock_cap'])->render('bootstrap/form_fields');
			?>
		</div>
		<div class="tab-pane" id="store-inventory">
			<div class="row">
				<a href="#" id="item-add" class="pull-right btn btn-success btn-xs"><i class="fa fa-plus-circle"></i> </a>
			</div>
			<table id="store-table-inventory" class="table">
				<thead>
					<th class="col-sm-3">Item</th>
					<th class="col-sm-3">Price</th>
					<th class="col-sm-2">Amount</th>
					<th class="col-sm-2">Frequency</th>
					<th></th>
				</thead>
				<tbody>

				</tbody>
			</table>
		</div>
	</div>
</form>

<div class="modal fade" id="modal-store-item">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&nbsp;&nbsp; &times;</button>
		<div class="btn-group btn-group-sm pull-right hide" style="margin-right: 5px" id="item-error-button">
			<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" title="Errors">
				<i class="fa fa-exclamation"></i>
				<span class="sr-only">Toggle Dropdown</span></button>
			<ul class="dropdown-menu" role="menu" id="item-errors">
			</ul>
		</div>

		<h4 class="modal-title">Add item to store</h4>
	</div>
	<div class="modal-body">
		<form class="form-horizontal" id="form-store-stock">
			<input type="hidden" name="store_id" id="item-store_id" />
			<input type="hidden" name="id" id="item-id" />
			<input type="hidden" name="csrf" id="item-csrf" />
			<div class="form-group">
				<label for="item-name" class="col-sm-3 control-label">Item</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="item-name" name="item_name">
				</div>
			</div>
			<div class="form-group">
				<label for="item-price_min" class="col-sm-3 control-label">Price</label>
				<div class="col-sm-4">
					<input type="number" class="form-control" id="item-price_min" name="min_price" placeholder="Min">
				</div>
			</div>
			<div class="form-group item-restock">
				<div class="col-sm-offset-3 col-sm-4">
					<input type="number" class="form-control" id="item-price_max" name="max_price" placeholder="Max">
				</div>
				<div class="col-sm-5">
					<a href="#" tabIndex="-1" class="pull-right tt" data-placement="left" title="Define the minimum and maximum price that the item will have when it restocks"><i class="fa fa-question-circle"></i></a>
				</div>
			</div>
			<div class="form-group">
				<label for="item-amount_min" class="col-sm-3 control-label">Amount</label>
				<div class="col-sm-4">
					<input type="number" class="form-control" id="item-amount_min" name="min_amount" placeholder="Min">
				</div>
			</div>
			<div class="form-group item-restock">
				<div class="col-sm-offset-3 col-sm-4 item-restock">
					<input type="number" class="form-control" id="item-amount_max" name="max_amount" placeholder="Max">
				</div>
				<div class="col-sm-5">
					<a href="#" tabIndex="-1" class="pull-right tt" data-placement="left" title="Define the minimum and maximum amount of copies the item will restock"><i class="fa fa-question-circle"></i></a>
				</div>
			</div>
			<div class="form-group item-restock">
				<label for="item-cap_amount" class="col-sm-3 control-label">Restock cap</label>
				<div class="col-sm-4">
					<input type="number" class="form-control" id="item-cap_amount" name="cap_amount">
				</div>
				<div class="col-sm-5">
					<a href="#" tabIndex="-1" class="pull-right tt" data-placement="left" title="The maximum amount of copies this shop is allowed to have in stock"><i class="fa fa-question-circle"></i></a>
				</div>
			</div>
			<div class="form-group item-restock">
				<label for="item-frequency" class="col-sm-3 control-label">Restock frequency</label>
				<div class="col-sm-4">
					<input type="number" class="form-control" id="item-frequency" name="frequency">
				</div>
				<div class="col-sm-5">
					<a href="#" tabIndex="-1" class="pull-right tt" data-placement="left" title="How fast can the item be restocked (in seconds)"><i class="fa fa-question-circle"></i></a>
				</div>
			</div>
		</form>

	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-success" id="restock-save">Save</button>
	</div>
</div><!-- /.modal -->

<div class="hide" id="csrf-token"><?=Security::token();?></div>
<script type="application/javascript">
	var store_routes = <?=json_encode($routes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);?>;
	var typeAhead_tpl = <?=json_encode($typeAhead, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);?>;
</script>