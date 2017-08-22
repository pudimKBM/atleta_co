<?php
if (isset($tpl['arr']) && !empty($tpl['arr']))
{
	?>
	<form action="" method="post" class="form pj-form">
		<input type="hidden" name="send_email" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<input type="hidden" name="from" value="<?php echo $tpl['arr']['from']; ?>" />
		<p><label class="fs12"><?php printf(__('send_email_title', true), $tpl['arr']['client_name'])?></label></p>
		<?php if (!empty($tpl['arr']['client_email'])) : ?>
		<p>
			<span class="block b5 bold"><?php __('booking_email'); ?></span>
			<span><input class="pj-form-field w300 required required" type="text" name="to[]" value="<?php echo pjSanitize::html($tpl['arr']['client_email']); ?>"/></span>
		</p>
		<?php endif; ?>
		<p>
			<span class="block b5 bold"><?php __('booking_subject'); ?></span>
			<span><input type="text" name="subject" id="confirm_subject" class="pj-form-field w600 required" value="<?php echo pjSanitize::html($tpl['arr']['subject']); ?>" /></span>
		</p>
		<p>
			<span class="block b5 bold"><?php __('booking_message'); ?></span>
			<span><textarea name="message" id="confirm_message" class="pj-form-field w600 h300 required"><?php echo stripslashes(str_replace(array('\r\n', '\n'), '&#10;', $tpl['arr']['message'])); ?></textarea></span>
		</p>
	</form>
	<?php
}
?>