<?php echo $header; ?>
<?php echo $column_left; ?>
<?php echo $column_right; ?>
<div id="content" xmlns="http://www.w3.org/1999/html">
    <?php echo $content_top; ?>

    <h1><?php echo $text_title_select_city; ?></h1>
    <div>
        <?php echo $text_select_city_desc; ?><br />
    </div>
    <form action="<?php echo $url ?>" method="post">
        <table width="300">
            <tr>
                <td style="width: 100px">
                    <label for="province">
                        <span class="required">*</span> <?php echo $entry_province; ?> :
                    </label>
                </td>
                <td>
                    <select id="province" name="province_id" style="width: 100%" onchange="ldMenu(this.value,'city');"></select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="city">
                        <span class="required">*</span> <?php echo $entry_city; ?> :
                    </label>
                </td>
                <td>
                    <select id="city" name="city_id" style="width: 100%"><option value="0">-- استان را انتخاب کنید --</option></select>
                </td>
            </tr>
        </table>
        <?php if($error) { ?>
            <br />
            <div class="warning">
                <?php echo $error; ?>
            </div>
        <?php } ?>
        <div class="buttons">
            <input type="submit" value="<?php echo $button_continue; ?>" class="button" />
        </div>
    </form>
    <?php echo $content_bottom; ?>
</div>
<script type="text/javascript">
    $(function(){
        loadOstan('province');
        var province = <?php echo isset($province_id)?$province_id:'undefined'; ?>;
        var city = <?php echo isset($city_id)?$city_id:'undefined'; ?>;
        if (province) {
            $('#province').val(province);
            ldMenu(province,'city');
            if (city) {
                $('#city').val(city);
            }
        }
    });
</script>
<?php echo $footer; ?>