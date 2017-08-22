<?php
if (!isset($tpl['t_arr']) || !isset($tpl['arr']))
{
	exit;
}

$start_time = $end_time = NULL;
foreach ($tpl['t_arr'] as $date => $wtime)
{
	if($wtime['is_dayoff'] == 'T')
	{
		continue;
	}
	$start = strtotime($wtime['start_hour'].":".$wtime['start_minutes'].":00");
	if(($wtime['start_ts'] == $wtime['end_ts'] || date('H:i:s',$wtime['end_ts']) == '00:00:00') && $wtime['slots'] > 0)
	{
		$end = strtotime(date('H:i:s',$wtime['end_ts'])) + 86400 - ($wtime['slot_length'] * 60);
	}else{
		$end = strtotime(date('H:i:s',$wtime['end_ts']));
		
	}

	if (is_null($start_time) || $start < $start_time)
	{
		$start_time = $start;
	}
	
	if (is_null($end_time) || $end > $end_time)
	{
		$end_time = $end;
	}
}

list($start_year, $start_month, $start_day, $start_hour) = explode("-", date("Y-n-j-H", $start_time));
list($end_year, $end_month, $end_day, $end_hour) = explode("-", date("Y-n-j-H", $end_time));

$start_time = mktime($start_hour, 0, 0, $start_month, $start_day, $start_year);
$end_time = mktime($end_hour, 0, 0, $end_month, $end_day, $end_year);

$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
?>

<div class="schedule_nav" style="display: <?php echo $_GET['action'] == 'pjActionSchedulePrint' ? 'none' : NULL; ?>">
	<span class="pj-form-field-custom pj-form-field-custom-after float_left">
		<input type="text" name="schedule_date" class="pj-form-field w80 datepick pointer required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo pjUtil::formatDate($_GET['date'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
		<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
	</span>
	&nbsp;
	<select class="pj-form-field w200" id="filter_calendar_id" name="calendar_id" data-iso="<?php echo $_GET['date']; ?>">
		<option value="">-- <?php __('lblAllCalendars')?> --</option>
		<?php
		foreach ($tpl['calendars'] as $calendar)
		{
			?><option value="<?php echo $calendar['id']; ?>"<?php echo $calendar['id'] == $_GET['calendar_id'] ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html($calendar['title']); ?></option><?php
		}
		?>
	</select>
	<span class="float_right t10">
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionSchedulePrint&amp;date=<?php echo $_GET['date']; ?>&amp;calendar_id=<?php echo $_GET['calendar_id']; ?>" target="_blank"><?php __('booking_schedule_print'); ?></a> |
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionSchedule" data-iso="<?php echo date("Y-m-d", strtotime("-5 days", strtotime($_GET['date']))); ?>" class="schedule_get"><?php __('booking_schedule_prev'); ?></a> |
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionSchedule" data-iso="<?php echo date("Y-m-d", strtotime("+5 days", strtotime($_GET['date']))); ?>" class="schedule_get"><?php __('booking_schedule_next'); ?></a>
	</span>
	<br class="clear_both" />
</div>

<table cellpadding="0" cellspacing="0" class="pj-table" style="width: 100%">
	<thead>
		<tr>
			<th class="schedule_time">&nbsp;</th>
			<?php
			$days = __('days', true);
			foreach ($tpl['arr'] as $date => $bookings)
			{
				$time = strtotime($date);
				$w = date("w", $time);
				$weekday = @$days[$w];
				if ($tpl['t_arr'][$date]['is_dayoff'] == 'T')
				{
					$weekday = sprintf("%s (%s)", $weekday, __('front_cart_dayoff', true));
				}
				?><th class="schedule_date"><?php echo date($tpl['option_arr']['o_date_format'], $time); ?><br /><?php echo $weekday; ?></th><?php
			}
			?>
		</tr>
	</thead>
	<tbody>
	<?php
	for ($i = $start_time; $i <= $end_time; $i += 3600)
	{
		list($y, $m, $d, $hh, $mm) = explode("-", date("Y-n-j-H-i", $i));
		
		?>
		<tr>
			<td><?php echo date($tpl['option_arr']['o_time_format'], $i); ?></td>
			<?php
			foreach ($tpl['arr'] as $date => $bookings)
			{
				
				$class = "available";
				$add_slot = true;
				$isDayOff = false;
				$break = false;
				if ($tpl['t_arr'][$date]['is_dayoff'] == 'T')
				{
					$isDayOff = true;
					$class = "schedule_dayoff";
				}
				list($y, $m, $d) = explode("-", $date);
				
				$time_from = mktime($hh, $mm, 0, $m, $d, $y);
				$time_to = mktime($hh + 1, $mm, 0, $m, $d, $y);
				
				$start_ts = mktime($tpl['t_arr'][$date]['start_hour'], 0, 0, $m, $d, $y);
				$end_ts = mktime($tpl['t_arr'][$date]['end_hour'], 0, 0, $m, $d, $y);
				
				$slot_length_in_seconds = $tpl['t_arr'][$date]['slot_length'] * 60;
				$start_of_last_slot = $tpl['t_arr'][$date]['end_ts'] - $slot_length_in_seconds;
				
				if (!$isDayOff && ($start_of_last_slot >= $time_to && $i == $end_time))	
				{
					$class = "schedule_na";
				}
				
				if(!$isDayOff && $i > $start_time)
				{
					if( ($time_from >= $tpl['t_arr'][$date]['lunch_start_ts'] && $time_from < $tpl['t_arr'][$date]['lunch_end_ts']) || 
						($time_to > $tpl['t_arr'][$date]['lunch_start_ts'] && $time_to <= $tpl['t_arr'][$date]['lunch_end_ts']))
					{
						$class = "schedule_na";
						$break = true;
					}
				}
				?><td class="<?php echo $class; ?>"><?php 
				foreach ($bookings as $booking)
				{
					if ($time_from < $booking['end_ts'] && $time_to > $booking['start_ts'])
					{
						?><div class="b5"><?php
						if ($_GET['action'] != 'pjActionSchedulePrint')
						{
							echo pjSanitize::html($booking['customer_name']); ?><br /><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $booking['booking_id']; ?>"><?php printf("%s - %s", date($tpl['option_arr']['o_time_format'], $booking['start_ts']), date($tpl['option_arr']['o_time_format'], $booking['end_ts'])); ?></a><br/><?php echo $booking['calendar_title']?><?php
						} else {
							printf("%s<br>%s - %s", $booking['customer_name'], date($tpl['option_arr']['o_time_format'], $booking['start_ts']), date($tpl['option_arr']['o_time_format'], $booking['end_ts']));
						}
						?></div><?php
						$add_slot = false;
					}
				}
				if($break == true)
				{
					__('time_tbl_break');
				}
				if($class == "available" && $add_slot == true)
				{
					?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate&date=<?php echo date($tpl['option_arr']['o_date_format'], strtotime($date)); ?>&amp;start_ts=<?php echo $time_from?>&amp;end_ts=<?php echo $time_to;?>" class="pj-table-icon-add"></a><?php
				}
				?></td><?php
			}
			?>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>