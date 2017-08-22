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
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	<?php pjUtil::printNotice(__('infoThemeTitle', true), __('infoThemeDesc', true), false, false); ?>
	<form action="" method="get" class="pj-form form">
		<p>
			<label class="title"><?php __('lblPreviewCalendar'); ?></label>
			<select class="pj-form-field w200" id="preview_calendar_id" name="calendar_id">
				<?php
				foreach ($tpl['calendars'] as $calendar)
				{
					?><option value="<?php echo $calendar['id']; ?>"<?php echo $controller->getForeignId() == $calendar['id'] ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html($calendar['title']); ?></option><?php
				}
				?>
			</select>
		</p>
	</form>
	<div class="theme-holder pj-loader-outer">
		<?php include PJ_VIEWS_PATH . 'pjAdminOptions/elements/theme.php'; ?>
	</div>
	<div class="clear_both"></div>
	<?php
}
?>