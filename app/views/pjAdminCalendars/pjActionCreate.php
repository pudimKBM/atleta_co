<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	?>
	<?php pjUtil::printNotice(@$titles['AC11'], @$bodies['AC11']); ?>
	
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
	<div class="multilang"></div>
	<?php endif; ?>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionCreate" method="post" id="frmCreateCalendar" class="form pj-form" autocomplete="off">
		<input type="hidden" name="calendar_create" value="1" />
		<?php if ($controller->isAdmin()) : ?>
		<p>
			<label class="title"><?php __('calendar_user'); ?></label>
			<span class="inline_block">
				<select name="user_id[]" id="ts_user_id" class="pj-form-field required" multiple="multiple" data-choose="-- <?php __('lblChoose');?> --" data-checkall="<?php __('lblCheckAll');?>" data-uncheckall="<?php __('lblUnCheckAll');?>">
					<?php
					if (isset($tpl['user_arr']))
					{
						foreach ($tpl['user_arr'] as $user)
						{
							?><option value="<?php echo $user['id']; ?>"><?php echo pjSanitize::html($user['name']); ?></option><?php
						}
					}
					?>
				</select>
			</span>
		</p>
		<?php endif; ?>
		<?php
		foreach ($tpl['lp_arr'] as $v)
		{
		?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('calendar_title'); ?>:</label>
				<span class="inline_block">
					<input type="text" name="i18n[<?php echo $v['id']; ?>][title]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" />
					<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					<?php endif; ?>
				</span>
			</p>
			<?php
		}
		?>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
			<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCalendars&action=pjActionIndex';" />
		</p>
	</form>
	<?php
}
?>