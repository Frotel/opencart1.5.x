/**
 * User: ReZa ZaRe <Rz.ZaRe@Gmail.com>
 * Date: 7/16/15
 * Time: 10:03 AM
 */

$(function(){
    $(document).on('click','.frotel_tracking', function() {
        var t=$(this);
        if(t.attr('disabled'))  return false;
        var id= t.attr('data-id');
        t.attr('disabled', true);
        $('#frotel_tracking_result_'+id).html('');
        $.ajax({
            type: 'GET',
            url: 'index.php?route=payment/frotel/tracking',
            data:{factor: $('.tracking_factor_'+id).val()},
            dataType:'json',
            success: function(d) {
                var html = '';
                if(d.error!=undefined) {
                    if(d.error==1){
                        html = '<div class="warning">'+ d.message+'</div>';
                    } else {
                        d = d.message;
                        if (d.barcode)
                            html = 'بارکد پستی : '+ d.barcode+'<br />';
                        html += 'وضعیت : <strong>'+ d.status+'</strong>';
                    }
                } else {
                    html = '<div class="warning">در دریافت اطلاعات خطایی رخ داده است. <br />لطفا مجددا تلاش کنید.</div>';
                }
                $('#frotel_tracking_result_'+id).html(html);
            },
            complete:function(){
                t.removeAttr('disabled');
            }
        });
    });

});