<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <?php if (isset($error_warning) && $error_warning) { ?>
        <div class="attention"><?php echo $error_warning; ?></div>
    <?php } ?>
    <?php if ($error) { ?>
        <div class="warning"><?php echo $error; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/payment.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
        </div>
        <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <table class="form">
                    <tr>
                        <td><label for="frotel_api"><?php echo $entry_api; ?></label></td>
                        <td><input type="text" name="frotel_api" id="frotel_api" placeholder="<?php echo $entry_api_desc; ?>" style="width: 400px;" value="<?php echo $frotel_api; ?>"/></td>
                    </tr>
                    <tr>
                        <td><label for="frotel_url"><?php echo $entry_url; ?></label></td>
                        <td><input type="text" name="frotel_url" id="frotel_url" dir="ltr" placeholder="<?php echo $entry_url_desc; ?>" style="width: 400px;text-align: left;" value="<?php echo $frotel_url; ?>"/></td>
                    </tr>
                    <tr>
                        <td valign="top"><?php echo $entry_pro_code; ?></td>
                        <td>
                            <label><input type="radio" name="frotel_pro_code" <?php echo ($frotel_pro_code=='product_id' || !$frotel_pro_code?"checked='checked'":"") ?> value="product_id" /><?php echo $text_product_id; ?></label><br/>
                            <label><input type="radio" name="frotel_pro_code" <?php echo ($frotel_pro_code=='model'?"checked='checked'":"") ?> value="model" /><?php echo $text_product_model; ?></label><br/>
                            <span class="help"><?php echo $entry_pro_code_desc; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><?php echo $entry_method_delivery; ?></td>
                        <td>
                            <label><input type="checkbox" name="frotel_express" <?php echo ($frotel_express?"checked='checked'":"") ?> value="1" /><?php echo $entry_express; ?></label><br/>
                            <label><input type="checkbox" name="frotel_registered" <?php echo ($frotel_registered?"checked='checked'":"") ?> value="1" /><?php echo $entry_registered; ?></label><br/>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><?php echo $entry_method_payments; ?></td>
                        <td>
                            <label><input type="checkbox" name="frotel_online" <?php echo ($frotel_online?"checked='checked'":"") ?> value="1" /><?php echo $entry_online; ?></label><br/>
                            <label><input type="checkbox" name="frotel_cod" <?php echo ($frotel_cod?"checked='checked'":"") ?> value="1" /><?php echo $entry_cod; ?></label><br/>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="frotel_default_online_express"><?php echo $entry_default_online_express; ?></label></td>
                        <td>
                            <input type="text" name="frotel_default_online_express" id="frotel_default_online_express" value="<?php echo $frotel_default_online_express; ?>" />
                            <br />
                            <span class="help"><?php echo $text_failed_get_price; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="frotel_default_online_registered"><?php echo $entry_default_online_registered; ?></label></td>
                        <td>
                            <input type="text" name="frotel_default_online_registered" id="frotel_default_online_registered" value="<?php echo $frotel_default_online_registered; ?>" />
                            <br />
                            <span class="help"><?php echo $text_failed_get_price; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="frotel_default_cod_express"><?php echo $entry_default_cod_express; ?></label></td>
                        <td>
                            <input type="text" name="frotel_default_cod_express" id="frotel_default_cod_express" value="<?php echo $frotel_default_cod_express; ?>" />
                            <br />
                            <span class="help"><?php echo $text_failed_get_price; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="frotel_default_cod_registered"><?php echo $entry_default_cod_registered; ?></label></td>
                        <td>
                            <input type="text" name="frotel_default_cod_registered" id="frotel_default_cod_registered" value="<?php echo $frotel_default_cod_registered; ?>" />
                            <br />
                            <span class="help"><?php echo $text_failed_get_price; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="frotel_default_weight"><?php echo $entry_default_weight; ?></label></td>
                        <td>
                            <input type="text" name="frotel_default_weight" id="frotel_default_weight" value="<?php echo $frotel_default_weight; ?>" size="3"/> <?php echo $text_weight_unit; ?>
                            <br />
                            <span class="help"><?php echo $text_weight_unit_desc; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="frotel_order_status"><?php echo $entry_order_status; ?></label></td>
                        <td>
                            <select name="frotel_order_status" id="frotel_order_status">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $frotel_order_status) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="frotel_verify_status"><?php echo $entry_verify_status; ?></label></td>
                        <td>
                            <select name="frotel_verify_status" id="frotel_verify_status">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $frotel_verify_status) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                            <br />
                            <span class="help"><?php echo $entry_verify_status_desc; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="frotel_geo_zone"><?php echo $entry_geo_zone; ?></label></td>
                        <td>
                            <select name="frotel_geo_zone_id" id="frotel_geo_zone">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                <?php if ($geo_zone['geo_zone_id'] == $frotel_geo_zone_id) { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="frotel_status"><?php echo $entry_status; ?></label></td>
                        <td>
                            <select name="frotel_status" id="frotel_status">
                                <?php if ($frotel_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="frotel_sort"><?php echo $entry_sort; ?></label></td>
                        <td><input type="text" name="frotel_sort" id="frotel_sort" value="<?php echo $frotel_sort; ?>" size="1"/></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php echo $footer; ?> 