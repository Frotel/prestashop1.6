<div class="box">
	<p class="cheque-indent">
		<strong class="dark">{l s='سفارش شما در فروتل ثبت شد.' sprintf=$shop_name mod='mymodpayment'}</strong>
	</p><br>

	<p>
        {l s='هزینه کل سفارش شما مبلغ %s است :' sprintf=$total_to_pay mod='mymodpayment'} <br>
		{l s='این مبلغ بعد از دریافت کالا در محل شما دریافت خواهد شد.' mod='mymodpayment'} 
	</p><br>

    <p>
		{l s='شماره فاکتور شما %s می باشد.' sprintf=$factor mod='mymodpayment'}    
	</p>

</div>