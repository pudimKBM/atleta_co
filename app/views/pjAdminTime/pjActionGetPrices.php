<?php
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
?>
<form action="" method="post" class="form pj-form">
	<input type="hidden" name="day" value="<?php echo $_GET['day']; ?>" />
	<table cellpadding="0" cellspacing="0" class="pj-table" style="width: 100%">
		<thead>
			<tr>
				<th colspan="3"><?php echo @$w_days[$_GET['day']];?></th>
			</tr>
			<tr>
				<th><?php echo __('time_from'); ?></th>
				<th><?php echo __('time_to'); ?></th>
				<th><?php echo __('time_price'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		#$step = $tpl['option_arr']['slot_length'] * 60;
		$step = $tpl['wt_arr'][$_GET['day'] . '_length'] * 60;
		$start_ts = strtotime($tpl['wt_arr'][$_GET['day'] . '_from']);
		$end_ts = strtotime($tpl['wt_arr'][$_GET['day'] . '_to']);
		
		$slots = $tpl['wt_arr'][$_GET['day'].'_slots'];
		
		$i = strtotime($tpl['wt_arr'][$_GET['day'] . '_from']);
		$lunch_from_ts = strtotime($tpl['wt_arr'][$_GET['day'].'_lunch_from']);
		$lunch_to_ts = strtotime($tpl['wt_arr'][$_GET['day'].'_lunch_to']);
		if($lunch_from_ts < $i)
		{
			$lunch_from_ts = $lunch_from_ts + (24 * 60 * 60);
			$lunch_to_ts = $lunch_to_ts + (24 * 60 * 60);
		}
		
		for($n = 1; $n <= $slots; $n++)
		{
			$price = NULL;
			foreach ($tpl['price_day_arr'] as $k => $v)
			{
				#if (strtotime($v['start_time']) == $i && strtotime($v['end_time']) == $i + $step)
				if ($v['start_time'] == date("H:i:s", $i) && $v['end_time'] == date("H:i:s", $i + $step))
				{
					$price = $v['price'];
					break;
				}
			}
			?>
			<tr>
				<td><?php echo date($tpl['option_arr']['o_time_format'], $i); ?></td>
				<td><?php echo date($tpl['option_arr']['o_time_format'], $i + $step); ?></td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="price[<?php echo $i; ?>|<?php echo $i + $step; ?>]" id="price_<?php echo $i; ?>" class="pj-form-field tsPriceField w70 align_right" value="<?php echo $price; ?>" />
					</span>
				 </td>
			</tr>
			<?php
			if($i + $step == $lunch_from_ts)
			{
				if($lunch_from_ts < $lunch_to_ts)
				{
					?>
					<tr>
						<td><?php echo date($tpl['option_arr']['o_time_format'], $lunch_from_ts); ?></td>
						<td><?php echo date($tpl['option_arr']['o_time_format'], $lunch_to_ts); ?></td>
						<td>
							<?php __('time_tbl_break');?>
						 </td>
					</tr>
					<?php
				}
				$i = $lunch_to_ts;
			}else{
				$i = $i + $step;
			}
				
		}
		?>
		</tbody>
	</table>
</form>