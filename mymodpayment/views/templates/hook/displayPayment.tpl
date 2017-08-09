
{if $carriermod eq 1 }
<div class="row">
	<div class="col-xs-12 col-md-6">
        <p class="payment_module">
			<a href="{$link->getModuleLink('mymodpayment', 'payment')|escape:'html'}" class="mymodpayment">
                {l s='سفارش شما به صورت پیشتاز و پرداخت در محل می باشد.' mod='mymodpayment'}
            </a>
        </p>
    </div>
</div>
{/if}

{if $carriermod eq 2 }
<div class="row">
	<div class="col-xs-12 col-md-6">
        <p class="payment_module">
			<a href="{$link->getModuleLink('mymodpayment', 'payment')|escape:'html'}" class="mymodpayment">
                {l s='سفارش شما به صورت سفارشی و پرداخت در محل می باشد.' mod='mymodpayment'}
            </a>
        </p>
    </div>
</div>
{/if}

{if $carriermod eq 3 }
<div class="row">
	<div class="col-xs-12 col-md-6">
        <p class="payment_module">
			<a href="{$link->getModuleLink('mymodpayment', 'payment')|escape:'html'}" class="mymodpayment">
                {l s='سفارش شما به صورت سفارشی و پرداخت آنلاین می باشد.' mod='mymodpayment'}
            </a>
        </p>
    </div>
</div>
{/if}

{if $carriermod eq 4 }
<div class="row">
	<div class="col-xs-12 col-md-6">
        <p class="payment_module">
			<a href="{$link->getModuleLink('mymodpayment', 'payment')|escape:'html'}" class="mymodpayment">
                {l s='سفارش شما به صورت پیشتاز و پرداخت آنلاین می باشد.' mod='mymodpayment'}
            </a>
        </p>
    </div>
</div>
{/if}
