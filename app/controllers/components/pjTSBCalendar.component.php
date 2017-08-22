<?php
class pjTSBCalendar extends pjBaseCalendar
{
	private $dayoff = array();
	
	private $dateoff = array();
	
	private $monthStatus = array();
	
	public function __construct()
	{
		parent::__construct();
		
		$this->classMonthPrev = "pjTsCalendarLinkMonth";
		$this->classMonthNext = "pjTsCalendarLinkMonth";
		$this->classCalendar = "pjTsCalendarDate pjTsTooltipster";
		
		$this->classPartly = "pjTsCalendarPartly";
		$this->classFully = "pjTsCalendarFully";
		
		$this->classPast = "pj-calendar-day-past";
		$this->classToday = "pj-calendar-day-today";
		$this->classReserved = "pj-calendar-day-inactive";
		$this->classDayoff = "pj-calendar-day-inactive pjTsTooltipster";
		$this->classSelected = "pj-calendar-day-selected";
		$this->classEmpty = "pj-calendar-day-disabled";
	}
	
	public function getMonthView($month, $year)
    {
        return $this->getMonthHTML($month, $year, 1);
    }
    
	public function get($key)
	{
		if (isset($this->$key))
		{
			return $this->$key;
		}
		return FALSE;
	}
	
	public function set($key, $value)
	{
		if (in_array($key, array('calendarId', 'weekNumbers', 'options', 'dayoff', 'dateoff', 'monthStatus', 'dates', 'cart', 'titles')))
		{
			$this->$key = $value;
		}

		return $this;
	}
	
