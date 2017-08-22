<?php
$date = pjUtil::formatDate($_POST['date'], $tpl['option_arr']['o_date_format']);
$step = $_POST['slot_length'] * 60;

$slots = $_POST['slots'];

$after_midnight = false;

$lunch_from_arr = array();
$slot_arr = array();
$from = strtotime($date . ' ' . $_POST['start']);
$i = $from;
for($n = 1; $n <= $slots; $n++)
{
	if($i+$step > strtotime(date('Y-m-d 00:00:00', $from + (24 * 60 * 60))))
	{
		$after_midnight = true;
	}
	$lunch_from_arr[date('H:i', ($i + $step))] = date($tpl['option_arr']['o_time_format'], $i + $step);
	$slot_arr[$i + $step] = date($tpl['option_arr']['o_time_format'], $i) . ' - ' . date($tpl['option_arr']['o_time_format'], $i + $step);
	$i = $i + $step;
}
$lunch_length = 0;
?>
<p class="business">
	<label class="title"><?php __('time_tbl_break');?></label>
	<span class="block float_left">
		<input type="radio" id="lunch_break_yes" name="lunch_break" value="T"class="tsLunchBreak float_left t5 r5" checked="checked"/>
		<label for="lunch_break_yes" class="block float_left t6 r5"><?php __('lblYes');?></label>
		<input type="radio" id="lunch_break_no" name="lunch_break" value="F" class="tsLunchBreak float_left t5 r5"/>
		<label for="lunch_break_no" class="block float_left t6 r5"><?php __('lblNo');?></label>
	</span>
</p>
<p id="tsLunchBreakContainer" class="business">
	<label class="title">&nbsp;</label>
	<span class="inline_block">
		<label class="block float_left t6 r5"><?php __('lblFrom');?></label>
		<select name="lunch_from" id="lunch_from" class="pj-form-field w100 float_left">
			<?php
			foreach($lunch_from_arr as $k => $v)
			{
				?><option value="<?php echo $v; ?>"><?php echo $v; ?></option><?php
			}
			?>
		</select>
		<label class="block float_left t6 r5 l5"><?php __('lblLength');?></label>
		<input type="text" name="lunch_length" class="pj-form-field field-int w80 float_left"/>
		<label class="block float_left t6 l5"><?php __('lblMinutes');?></label>
	</span>
</p>
<div id="tsCustomSlotsContainer">
	<div class="p business">
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
	<p class="business">
		<label class="title"><?php __('lblEndTime');?></label>
		<span class="inline_block">
			<input type="text" name="end" readonly="readonly" value="<?php echo $to;?>" class="pj-form-field w80"/>
		</span>
	</p>
</div>