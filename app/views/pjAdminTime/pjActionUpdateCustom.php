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
	
	include dirname(__FILE__) . '/elements/menu_options.php';
	
	pjUtil::printNotice(@$titles['AT04'], @$bodies['AT04']);
	
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	
	$business = $tpl['arr']['is_dayoff'] == 'T' ? 'none' : NULL;
	$show_period = 'false';
	if((strpos($tpl['option_arr']['o_time_format'], 'a') > -1 || strpos($tpl['option_arr']['o_time_format'], 'A') > -1))
	{
		$show_period = 'true';
	}
	
	$start_time = strtotime($tpl['arr']['date'] . ' ' . $tpl['arr']['start_time']);
	$end_time = strtotime($tpl['arr']['date'] . ' ' . $tpl['arr']['end_time']);
	$lunch_from_ts = strtotime($tpl['arr']['date'] . ' ' . $tpl['arr']['start_lunch']);
	$lunch_to_ts = strtotime($tpl['arr']['date'] . ' ' . $tpl['arr']['end_lunch']);
	
	if($lunch_from_ts < $start_time)
	{
		$lunch_from_ts = $lunch_from_ts + (24 * 60 * 60);
		$lunch_to_ts = $lunch_to_ts + (24 * 60 * 60);
	}
	if($end_time < $start_time)
	{
		$end_time = $end_time + (24 * 60 * 60);
	}
	$lunch_length = strtotime($tpl['arr']['end_lunch']) - strtotime($tpl['arr']['start_lunch']) ;
	$lunch_to = $lunch_from_ts + $lunch_length;
	
	$step = $tpl['arr']['slot_length'] * 60;
	
	$after_midnight = false;
	
	$i = $start_time;
	$slot_arr = array();
	$lunch_from_arr = array();
	$price_arr = array();
	$slots = $tpl['arr']['slots'];
	if(empty($slots))
	{
		while($i < $end_time)
		{
			$lunch_from_arr[date('H:i', ($i + $step))] = date($tpl['option_arr']['o_time_format'], $i + $step);
			$i = $i + $step;
		}
		$slots = 0;
		$i = $start_time;
		while($i < $end_time)
		{
			if($i+$step > strtotime(date('Y-m-d 00:00:00', $start_time + (24 * 60 * 60))))
			{
				$after_midnight = true;
			}
			$slot_arr[$i + $step] = date($tpl['option_arr']['o_time_format'], $i) . ' - ' . date($tpl['option_arr']['o_time_format'], $i + $step);
			foreach ($tpl['price_arr'] as $v)
			{
				if ($v['start_ts'] == $i && $v['end_ts'] == $i + $step)
				{
					$price_arr[$i . '|' . ($i + $step)] = $v['price'];
					break;
				}
			}
			if($i + $step == $lunch_from_ts)
			{
				$i = $lunch_to;
			}else{
				$i = $i + $step;
				$slots++;
			}
		}
	}else{
		for($n = 1; $n <= $slots; $n++)
		{
			$lunch_from_arr[date('H:i', ($i + $step))] = date($tpl['option_arr']['o_time_format'], $i + $step);
			$i = $i + $step;
		}
		$i = $start_time;
		for($n = 1; $n <= $slots; $n++)
		{
			if($i+$step > strtotime(date('Y-m-d 00:00:00', $start_time + (24 * 60 * 60))))
			{
				$after_midnight = true;
			}
			$slot_arr[$i + $step] = date($tpl['option_arr']['o_time_format'], $i) . ' - ' . date($tpl['option_arr']['o_time_format'], $i + $step);
			foreach ($tpl['price_arr'] as $v)
			{
				if ($v['start_ts'] == $i && $v['end_ts'] == $i + $step)
				{
					$price_arr[$i . '|' . ($i + $step)] = $v['price'];
					break;
				}
			}
			if($i + $step == $lunch_from_ts)
			{
				$i = $lunch_to;
			}else{
				$i = $i + $step;
			}
		}
	}
	?>
	<style>
		.ui-spinner{
			float: left;
			margin-right: 5px;
		}
	</style>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionUpdateCustom" method="post" class="form pj-form" id="frmTimeCustom">
		<input type="hidden" name="custom_time" value="1" />
		<input type="hidden" name="id" value="<?php echo @$tpl['arr']['id']; ?>" />
		
		<p>
			<label class="title"><?php __('time_date'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-after">
				<input type="text" name="date" id="date" class="pj-form-field w80 datepick pointer required pps" readonly="readonly" value="<?php echo pjUtil::formatDate($tpl['arr']['date'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
				<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
			</span>
		</p>
		<p>
			<label class="title"><?php __('time_is'); ?></label>
			<span class="block float_left t5 b10"><input type="checkbox" name="is_dayoff" id="is_dayoff" value="T" <?php echo $tpl['arr']['is_dayoff'] == 'T' ? ' checked="checked"' : NULL; ?>/></span>
		</p>
		<p class="business">
			<label class="title"><?php __('time_from'); ?></label>
			<span class="inline-block">
				<input name="start" readonly="readonly" value="<?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['start_time']));?>" class="pj-timepicker pj-form-field w80 required"/>
			</span>
		</p>
		<p class="business" style="display: <?php echo $business; ?>">
			<label class="title"><?php __('time_length'); ?></label>
			<select name="slot_length" id="slot_length" class="pj-form-field w120 pps">
			<?php
			$time_slot_length = __('time_slot_length', true);
			ksort($time_slot_length);
			foreach ($time_slot_length as $sk => $sv)
			{
				?><option value="<?php echo $sk; ?>"<?php echo $tpl['arr']['slot_length'] != $sk ? NULL : ' selected="selected"'; ?>><?php echo $sv; ?></option><?php
			}
			?>
			</select>
		</p>
		<p class="business" style="display: <?php echo $business; ?>">
			<label class="title"><?php __('lblNumberOfSlots');?></label>
			<span class="inline_block">
				<select name="slots" id="number_of_slots" class="pj-form-field w100">
				<?php
				foreach (range(1, 144) as $number) 
				{
    
					?><option value="<?php echo $number; ?>"<?php echo $number == $slots ? ' selected="selected"' : NULL;?>><?php echo $number; ?></option><?php
				}
				?>
				</select>
			</span>
		</p>
		<div id="tsCustomSlotBox">
			<p class="business" style="display: <?php echo $business; ?>">
				<label class="title"><?php __('time_tbl_break');?></label>
				<span class="block float_left">
					<input type="radio" id="lunch_break_yes" name="lunch_break" value="T"<?php echo $lunch_length == 0 ? NULL : ' checked=checked';?> class="tsLunchBreak float_left t5 r5" checked="checked"/>
					<label for="lunch_break_yes" class="block float_left t6 r5"><?php __('lblYes');?></label>
					<input type="radio" id="lunch_break_no" name="lunch_break" value="F"<?php echo $lunch_length > 0 ? NULL : ' checked=checked';?> class="tsLunchBreak float_left t5 r5"/>
					<label for="lunch_break_no" class="block float_left t6 r5"><?php __('lblNo');?></label>
				</span>
			</p>
			<p id="tsLunchBreakContainer" class="business" style="display: <?php echo $business == NULL ? ($lunch_length == 0 ? 'none' : 'block') : 'none';?>;">
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<label class="block float_left t6 r5"><?php __('lblFrom');?></label>
					<select name="lunch_from" id="lunch_from" class="pj-form-field w100 float_left">
						<?php
						foreach($lunch_from_arr as $k => $v)
						{
							?><option value="<?php echo $v; ?>"<?php echo date($tpl['option_arr']['o_time_format'], $lunch_from_ts) == $v ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
						}
						?>
					</select>
					<label class="block float_left t6 r5 l5"><?php __('lblLength');?></label>
					<input type="text" name="lunch_length" value="<?php echo ceil($lunch_length / 60);?>" class="pj-form-field field-int w80 float_left"/>
					<label class="block float_left t6 l5"><?php __('lblMinutes');?></label>
				</span>
			</p>
			<div id="tsCustomSlotsContainer">
				<div class="p business" style="display: <?php echo $business; ?>">
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
				<p class="business" style="display: <?php echo $business; ?>">
					<label class="title"><?php __('lblEndTime');?></label>
					<span class="inline_block">
						<input type="text" name="end" readonly="readonly" value="<?php echo $to;?>" class="pj-form-field w80"/>
					</span>
				</p>
			</div>	
		</div>
		<p class="business" style="display: <?php echo $business; ?>">
			<label class="title"><?php __('time_price'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" name="price" id="price" class="pj-form-field tsPriceField w70 align_right number" value="<?php echo number_format($tpl['arr']['price'], 2); ?>" />
			</span>
		</p>
		<p class="business" style="display: <?php echo $business; ?>">
			<label class="title">&nbsp;</label>
			<input type="checkbox" name="single_price" id="single_price" value="1"<?php echo !empty($tpl['price_arr']) ? NULL : ' checked="checked"'; ?> class="pps" />
			<label for="single_price"><?php __('time_single_price'); ?></label>
		</p>
		
		<div id="boxPPS" class="p business" style="display: <?php echo $business; ?>">
			<?php
			if (isset($tpl['price_arr']) && !empty($tpl['price_arr']))
			{
				?>
				<label class="title">&nbsp;</label>
				<table cellpadding="0" cellspacing="0" class="pj-table">
					<thead>
						<tr>
							<th><?php echo __('time_from'); ?></th>
							<th><?php echo __('time_to'); ?></th>
							<th><?php echo __('time_price'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					foreach($price_arr as $k => $v)
					{
						list($i, $istep) = explode("|", $k); 
						?>
						<tr>
							<td><?php echo date($tpl['option_arr']['o_time_format'], $i); ?></td>
							<td><?php echo date($tpl['option_arr']['o_time_format'], $istep); ?></td>
							<td>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" name="price[<?php echo $k;?>]" id="price_<?php echo $i; ?>" class="pj-form-field tsPriceField w70 align_right" value="<?php echo $v; ?>" />
								</span>
							</td>
						</tr>
						<?php
					}	
					?>
					</tbody>
				</table>
				<?php
			}
			?>
		</div>
		<p class="business" style="display: <?php echo $business; ?>">
			<label class="title"><?php __('time_limit'); ?></label>
			<input type="text" name="slot_limit" id="slot_limit" class="pj-form-field spin w60" value="<?php echo $tpl['arr']['slot_limit']; ?>" />
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
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	var myLabel = myLabel || {};
	myLabel.showperiod = <?php echo $show_period; ?>;
	</script>
	<?php
}
?>