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
	<div class="block b10">
		<?php __('lblSetWtimeAndPrice');?>
		&nbsp;
		<select class="pj-form-field w150 setForeignId" id="search_calendar_id" name="calendar_id" data-controller="pjAdminTime"  data-action="pjActionIndex"  data-tab="">
			<?php
			foreach ($tpl['calendars'] as $calendar)
			{
				?><option value="<?php echo $calendar['id']; ?>"<?php echo $calendar['id'] == $controller->getForeignId() ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html($calendar['title']); ?></option><?php
			}
			?>
		</select>
	</div>
	<?php
	include dirname(__FILE__) . '/elements/menu_options.php';
	
	pjUtil::printNotice(@$titles['AT04'], @$bodies['AT04']);
	
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	
	$show_period = 'false';
	if((strpos($tpl['option_arr']['o_time_format'], 'a') > -1 || strpos($tpl['option_arr']['o_time_format'], 'A') > -1))
	{
		$show_period = 'true';
	}
	?>
	<style>
		.ui-spinner{
			float: left;
			margin-right: 5px;
		}
	</style>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionCustom" method="post" class="form pj-form" id="frmTimeCustom">
		<input type="hidden" name="custom_time" value="1" />
		<fieldset class="fieldset white">
			<legend><?php __('time_custom'); ?></legend>
			<p>
				<label class="title"><?php __('time_date'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-after">
					<input type="text" name="date" id="date" class="pj-form-field w80 datepick pointer required pps" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
					<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
				</span>
			</p>
			<p>
				<label class="title"><?php __('time_is'); ?></label>
				<span class="block float_left t5 b10"><input type="checkbox" name="is_dayoff" id="is_dayoff" value="T"/></span>
			</p>
			<p class="business">
				<label class="title"><?php __('time_from'); ?></label>
				<span class="inline-block">
					<input name="start" readonly="readonly" class="pj-timepicker pj-form-field w80 required"/>
				</span>
			</p>
			<p class="business">
				<label class="title"><?php __('time_length'); ?></label>
				<select name="slot_length" id="slot_length" class="pj-form-field w120 pps">
				<?php
				$time_slot_length = __('time_slot_length', true);
				ksort($time_slot_length);
				foreach ($time_slot_length as $sk => $sv)
				{
					?><option value="<?php echo $sk; ?>"><?php echo $sv; ?></option><?php
				}
				?>
				</select>
			</p>
			<p class="business">
				<label class="title"><?php __('lblNumberOfSlots');?></label>
				<span class="inline_block">
					<select name="slots" id="number_of_slots" class="pj-form-field w100">
					<?php
					foreach (range(1, 144) as $number) 
					{
	    
						?><option value="<?php echo $number; ?>"><?php echo $number; ?></option><?php
					}
					?>
					</select>
				</span>
			</p>
			<div id="tsCustomSlotBox">
				
			</div>
			
			<p class="business">
				<label class="title"><?php __('time_price'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="price" id="price" class="pj-form-field tsPriceField w70 align_right number" />
				</span>
			</p>
			<p class="business">
				<label class="title">&nbsp;</label>
				<input type="checkbox" name="single_price" id="single_price" value="1" checked="checked" class="pps" />
				<label for="single_price"><?php __('time_single_price'); ?></label>
			</p>
			
			<div id="boxPPS" class="p business"></div>
			
			<p class="business">
				<label class="title"><?php __('time_limit'); ?></label>
				<input type="text" name="slot_limit" id="slot_limit" class="pj-form-field spin w60" value="1" />
			</p>
			
			
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button float_left r10" />
				<?php
				$message =  __('lblSlotLimitation', true);
				$message = str_replace("{AFTER}", date($tpl['option_arr']['o_time_format'], strtotime(date('Y-m-d') . ' 00:00:00')), $message);
				?>
				<label class="tsMidnightMessage" style="display: none;"><?php echo $message;?></label>
			</p>
		</fieldset>
	</form>
	
	<div class="b10">
		<?php
		$yesno = __('_yesno', true);
		$filter = __('custom_filter', true);
		?>
		<div class="float_right">
			<a href="#" class="pj-button btn-all"><?php echo $filter['A']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="is_dayoff" data-value="F"><?php echo $filter['T']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="is_dayoff" data-value="T"><?php echo $filter['F']; ?></a>
		</div>
		<br class="clear_right" />
	</div>
	
	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	var myLabel = myLabel || {};
	myLabel.showperiod = <?php echo $show_period; ?>;
	myLabel.time_price = "<?php __('time_price', false, true); ?>";
	myLabel.time_date = "<?php __('time_date', false, true); ?>";
	myLabel.time_start = "<?php __('time_from', false, true); ?>";
	myLabel.time_end = "<?php __('time_to', false, true); ?>";
	myLabel.time_lunch_start = "<?php __('time_lunch_from', false, true); ?>";
	myLabel.time_lunch_end = "<?php __('time_lunch_to', false, true); ?>";
	myLabel.time_dayoff = "<?php __('time_is', false, true); ?>";
	myLabel.time_yesno = <?php echo pjAppController::jsonEncode(__('_yesno', true)); ?>;
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
	</script>
	<?php
}
?>