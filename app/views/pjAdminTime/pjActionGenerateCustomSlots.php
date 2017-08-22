<?php
$date = pjUtil::formatDate($_POST['date'], $tpl['option_arr']['o_date_format']);
$step = $_POST['slot_length'] * 60;

$slots = $_POST['slots'];
$start_ts = strtotime($date . ' ' . $_POST['start']);
$i = $start_ts;
$lunch_from_ts = strtotime($date . ' ' . $_POST['lunch_from']);
$lunch_length = $_POST['lunch_length'] != '' ? intval($_POST['lunch_length']) : 0;
if($lunch_from_ts < $i)
{
	$lunch_from_ts = $lunch_from_ts + (24 * 60 * 60);
}
$lunch_to = $lunch_from_ts + ($lunch_length * 60);

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