	private function getClass($status)
	{
		$class = $this->classCalendar;
	
		switch ($status)
		{
			case 'partly':
				$class = $this->classCalendar ." ". $this->classPartly;
				break;
			case 'fully':
				$class = $this->classFully;
				break;
			case 'available':
			default:
				$class = $this->classCalendar;
				break;
		}
	
		return $class;
	}
	public function onShowTooltip($timestamp)
	{
		$tooltip = '';
		
		if(isset($this->dates[$timestamp]))
		{
			$date = $this->dates[$timestamp];
			if($date['t_arr']['is_dayoff'] == 'F')
			{	
			
				$iso_date = date('Y-m-d', $timestamp);
				
				$hidePrices = (int) $this->options['o_hide_prices'] === 1;
				$step = $date['t_arr']['slot_length'] * 60;
				# Fix for 24h support
				$offset = $date['t_arr']['end_ts'] <= $date['t_arr']['start_ts'] ? 86400 : 0;
				$now = time();
				$total = 0;
				if (!$hidePrices)
				{
					for ($i = $date['t_arr']['start_ts']; $i < $date['t_arr']['end_ts'] + $offset; $i += $step)
					{
						if ((float) @$date['price_arr'][$i . "|" . ($i + $step)] > 0)
						{
							$total += @$date['price_arr'][$i . "|" . ($i + $step)];
						}
					}
				}
				
				$CART = $this->cart;
				
				$tooltip .= '<div class="pj-calendar-tooltip">';
				$tooltip .= '<ul>';
				
				$slots = $date['t_arr']['slots'];
				$i = $date['t_arr']['start_ts'];
				$lunch_from_ts = $date['t_arr']['lunch_start_ts'];
				$lunch_to_ts = $date['t_arr']['lunch_end_ts'];
				if($lunch_from_ts < $i)
				{
					$lunch_from_ts = $lunch_from_ts + (24 * 60 * 60);
					$lunch_to_ts = $lunch_to_ts + (24 * 60 * 60);
				}
				
				for($n = 1; $n <= $slots; $n++)
				{
					$booked = 0;
					foreach ($date['bs_arr'] as $bs)
					{
						if ($bs['start_ts'] == $i && $bs['end_ts'] == $i + $step)
						{
							$booked++;
						}
					}
					$attr = NULL;
					$tooltip_class = 'tooltip-past';
					if ($i < $now)
					{
						# Start Time is in past
						$state = 4;
						$class = "past-item";
						$tooltip_class = 'tooltip-past';
					} elseif ($i < $now + $this->options['o_hours_before'] * 3600) {
						# Bookings are not allowed X hours before
						$state = 6;
						$class = "past-item";
						$tooltip_class = 'tooltip-past';
					} else {
						if ($booked < $date['t_arr']['slot_limit'])
						{
							$checked = NULL;
							if (isset($CART[$_GET['cid']][$iso_date][$i . "|" . ($i + $step)]))
							{
								# In basket
								$state = 1;
								$tooltip_class = 'tooltip-available';
							} else {
								# Available
								$state = 2;
								$tooltip_class = 'tooltip-available';
							}
						} else {
							# Fully booked
							$state = 3;
							$tooltip_class = 'tooltip-booked';
						}
					}
					$slot_text = '';
					switch ($state)
					{
						case 1:
							# In basket
							$slot_text = $this->titles['selected'];
							break;
						case 2:
							# Available
							$slot_text = $this->titles['available'];
							break;
						case 3:
							# Fully booked
							$slot_text = $this->titles['booked'];
							break;
						case 4:
							# Past
							$slot_text = $this->titles['past'];
							break;
						case 5:
							# Lunch break
							$slot_text = $this->titles['lunch'];
							break;
						case 6:
							# Bookings are not allowed X hours before
							$slot_text = $this->titles['before'];
							break;
					}
					$price = '&nbsp;';
					if (!$hidePrices)
					{
						if (!in_array($state, array(3,4,5,6)))
						{
							if (isset($date['price_arr'][$i . "|" . ($i + $step)]))
							{
								$price = pjUtil::formatCurrencySign(number_format(@$date['price_arr'][$i . "|" . ($i + $step)], 2, '.', ','), $this->options['o_currency']);
							} else {
								$price = pjUtil::formatCurrencySign(0.00, $this->options['o_currency']);
							}
						}
					}
					$tooltip .= '<li><span class="'.$tooltip_class.'">'.date($this->options['o_time_format'], $i).' - '.date($this->options['o_time_format'], $i+ $step).'</span> <strong class="'.$tooltip_class.'">'.$slot_text.'</strong> <strong class="'.$tooltip_class.'">'.$price.'</strong></li>';
	
					if($i + $step == $lunch_from_ts)
					{
						if($lunch_from_ts < $lunch_to_ts)
						{
							$tooltip .= '<li><span class="tooltip-past">'.date($this->options['o_time_format'], $lunch_from_ts).' - '.date($this->options['o_time_format'], $lunch_to_ts).'</span> <strong class="tooltip-past">'. $this->titles['lunch'].'</strong> <strong class="tooltip-past">&nbsp;</strong></li>';
						}
						$i = $lunch_to_ts;
					}else{
						$i = $i + $step;
					}
				}
				
				$tooltip .= '</ul>';
				$tooltip .= '</div>';
			}
		}else{
			$iso_date = date('Y-m-d', $timestamp);
			if(isset($this->dateoff[$iso_date]))
			{
				$tooltip .= '<div class="pj-calendar-tooltip dayoff"><label>' . $this->titles['dayoff'] . '</label></div>';
			}
		}
		return $tooltip;
	}
	public function onBeforeShow($timestamp, $iso, $today, $current, $year, $month, $d)
	{
		$date = getdate($timestamp);
		$weekday = strtolower($date['weekday']);
		
		if ($timestamp < strtotime(date('Y-m-d 00:00:00', $today[0]))){
			$class = $this->classPast;
		} elseif (isset($this->dates[$timestamp])) {
			if(isset($this->dateoff[$iso]['is_dayoff']))
			{
				switch ($this->dateoff[$iso]['is_dayoff'])
				{
					case 'T':
						$class = $this->classDayoff;
						break;
					case 'F':
					default:
						$class = $this->getClass($this->monthStatus[$iso]['text']);
						break;
				}
			}else{
				$class = $this->getClass($this->monthStatus[$iso]['text']);
			}
		} elseif (isset($this->dayoff[$weekday])) {
			$class = $this->classDayoff;
		} else {
			if(isset($this->dateoff[$iso]['is_dayoff']))
			{
				if($this->dateoff[$iso]['is_dayoff'] == 'F')
				{
					$class = $this->getClass($this->monthStatus[$iso]['text']);
				}else{
					$class = $this->classDayoff;
				}
			}else{
				$class = $this->getClass($this->monthStatus[$iso]['text']);
			}
		}
		
		if ($class == $this->classCalendar)
		{
			if ($year == $today["year"] && $month == $today["mon"] && $d == $today["mday"])
			{
				$class .= " " . $this->classToday;
			}
		}
		return $class;
	}
}
?>