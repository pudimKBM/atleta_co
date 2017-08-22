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
		<select class="pj-form-field w150 setForeignId" id="search_calendar_id" name="calendar_id" data-controller="pjAdminTime"  data-action="pjActionIndex" data-tab="">
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
	$time_slot_length = __('time_slot_length', true);
	ksort($time_slot_length);
	
	$show_period = 'false';
	if((strpos($tpl['option_arr']['o_time_format'], 'a') > -1 || strpos($tpl['option_arr']['o_time_format'], 'A') > -1))
	{
		$show_period = 'true';
	}
	$days = __('days', true);
	$w_days = array(
		'monday' => $days[1],
		'tuesday' => $days[2],
		'wednesday' => $days[3],
		'thursday' => $days[4],
		'friday' => $days[5],
		'saturday' => $days[6],
		'sunday' => $days[0]
	);
	
	$after_midnight = false;
	$selected_day = 'monday';
	if(isset($_GET['day']))
	{
		$selected_day = $_GET['day'];
	}
	$lunch_from_arr = array();
	if (isset($tpl['wt_arr']) && !empty($tpl['wt_arr']))
	{
		$from = date('H:i', strtotime($tpl['wt_arr'][$selected_day.'_from']));
		$to = date('H:i', strtotime($tpl['wt_arr'][$selected_day.'_to']));
		$lunch_from = date('H:i', strtotime($tpl['wt_arr'][$selected_day.'_lunch_from']));
		$lunch_to = date('H:i', strtotime($tpl['wt_arr'][$selected_day.'_lunch_to']));
		$lunch_length = (strtotime($tpl['wt_arr'][$selected_day.'_lunch_to']) - strtotime($tpl['wt_arr'][$selected_day.'_lunch_from'])) / 60;
		if($show_period == 'true')
		{
			if(strpos($tpl['option_arr']['o_time_format'], 'A') > -1)
			{
				$from = date('h:i A', strtotime($tpl['wt_arr'][$selected_day.'_from']));
				$to = date('h:i A', strtotime($tpl['wt_arr'][$selected_day.'_to']));
				$lunch_from = date('h:i A', strtotime($tpl['wt_arr'][$selected_day.'_lunch_from']));
				$lunch_to = date('h:i A', strtotime($tpl['wt_arr'][$selected_day.'_lunch_to']));
			}else{
				$from = date('h:i a', strtotime($tpl['wt_arr'][$selected_day.'_from']));
				$to = date('h:i a', strtotime($tpl['wt_arr'][$selected_day.'_to']));
				$lunch_from = date('h:i a', strtotime($tpl['wt_arr'][$selected_day.'_lunch_from']));
				$lunch_to = date('h:i a', strtotime($tpl['wt_arr'][$selected_day.'_lunch_to']));
			}
		}
		
		$price = $tpl['wt_arr'][$selected_day.'_price'];
		$limit = $tpl['wt_arr'][$selected_day.'_limit'];
		$length = $tpl['wt_arr'][$selected_day.'_length'];
		$slots = $tpl['wt_arr'][$selected_day.'_slots'];
			
		$checked = NULL;
		$dayoff_class = NULL;
		$day_price = NULL;
		$lunch_break = NULL;
		$slot_arr = array();
		
		if($lunch_length > 0)
		{
			$lunch_break = ' checked="checked"';
		}
			
		if ($tpl['wt_arr'][$selected_day.'_dayoff'] == 'T')
		{
			$day_price = ' disabled';
	
			$checked = ' checked="checked"';
			$dayoff_class = ' tsDayOff';
		}
		
		$step = $tpl['wt_arr'][$selected_day . '_length'] * 60;
		$start_ts = strtotime($tpl['wt_arr'][$selected_day . '_from']);
		$lunch_from_ts = strtotime($tpl['wt_arr'][$selected_day.'_lunch_from']);
		if(date('H:i', $lunch_from_ts) == '00:00')
		{
			$lunch_from_ts = $lunch_from_ts + (24 * 60 * 60);
		}
		$i = $start_ts;		
		for($n = 1; $n <= $slots; $n++)
		{
			if($i+$step > strtotime(date('Y-m-d 00:00:00', $start_ts + (24 * 60 * 60))))
			{
				$after_midnight = true;
			}
			$slot_arr[$i + $step] = date($tpl['option_arr']['o_time_format'], $i) . ' - ' . date($tpl['option_arr']['o_time_format'], $i + $step);
			if($i + $step == $lunch_from_ts)
			{
				$i = strtotime($tpl['wt_arr'][$selected_day.'_lunch_to']);
			}else{
				$i = $i + $step;
			}
		}
		
		$i = $start_ts;
		for($n = 1; $n <= $slots; $n++)
		{
			$lunch_from_arr[date('H:i', ($i + $step))] = date($tpl['option_arr']['o_time_format'], $i + $step);
			$i = $i + $step;
		}
	} else {
		$from = NULL;
		$to = NULL;
		$lunch_from = NULL;
		$lunch_to = NULL;
		$lunch_length = NULL; 
		$lunch_break = NULL;
		
		$checked = NULL;
			
		$limit = NULL;
		$length = NULL;
		$price = NULL;
		$day_price = NULL;
		$slots = NULL;
		$slot_arr = array();
	}
	?>
	<style>
		.ui-spinner{
			float: left;
			margin-right: 5px;
		}
	</style>
	<form id="frmPriceDefault" action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionIndex" method="post" class="form pj-form">
		<input type="hidden" name="working_time" value="1" />
		<?php
		if ($controller->isAdmin())
		{
			?><input type="hidden" name="id" value="<?php echo (int) $tpl['wt_arr']['id']; ?>" /><?php
		}
		?>
		<p>
			<label class="title"><?php __('lblDayOfWeek');?></label>
			<span class="inline_block">
				<select name="day_of_week" class="pj-form-field w150">
					<option value="">-- <?php __('lblChoose');?> --</option>
					<?php
					foreach($w_days as $k => $v)
					{
						?><option value="<?php echo $k;?>"<?php echo $k == $selected_day ? ' selected="selected"' : NULL;?>><?php echo $v;?></option><?php
					} 
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblSetAsDayOff');?></label>
			<span class="block float_left t5">
				<input type="checkbox" name="<?php echo $selected_day?>_dayoff" value="T"<?php echo $checked;?> class="working_day tsIsDayOff" data-day="<?php echo $selected_day;?>"/>
			</span>
		</p>
		<p class="tsWorkingDay_<?php echo $selected_day;?><?php echo $dayoff_class;?>">
			<label class="title"><?php __('lblStartTime');?></label>
			<span class="inline_block">
				<input type="text" name="<?php echo $selected_day?>_from" value="<?php echo $from;?>" readonly="readonly" class="pj-timepicker pj-form-field w80"/>
			</span>
		</p>
		<p class="tsWorkingDay_<?php echo $selected_day;?><?php echo $dayoff_class;?>">
			<label class="title"><?php __('time_tbl_length');?></label>
			<span class="inline_block">
				<select name="<?php echo $selected_day; ?>_length" id="slot_length" class="pj-form-field w100">
				<?php
				foreach ($time_slot_length as $sk => $sv)
				{
					?><option value="<?php echo $sk; ?>"<?php echo $length == $sk ? ' selected="selected"' : NULL; ?>><?php echo $sv; ?></option><?php
				}
				?>
				</select>
			</span>
		</p>
		<p class="tsWorkingDay_<?php echo $selected_day;?><?php echo $dayoff_class;?>">
			<label class="title"><?php __('lblNumberOfSlots');?></label>
			<span class="inline_block">
				<select name="<?php echo $selected_day; ?>_slots" id="number_of_slots" class="pj-form-field w100">
				<?php
				foreach (range(1, 144) as $number) 
				{
    
					?><option value="<?php echo $number; ?>"<?php echo $slots == $number ? ' selected="selected"' : NULL; ?>><?php echo $number; ?></option><?php
				}
				?>
				</select>
			</span>
		</p>
		<div id="tsSlotBox">
			<p class="tsWorkingDay_<?php echo $selected_day;?><?php echo $dayoff_class;?>">
				<label class="title"><?php __('time_tbl_break');?></label>
				<span class="block float_left">
					<input type="radio" id="lunch_break_yes" name="lunch_break" value="T"<?php echo $lunch_length == 0 ? NULL : ' checked=checked';?> class="tsLunchBreak float_left t5 r5"/>
					<label for="<?php echo $selected_day?>_lunch_break" class="block float_left t6 r5"><?php __('lblYes');?></label>
					<input type="radio" id="lunch_break_no" name="lunch_break" value="F"<?php echo $lunch_length > 0 ? NULL : ' checked=checked';?> class="tsLunchBreak float_left t5 r5"/>
					<label for="lunch_break_no" class="block float_left t6 r5"><?php __('lblNo');?></label>
				</span>
			</p>
			<p id="tsLunchBreakContainer" class="tsWorkingDay_<?php echo $selected_day;?><?php echo $dayoff_class;?>" style="display: <?php echo $dayoff_class == NULL ? ($lunch_length == 0 ? 'none' : 'block') : 'none';?>;">
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<label class="block float_left t6 r5"><?php __('lblFrom');?></label>
					<select name="<?php echo $selected_day; ?>_lunch_from" id="lunch_from" class="pj-form-field w100 float_left">
					<?php
					foreach($lunch_from_arr as $k => $v)
					{
						?><option value="<?php echo $v; ?>"<?php echo $lunch_from == $v ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
					}
					?>
					</select>
					<label class="block float_left t6 r5 l5"><?php __('lblLength');?></label>
					<input type="text" name="lunch_length" value="<?php echo $lunch_length;?>" class="pj-form-field field-int w80 float_left"/>
					<label class="block float_left t6 l5"><?php __('lblMinutes');?></label>
				</span>
			</p>
			
			<div id="tsSlotsContainer">
				<div class="p tsWorkingDay_<?php echo $selected_day;?><?php echo $dayoff_class;?>">
					<label class="title"><?php __('booking_slots');?></label>
					<div class="block float_left">
						<?php
						$to = null;
						foreach($slot_arr as $k => $v)
						{
							?><label class="block"><?php echo $v;?></label><?php
							$to = date($tpl['option_arr']['o_time_format'], $k);
						} 
						if($after_midnight == true)
						{
							?><div id="after_midnight"></div><?php
						}
						?>
					</div>
				</div>
				<p class="tsWorkingDay_<?php echo $selected_day;?><?php echo $dayoff_class;?>">
					<label class="title"><?php __('lblEndTime');?></label>
					<span class="inline_block">
						<input type="text" name="<?php echo $selected_day?>_to" readonly="readonly" value="<?php echo $to;?>" class="pj-form-field w80"/>
					</span>
				</p>
			</div><!-- /.tsSlotsContainer -->
		</div>	
		
		<p class="tsWorkingDay_<?php echo $selected_day;?><?php echo $dayoff_class;?>">
			<label class="title"><?php __('time_tbl_price');?></label>
			<span class="block float_left r5">
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="<?php echo $selected_day; ?>_price" id="<?php echo $selected_day; ?>_price" class="pj-form-field tsPriceField w50 align_right" value="<?php echo $price; ?>"/>
				</span>
			</span>
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>" data-day="<?php echo $selected_day; ?>" class="day-price block float_left t6 tsWorkingDay_<?php echo $selected_day;?>"><?php __('time_tbl_customize'); ?></a>
		</p>
		<p class="tsWorkingDay_<?php echo $selected_day;?><?php echo $dayoff_class;?>">
			<label class="title"><?php __('time_limit'); ?></label>
			<input type="text" name="<?php echo $selected_day; ?>_limit" id="<?php echo $selected_day; ?>_limit" class="pj-form-field field-int w50 tsWorkingDay_<?php echo $selected_day;?>" value="<?php echo $limit; ?>"/>
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
	</form>
	<div id="dialogDayPrice" title="<?php __('time_dp_title'); ?>" style="display: none"></div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.showperiod = <?php echo $show_period; ?>;
	</script>
	<?php
}
?>