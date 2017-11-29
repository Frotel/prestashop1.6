{if $haserror eq 1}
	<p class="alert alert-danger">
		{$error}
	</p>
{/if}

{capture name=path}
    {l s='پرداخت فروتل' mod='frotelpayment'}
{/capture}

<h1 class="page-heading">
{l s='خلاصه سفارش و پرداخت' mod='frotelpayment'}
</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}


    <form action="{$link->getModuleLink('frotelpayment', 'validation', [], true)|escape:'html'}" method="post">
	<div class="box cheque-box">
		<h3 class="page-subheading">
            {l s='پرداخت فروتل' mod='frotelpayment'}
		</h3>
		<p class="cheque-indent">
			<strong class="dark">
                {l s='شما پرداخت با فروتل را انتخاب کرده اید' mod='frotelpayment'} 
			</strong>
		</p>
		<p>
			- {l s='مقدرا کل سفارش شما هست :' mod='frotelpayment'}
			<span id="amount" class="price">{displayPrice price=$total_amount}</span>
		</p>
		<p>
			- {l s='در صورت انتخاب پرداخت آنلاین شما در صفحه بعد لیست پرداخت را خواهید دید.' mod='frotelpayment'}
			<br />
			- {l s='لطفا روی ثبت سفارش کلیک کنید.' mod='frotelpayment'}.
		</p>
	</div><!-- .cheque-box -->

	<p class="cart_navigation clearfix" id="cart_navigation">
		<a
				class="button-exclusive btn btn-default"
				href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
			<i class="icon-chevron-left"></i>{l s='انتخاب روش های پرداخت دیگر' mod='frotelpayment'}
		</a>
		<button
				class="button btn btn-default button-medium"
				type="submit">
			<span>{l s='ثبت سفارش' mod='frotelpayment'}<i class="icon-chevron-right right"></i></span>
		</button>
	</p>
    </form>








