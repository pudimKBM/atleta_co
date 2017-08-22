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
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	if($_GET['tab'] != 1)
	{
		?>
		<div class="block b10">
			<?php __('lblSetOptionsFor');?>
			&nbsp;
			<select class="pj-form-field w150 setForeignId" id="search_calendar_id" name="calendar_id" data-controller="pjAdminOptions"  data-action="pjActionIndex" data-tab="<?php echo @$_GET['tab']; ?>">
				<?php
				foreach ($tpl['calendars'] as $calendar)
				{
					?><option value="<?php echo $calendar['id']; ?>"<?php echo $calendar['id'] == $controller->getForeignId() ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html($calendar['title']); ?></option><?php
				}
				?>
			</select>
		</div>
		<?php
	}
	if (!in_array(@$_GET['tab'], array(1,2)))
	{
		include_once dirname(__FILE__) . '/elements/submenu.php';
	}
	if (isset($tpl['calendars']) && count($tpl['calendars']) > 1 && $_GET['tab'] != 1)
	{
		?>
		<div class="b5 overflow">
			<input type="hidden" name="copy_tab_id" value="<?php echo @$_GET['tab']; ?>" />
			<input type="button" value="<?php __('lblOptionCopy'); ?>" class="pj-button float_left align_middle r5" id="btnCopyOptions" />
			<select name="copy_calendar_id" class="pj-form-field w300">
			<?php
			foreach ($tpl['calendars'] as $calendar)
			{
				if ($calendar['id'] == $controller->getForeignId())
				{
					continue;
				}
				?><option value="<?php echo $calendar['id']; ?>"><?php echo stripslashes($calendar['title']); ?></option><?php
			}
			?>
			</select>
			<a class="pj-form-langbar-tip listing-tip" href="#" title="<?php echo nl2br(__('lblOptionCopyTip', true)); ?>"></a>
		</div>
		
		<div id="dialogCopyOptions" style="display:none" title="<?php echo htmlspecialchars(__('lblOptionCopyTitle', true)); ?>"><?php __('lblOptionCopyDesc'); ?></div>
		<?php
	}
	switch (@$_GET['tab'])
	{
		case 5:
			pjUtil::printNotice(@$titles['AO25'], @$bodies['AO25'], false);
			include dirname(__FILE__) . '/elements/confirmation.php';
			break;
		case 6:
			pjUtil::printNotice(@$titles['AO26'], @$bodies['AO26']);
			include dirname(__FILE__) . '/elements/terms.php';
			break;
		default:
			switch ($_GET['tab'])
			{
				case 1:
					pjUtil::printNotice(@$titles['AO21'], @$bodies['AO21']);
					break;
				case 3:
					pjUtil::printNotice(@$titles['AO23'], @$bodies['AO23']);
					break;
				case 4:
					pjUtil::printNotice(@$titles['AO24'], @$bodies['AO24']);
					break;
				case 7:
					pjUtil::printNotice(@$titles['AO27'], @$bodies['AO27']);
					break;
				case 8:
					pjUtil::printNotice(@$titles['AO28'], @$bodies['AO28']);
					break;
			}
			include dirname(__FILE__) . '/elements/tab.php';
	}
	?>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.btnCopy = "<?php __('btnCopy', false, true); ?>";
	myLabel.btnCancel = "<?php __('btnCancel', false, true); ?>";
	</script>
	<?php
}
?>