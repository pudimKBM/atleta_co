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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	$cid = $controller->getForeignId();
	?>
	<style type="text/css">
	#tsWrapper{
		float: left;
		width: 380px;
		min-height: 375px;
	}
	#tsContainer_<?php echo $cid; ?> .tsCalendarDateInner{
		padding: 13px 0;
	}
	#tsContainer_<?php echo $cid; ?> .tsContainerCalendar{
		height: 340px;
	}
	#tsContainer_<?php echo $cid; ?> .tsCalendarMonthInner{
		height: 100%;
	}
	</style>
	<?php pjUtil::printNotice(__('infoCalendarViewTitle', true), __('infoCalendarViewDesc', true)); ?>
	<div id="tsWrapper">
		<div id="tsContainer_<?php echo $cid; ?>" class="tsContainer">
			<div id="pjWrapperTSBCalendar_<?php echo $cid;?>" class="tsContainerCalendar"></div>
		</div>
	</div>
	
	<div id="gridReservations" class="float_right w350 pj-grid"></div>
	<div class="clear_both"></div>
	
	<div id="dialogTimeslotDelete" title="<?php __('cal_del_ts_title', false, true); ?>" style="display:none"><?php __('cal_del_ts_body'); ?></div>
	<div id="dialogBookingDelete" title="<?php __('cal_del_title', false, true); ?>" style="display:none"><?php __('cal_del_body'); ?></div>
	<?php
}
?>