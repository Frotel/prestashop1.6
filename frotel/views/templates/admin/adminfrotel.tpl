<script type="text/javascript">
function sendFrotel()
{
	$.ajax({
            url:"{$ajaxurl}",
            type:"POST",
data:{ldelim}
        'orderid':"{$orderid}"{rdelim},
            success:function(data)
            {
				$('.test110').html(data);
            }
        });
}
</script>
<div class="row">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-envelope"></i> ارسال به فروتل <span class="badge">1</span>
		</div>
		<div class="panel panel-highlighted">
		{l s='این سفارش در فروتل ثبت نشده است لطفا روی دکمه ارسال به فروتل کلیک کنید.'}
			<button onclick="sendFrotel()" id="submitMessage" class="btn btn-primary pull-right" name="submitMessage">
									ارسال به فروتل
			</button>
		</div>
		<div class="row test110">
		</div>
	</div>
</div>