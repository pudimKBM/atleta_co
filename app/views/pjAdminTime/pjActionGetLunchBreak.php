<?php
$selected_day = $_POST['day_of_week'];
$from = strtotime($_POST[$selected_day . '_from']);
$lunch_from_ts = strtotime($_POST[$selected_day . '_lunch_from']);
$lunch_length = $_POST['lunch_length'] != '' ? intval($_POST['lunch_length']) : 0;
if($lunch_from_ts < $from)
{
	$lunch_from_ts = $lunch_from_ts + (24 * 60 * 60);
}
$lunch_to = $lunch_from_ts + ($lunch_length * 60);

$lunch_from_arr = array();
$step = $_POST[$selected_day . '_length'] * 60;
$slots = $_POST[$selected_day . '_slots'];
$i = $from;
for($n = 1; $n <= $slots; $n++)
{
	$lunch_from_arr[date('H:i', ($i + $step))] = date($tpl['option_arr']['o_time_format'], $i + $step);
	$i = $i + $step;
}

$after_midnight = false;
$slot_arr = array();
$i = $from;

for($n = 1; $n <= $slots; $n++)
{
	if($i+$step > strtotime(date('Y-m-d 00:00:00', $from + (24 * 60 * 60))))
	{
		$after_midnight = true;
	}
	$slot_arr[$i + $step] = date($tpl['option_arr']['o_time_format'], $i) . ' - ' . date($tpl['option_arr']['o_time_format'], $i + $step);
	if($i + $step == $lunch_from_ts)
	{
		$i = $lunch_to;
	}else{
		$i = $i + $step;
	}
}
?>
<p class="tsWorkingDay_<?php echo $selected_day;?>">
	<label class="title"><?php __('time_tbl_break');?></label>
	<span class="block float_left">
		<input type="radio" id="lunch_break_yes" name="lunch_break" value="T"<?php echo $lunch_length == 0 ? NULL : ' checked=checked';?> class="tsLunchBreak float_left t5 r5"/>
		<label for="<?php echo $selected_day?>_lunch_break" class="block float_left t6 r5"><?php __('lblYes');?></label>
		<input type="radio" id="lunch_break_no" name="lunch_break" value="F"<?php echo $lunch_length > 0 ? NULL : ' checked=checked';?> class="tsLunchBreak float_left t5 r5"/>
		<label for="lunch_break_no" class="block float_left t6 r5"><?php __('lblNo');?></label>
	</span>
</p>
<p id="tsLunchBreakContainer" class="tsWorkingDay_<?php echo $selected_day;?>" style="display: <?php echo $lunch_length == 0 ? 'none' : 'block';?>;">
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
	<div class="p tsWorkingDay_<?php echo $selected_day;?>">
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
	<p class="tsWorkingDay_<?php echo $selected_day;?>">
		<label class="title"><?php __('lblEndTime');?></label>
		<span class="inline_block">
			<input type="text" name="<?php echo $selected_day?>_to" readonly="readonly" value="<?php echo $to;?>" class="pj-form-field w80"/>
		</span>
	</p>
</div><!-- /.tsSlotsContainer -->