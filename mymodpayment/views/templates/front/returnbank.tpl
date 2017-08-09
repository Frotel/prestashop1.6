{if $code eq 0}
	<p class="alert alert-success">
        {$resultmessage}
        {l s='کد رهگیری سفارش شما :' mod='mymodpayment'}{$resultcode}
    </p>
{else}
	<p class="alert alert-danger">
        {$message}
    </p>
    <p>
    	<a href="{$link->getModuleLink('mymodpayment', 'banks')}?status=orderpay" class="button standard-checkout btn btn-default button-medium" >
				<span>
				{l s='پرداخت مجدد' mod='frotel'}
				</span>
		</a>
    </p>
{/if}
