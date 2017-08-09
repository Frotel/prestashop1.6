<script type="text/javascript">
frotel_carrier_rul ={$frotel_carrier_rul}
{literal}
$(document).ready(function(){
    if(frotel_carrier_rul !=1)
     {
        if(frotel_carrier_rul ==2)
        {
            {/literal}
            {foreach $frotel_online as $id}
                $(".delivery_option_radio[value='{$id},']").parent('.delivery_option').remove();
            {/foreach}
            {literal}
        }
     
    $('.delivery_options input').attr({checked: false});
    $('.delivery_option_price').text('');
    $.ajax({
        url:"{/literal}{$ajaxurl}{literal}",
        type:"POST",
        success:function(data){
            
            try
            {
                value=$.parseJSON(data);
            }
            catch(e)
            {
                value=false;
            }
            if(value)
            {
                $.each(value,function(key,val)
                {
    $(".delivery_option_radio[value='"+key+",']").closest('.delivery_option').find('.delivery_option_price').text(val);
                });
            }
            else
            {
               alert('مشکلی در محاسبه هزینه حمل پیش آمده است با مدیریت تماس بگیرید')
            }
        }
    });
 }
     /** remove frotel carriers */
else if(frotel_carrier_rul ==1)
 {
 
     /*$(".delivery_option_radio[value='{/literal}{$frotel_pcid}{literal},']").parent('.delivery_option')
                   .remove();
    $(".delivery_option_radio[value='{/literal}{$frotel_scid}{literal},']").parent('.delivery_option')
                    .remove();
    $(".delivery_option_radio[value='{/literal}{$frotel_socid}{literal},']").parent('.delivery_option')
                   .remove();
    $(".delivery_option_radio[value='{/literal}{$frotel_poid}{literal},']").parent('.delivery_option')
                    .remove();*/
 }
  $('#center_column').ajaxStart(function(){
          /*  $(this).block({
                
            });*/
        });
        $('#center_column').ajaxSuccess(function(){
            /*$(this).unblock({
                
            });*/
        });
});
{/literal}
</script>