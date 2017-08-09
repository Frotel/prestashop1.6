<script type="text/javascript">
$(document).ready(function(){
    $("#get_tracking").click(function(){
        var factor = $("#factor_rahgiri").val();
        $.ajax({
            url:"{$ajaxurl}",
            type:"POST",
            data:{ldelim}'action':'get_tracking','factor':factor{rdelim},
            success:function(data)
            {
                $(".myres").html(data);
            }
        });
    });
});
</script>
<div class="row">
    <div class="col-lg-12">
        <div class="col-lg-3">
        </div>
        <div class="col-lg-6">
            <div class="form-group">
                <label for="exampleInputEmail1">برای پیگری خرید شماره فاکتور خود را وارد نمایید.</label>
                <input type="text" name="factor_rahgiri" id="factor_rahgiri" class="form-control" placeholder="شماره فاکتور"> 
            </div>
            <button type="button" class="btn btn-default btn-lg"       id="get_tracking">رهگیری</button>
        </div>
        <div class="col-lg-3">
        </div>
    </div>
</div>
<div class="row" style="margin-top:15px;">
    <div class="col-lg-3">
    </div>
    <div class="myres col-lg-6">
    </div>
    <div class="col-lg-3">
    </div>
</div>