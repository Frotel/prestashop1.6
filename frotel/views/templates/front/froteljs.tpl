<script type="text/javascript">
$(document).ready(function(){

    $('#getsend_price').click(function(){
        $(".frotelloading").css("display","inline");
        $(".message_frotel").css("display","none");
        ostan = $('#FrotelOstan').val();
        city = $('#FrotelCity').val();

        $.ajax({
            url:"{$ajaxurl}",
            type:"POST",
            data:{ldelim}'action':'getPriceFrotel','id_state':ostan,'id_city':city{rdelim},
            success:function(data)
            {
                
                $(".message_frotel").css("display","inline");
                $(".frotelloading").css("display","none");
                if(data == 1)
                {
                    $(".message_frotel").html("استان و شهر شما انتخاب شد .")
                }else{
                    $(".message_frotel").html("خطا در اتصال به سرور")
                }
                
            }
        });

    });

});

function setcityfrotel(city)
{
    if(city == -1) return;
    $(".frotelloading").css("display","inline");
        $(".message_frotel").css("display","none");
        ostan = $('#FrotelOstan').val();
        city = $('#FrotelCity').val();

        $.ajax({
            url:"{$ajaxurl}",
            type:"POST",
            data:{ldelim}'action':'getPriceFrotel','id_state':ostan,'id_city':city{rdelim},
            success:function(data)
            {
                
                $(".message_frotel").css("display","inline");
                $(".frotelloading").css("display","none");
                if(data == 1)
                {
                    $(".message_frotel").html("استان و شهر شما انتخاب شد .")
                }else{
                    $(".message_frotel").html(data)
                }
                
            }
        });

}

</script>