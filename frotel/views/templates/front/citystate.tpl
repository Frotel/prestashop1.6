<script type="text/javascript" src="http://pc.fpanel.ir/city.js"></script>
<script type="text/javascript" src="http://pc.fpanel.ir/ostan.js"></script>
<script type="text/javascript">
$(document).ready(function(){
		loadOstan('FrotelOstan');
});

function ListCity(ostan){
		ldMenu(ostan, 'FrotelCity');
}

</script>
{include file="$tpldir/froteljs.tpl"}
<div class="row">
	<div class="col-md-12">
			<div class="col-md-3">
				<select class="form-control" onchange="ListCity(this.value)" id="FrotelOstan">
					<option></option>
				</select>
			</div>
			<div class="col-md-3">
				<select class="form-control" id="FrotelCity" 
				onchange="setcityfrotel(this.value)" ></select>
			</div>
			{* class="col-md-3">
			<a class="button standard-checkout btn btn-default button-medium" id="getsend_price">
				<span>
				{l s='ذخیره' mod='frotel'}
				</span>
			</a>*}
			</div>
			<img class="frotelloading" src="{$imgdir}" style="display:none">
	</div>
</div>
<div class="row message_frotel" style="display:none;">
	
</div>