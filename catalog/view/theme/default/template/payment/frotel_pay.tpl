<?php echo $header; ?>
<?php echo $column_left; ?>
<?php echo $column_right; ?>
<style type="text/css">
    .bank{
        width: 85px;
        float: right;
        padding: 5px;
        text-align: center;
    }
    .bank > input {
        display: none;
    }
    .bank.selected{
        padding: 3px;
        border:2px solid green;
    }
    .banks {
        overflow: hidden;
        margin-top: 20px;
    }
</style>
<div id="content" xmlns="http://www.w3.org/1999/html">
    <?php echo $content_top; ?>

    <h1><?php echo $text_title; ?></h1>

        لطفا برای پرداخت هزینه سفارش خود یکی از درگاه های زیر را انتخاب کنید : <br />
        <div class="banks">
            <?php
                foreach($banks as $bank){
                    echo '<label class="bank">
                        <img src="'.$bank['logo'].'" width="64" height="64" class="bank_thumbnail" alt="'.$bank['name'].'" /><br />
                        <input type="radio" name="bank" value="'.$bank['id'].'" /> '.$bank['name'].'
                    </label>';
                }
            ?>
        </div>
        <div class="buttons">
            <input type="button" id="btn_pay" value="پرداخت" class="button" />
        </div>
        <div class="result_pay">

        </div>
        <script type="text/javascript">
            $(function(){
                $('.bank').click(function() {
                    $('.bank').removeClass('selected');
                    $(this).addClass('selected');
                });
                $('#btn_pay').click(function() {
                    var t=$(this);
                    if(t.attr('disabled'))  return false;
                    var b=$('input[name="bank"]:checked');
                    var r=$('.result_pay');
                    if (b.length==0){
                        r.html('<div class="warning"><?php echo $choose_bank; ?></div>');
                        return false;
                    }
                    r.html('');
                    t.attr('disabled','disabled');
                    $.ajax({
                        url:'<?php echo $url ?>',
                        data:{bank:b.val()},
                        dataType:'json',
                        success:function(d){
                            if(d.error!=undefined){
                                if(d.error==0){
                                    r.html('<?php echo $text_start_transaction; ?><br />'+d.message);
                                }else{
                                    r.html('<div class="warning">'+ d.message+'</div>');
                                }
                            }else{
                                r.html('<div class="warning"><?php echo $text_error_response; ?></div>');
                            }
                        },
                        complete:function(){
                            t.removeAttr('disabled');
                        }
                    });
                });
            });
        </script>
    <?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>