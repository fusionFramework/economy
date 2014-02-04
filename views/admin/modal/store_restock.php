<form class="form-horizontal" id="form-store-stock">
	<input type="hidden" name="store_restock[store_id]" id="input-store_id" />
	<div class="form-group">
		<label for="input-name" class="col-sm-3 control-label">Item</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="input-item_name" name="store_restock[item_name]">
		</div>
	</div>
	<div class="form-group">
		<label for="input-price_min" class="col-sm-3 control-label">Price</label>
		<div class="col-sm-4">
			<input type="number" class="form-control" id="input-min_price" name="store_restock[min_price]" placeholder="Min">
		</div>
	</div>
	<div class="form-group item-restock">
		<div class="col-sm-offset-3 col-sm-4">
			<input type="number" class="form-control" id="input-max_price" name="store_restock[max_price]" placeholder="Max">
		</div>
		<div class="col-sm-5">
			<a href="#" tabIndex="-1" class="pull-right tt" data-placement="left" title="Define the minimum and maximum price that the item will have when it restocks"><i class="fa fa-question-circle"></i></a>
		</div>
	</div>
	<div class="form-group">
		<label for="input-amount_min" class="col-sm-3 control-label">Amount</label>
		<div class="col-sm-4">
			<input type="number" class="form-control" id="input-min_amount" name="store_restock[min_amount]" placeholder="Min">
		</div>
	</div>
	<div class="form-group item-restock">
		<div class="col-sm-offset-3 col-sm-4 item-restock">
			<input type="number" class="form-control" id="input-max_amount" name="store_restock[max_amount]" placeholder="Max">
		</div>
		<div class="col-sm-5">
			<a href="#" tabIndex="-1" class="pull-right tt" data-placement="left" title="Define the minimum and maximum amount of copies the item will restock"><i class="fa fa-question-circle"></i></a>
		</div>
	</div>
	<div class="form-group item-restock">
		<label for="input-cap_amount" class="col-sm-3 control-label">Restock cap</label>
		<div class="col-sm-4">
			<input type="number" class="form-control" id="input-cap_amount" name="store_restock[cap_amount]">
		</div>
		<div class="col-sm-5">
			<a href="#" tabIndex="-1" class="pull-right tt" data-placement="left" title="The maximum amount of copies this shop is allowed to have in stock"><i class="fa fa-question-circle"></i></a>
		</div>
	</div>
	<div class="form-group item-restock">
		<label for="input-frequency" class="col-sm-3 control-label">Restock frequency</label>
		<div class="col-sm-4">
			<input type="number" class="form-control" id="input-frequency" name="store_restock[frequency]">
		</div>
		<div class="col-sm-5">
			<a href="#" tabIndex="-1" class="pull-right tt" data-placement="left" title="How fast can the item be restocked (in seconds)"><i class="fa fa-question-circle"></i></a>
		</div>
	</div>
</form>
<script type="application/javascript">
	var typeAhead_tpl = <?=json_encode($typeAhead, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);?>;
	var store_id = '<?=$store_id;?>';
</script>