<?php
$selected_day = $_POST['day_of_week'];
$start_ts = strtotime($_POST[$selected_day . '_from']);
$i = $start_ts;
$lunch_from_ts = strtotime($_POST[$selected_day . '_lunch_from']);
$lunch_length = $_POST['lunch_length'] != '' ? intval($_POST['lunch_length']) : 0;
if($lunch_from_ts < $i)
{
	$lunch_from_ts = $lunch_from_ts + (24 * 60 * 60);
}
$lunch_to = $lunch_from_ts + ($lunch_length * 60);

$step = $_POST[$selected_day . '_length'] * 60;
$slots = $_POST[$selected_day . '_slots'];
$slot_arr = array();

$after_midnight = false;
for($n = 1; $n <= $slots; $n++)
{
	if($i+$step > strtotime(date('Y-m-d 00:00:00', $start_ts + (24 * 60 * 60))))
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