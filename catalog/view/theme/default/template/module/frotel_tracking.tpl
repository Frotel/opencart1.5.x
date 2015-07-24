<div class="box">
	<div class="box-heading"><?php echo $heading_title; ?></div>
	<div class="box-content">
		<table class="tracking-table">
			<tr><td><label for="frotel_tracking_factor_<?php echo $frotel_tracking_id; ?>"><?php echo $text_tracking_factor; ?></label></td></tr>
			<tr><td><input type="text" id="frotel_tracking_factor_<?php echo $frotel_tracking_id; ?>" class="tracking-field tracking_factor_<?php echo $frotel_tracking_id; ?>" /></td></tr>
			<tr>
				<td style="text-align: right;">
					<a class="button frotel_tracking" data-id="<?php echo $frotel_tracking_id; ?>"><span><?php echo $button_tracking; ?></span></a>
				</td>
			</tr>
		</table>
		<div id="frotel_tracking_result_<?php echo $frotel_tracking_id; ?>"></div>
	</div>
</div>