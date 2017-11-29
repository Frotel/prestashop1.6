
<script type="text/javascript">
	function getformbank(id)
	{
        $(".loadingfrotel" + id).css("display","inline");
		$.ajax({
            url:"{$ajaxurl}",
            type:"POST",
data:{ldelim}
        'api':"{$api}",'factor':"{$factor}",'bank':id,
        'webservice': "{$webservice}",
		'callback':"{$callback}"{rdelim},
            success:function(data)
            {
                //$(".loadingfrotel" + id).css("display","none");
                //$("#payment" + id).html("برای پرداخت روی دکمه شروع تراکنش کلیک کنید.");
                if(data == 1)
                {
                    $(".loadingfrotel" + id).css("display","none");
                    $("#payment" + id).css("display","inline");
                    $("#payment" + id).html("ارتباط با سرور قطع می باشد.");
                }else{
                    $("#payment" + id).html(data);
                    $("#payment" + id).children('form').submit();
                }
            }
        });
	}
</script>
{foreach from=$banksfrotel key=i item=topic name=foo}
  		<p class="payment_module">
			  <a onclick="getformbank({$topic.id})" style="cursor:pointer">
		      <img src="{$topic.logo}" >
		      	    {l s='پرداخت از طریق ' mod='frotelpayment'}{$topic.name}
		      </a>
    	</p>
        <p style="border:1px solid #d6d4d4;border-radius:4px;display:none;" id="payment{$topic.id}">

        </p>
        <img src="{$imgdir}" style="display:none;" class="loadingfrotel{$topic.id}">
{/foreach}

