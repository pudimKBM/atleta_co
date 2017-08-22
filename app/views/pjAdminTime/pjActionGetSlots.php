<?php
$required = array('slot_length', 'date', 'start', 'end');
foreach ($required as $index)
{
	if (!array_key_exists($index, $_POST))
	{
		pjUtil::printNotice('Missing params', 'Prices per slot are unable to show up due missing parameters.');
		exit;
	}
}

$date = pjUtil::formatDate($_POST['date'], $tpl['option_arr']['o_date_format']);
$step = $_POST['slot_length'] * 60;

$slots = $_POST['slots'];
$i = strtotime($date . ' ' . $_POST['start']);
$lunch_from_ts = strtotime($date . ' ' . $_POST['lunch_from']);
$lunch_length = $_POST['lunch_length'] != '' ? intval($_POST['lunch_length']) : 0;
if($lunch_from_ts < $i)
{
	$lunch_from_ts = $lunch_from_ts + (24 * 60 * 60);
}
$lunch_to = $lunch_from_ts + ($lunch_length * 60);

?>
<label class="title">&nbsp;</label>
<table cellpadding="0" cellspacing="0" class="pj-table">
	<thead>
		<tr>
			<th><?php __('time_from'); ?></th>
			<th><?php __('time_to'); ?></th>
			<th><?php __('time_price'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	for($n = 1; $n <= $slots; $n++)
	{
		?>
		<tr>
			<td><?php echo date($tpl['option_arr']['o_time_format'], $i); ?></td>
			<td><?php echo date($tpl['option_arr']['o_time_format'], $i + $step); ?></td>
			<td>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="price[<?php echo $i; ?>|<?php echo $i + $step; ?>]" id="price_<?php echo $i; ?>" class="pj-form-field tsPriceField w70 align_right" />
				</span>
			</td>
		</tr>
		<?php
		if($i + $step == $lunch_from_ts)
		{
			?>
			<tr>
				<td><?php echo date($tpl['option_arr']['o_time_format'], $lunch_from_ts); ?></td>
				<td><?php echo date($tpl['option_arr']['o_time_format'], $lunch_to); ?></td>
				<td>
					<?php __('time_tbl_break');?>
				</td>
			</tr>
			<?php
			$i = $lunch_to;
		}else{
			$i = $i + $step;
		}
	}
	?>
	</tbody>
</table>