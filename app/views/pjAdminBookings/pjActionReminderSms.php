<?php
if (isset($tpl['arr']) && !empty($tpl['arr']))
{
	?>
	<form action="" method="post" class="form pj-form">
		<input type="hidden" name="send_sms" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<p><label><?php printf(__('send_sms_title', true), $tpl['arr']['client_name'])?></label></p>
		<?php if (!empty($tpl['arr']['client_phone'])) : ?>
		<p>
			<span class="block b5 bold"><?php __('booking_phone'); ?></span>
			<span><input class="pj-form-field w200 required" type="text" name="to[]" value="<?php echo pjSanitize::html($tpl['arr']['client_phone']); ?>"/></span>
		</p>
		<?php endif; ?>
		<p>
			<span class="block b5 bold"><?php __('booking_message'); ?></span>
			<span><textarea name="message" id="confirm_message" class="pj-form-field w600 h120 required"><?php echo stripslashes(str_replace(array('\r\n', '\n'), '&#10;', $tpl['arr']['message'])); ?></textarea></span>
		</p>
		
	</form>
	<?php
}
?>