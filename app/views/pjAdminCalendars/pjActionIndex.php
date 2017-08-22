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
	
	<?php pjUtil::printNotice(@$titles['AC10'], @$bodies['AC10']); ?>
	
	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
			<input type="hidden" name="controller" value="pjAdminCalendars" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button" value="<?php __('btnAddCalendar'); ?>" />
		</form>
		<form action="" method="get" class="pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch', false, true); ?>" />
		</form>
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.currentCalendarId = <?php echo (int) $controller->getForeignId(); ?>;
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['user_id']) && (int) $_GET['user_id'] > 0)
	{
		?>pjGrid.queryString += "&user_id=<?php echo (int) $_GET['user_id']; ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.title = "<?php __('calendar_title', false, true); ?>";
	myLabel.name = "<?php __('lblName', false, true); ?>";
	myLabel.email = "<?php __('email', false, true); ?>";
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
	</script>
	<?php
}
?>