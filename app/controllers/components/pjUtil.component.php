<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjUtil extends pjToolkit
{
	static public function getClientIp()
	{
		if (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			return $_SERVER['HTTP_CLIENT_IP'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
			return $_SERVER['HTTP_X_FORWARDED'];
		} else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_FORWARDED_FOR'];
		} else if(isset($_SERVER['HTTP_FORWARDED'])) {
			return $_SERVER['HTTP_FORWARDED'];
		} else if(isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		}

		return 'UNKNOWN';
	}
	
	static public function getTimezone($offset)
	{
		$db = array(
			'-14400' => 'America/Porto_Acre',
			'-18000' => 'America/Porto_Acre',
			'-7200' => 'America/Goose_Bay',
			'-10800' => 'America/Halifax',
			'14400' => 'Asia/Baghdad',
			'-32400' => 'America/Anchorage',
			'-36000' => 'America/Anchorage',
			'-28800' => 'America/Anchorage',
			'21600' => 'Asia/Aqtobe',
			'18000' => 'Asia/Aqtobe',
			'25200' => 'Asia/Almaty',
			'10800' => 'Asia/Yerevan',
			'43200' => 'Asia/Anadyr',
			'46800' => 'Asia/Anadyr',
			'39600' => 'Asia/Anadyr',
			'0' => 'Atlantic/Azores',
			'-3600' => 'Atlantic/Azores',
			'7200' => 'Europe/London',
			'28800' => 'Asia/Brunei',
			'3600' => 'Europe/London',
			'-39600' => 'America/Adak',
			'32400' => 'Asia/Shanghai',
			'36000' => 'Asia/Choibalsan',
			'-21600' => 'America/Chicago',
			'-25200' => 'Chile/EasterIsland',
			'-43200' => 'Pacific/Kwajalein'
		);
		if (is_null($offset) && strlen($offset) === 0)
		{
			return $db;
		}
		return array_key_exists($offset, $db) ? $db[$offset] : false;
	}

	static public function formatPhone($value)
	{
		$value = trim($value);
		$value = preg_replace('/^\+/', '00', $value);
		$value = preg_replace('/\D+/', '', $value);
		
		return $value;
	}
	
	static public function getCommonOptions()
	{
		return array('o_currency','o_time_format','o_date_format','o_datetime_format','o_week_start','o_timezone','o_send_email','o_smtp_host','o_smtp_pass','o_smtp_port', 'o_smtp_user', 'o_from_email', 'o_theme');
	}
	
	static public function textToHtml($content)
	{
		$content = preg_replace('/\r\n|\n/', '<br />', $content);
		return '<html><head><title></title></head><body>'.$content.'</body></html>';
	}
	
	static public function uuid()
	{
		return chr(rand(65,90)) . chr(rand(65,90)) . time();
	}
	
	static public function getWeekRange($date, $week_start)
	{
		$week_arr = array(
				0=>'sunday',
				1=>'monday',
				2=>'tuesday',
				3=>'wednesday',
				4=>'thursday',
				5=>'friday',
				6=>'saturday');
			
		$ts = strtotime($date);
		$start = (date('w', $ts) == $week_start) ? $ts : strtotime('last ' . $week_arr[$week_start], $ts);
		$week_start = ($week_start == $week_start ? 6 : $week_start - 1);
		return array(date('Y-m-d', $start), date('Y-m-d', strtotime('next ' . $week_arr[$week_start], $start)));
	}
	
	static public function getComingWhere($period, $week_start)
	{
		$where_str = '';
		switch ($period) {
			case 1:
				$where_str = "(CURDATE() = t2.booking_date)";
				break;
				;
			case 2:
				$where_str = "(DATE(DATE_ADD(NOW(), INTERVAL 1 DAY)) = t2.booking_date)";
				break;
				;
			case 3:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d'), $week_start);
				$where_str = "(t2.booking_date BETWEEN CURDATE() AND '$end_week')";
				break;
				;
			case 4:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d', strtotime("+7 days")), $week_start);
				$where_str = "(t2.booking_date BETWEEN '$start_week' AND '$end_week')";
				break;
				;
			case 5:
				$end_month = date('Y-m-t',strtotime('this month'));
				$where_str = "(t2.booking_date BETWEEN CURDATE() AND '$end_month')";
				break;
				;
			case 6:
				$start_month = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, 1, date("Y")));
				$end_month = date("Y-m-d", mktime(0, 0, 0, date("m") + 2, 0, date("Y")));
				$where_str = "(t2.booking_date BETWEEN '$start_month' AND '$end_month')";
				break;
				;
		}
		return $where_str;
	}
	
	static public function getMadeWhere($period, $week_start)
	{
		$where_str = '';
		switch ($period) {
			case 1:
				$where_str = "(DATE(t1.created) = CURDATE() OR DATE(t1.modified) = CURDATE())";
				break;
				;
			case 2:
				$where_str = "(DATE(t1.created) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) OR DATE(t1.modified) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)))";
				break;
				;
			case 3:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d'), $week_start);
				$where_str = "((DATE(t1.created) BETWEEN '$start_week' AND '$end_week') OR (DATE(t1.modified) BETWEEN '$start_week' AND '$end_week'))";
				break;
				;
			case 4:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d', strtotime("-7 days")), $week_start);
				$where_str = "((DATE(t1.created) BETWEEN '$start_week' AND '$end_week') OR (DATE(t1.modified) BETWEEN '$start_week' AND '$end_week'))";
				break;
				;
			case 5:
				$start_month = date('Y-m-01',strtotime('this month'));
				$end_month = date('Y-m-t',strtotime('this month'));
				$where_str = "((DATE(t1.created) BETWEEN '$start_month' AND '$end_month') OR (DATE(t1.modified) BETWEEN '$start_month' AND '$end_month'))";
				break;
				;
			case 6:
				$start_month = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1, date("Y")));
				$end_month = date("Y-m-d", mktime(0, 0, 0, date("m"), 0, date("Y")));
				$where_str = "((DATE(t1.created) BETWEEN '$start_month' AND '$end_month') OR (DATE(t1.modified) BETWEEN '$start_month' AND '$end_month'))";
				break;
				;
		}
		return $where_str;
	}
	
	static public function getTimezoneName($timezone)
	{
		$offset = $timezone / 3600;
		$timezone_name = timezone_name_from_abbr(null, $offset * 3600, true);
		if($timezone_name === false)
		{
			$timezone_name = timezone_name_from_abbr(null, $offset * 3600, false);
		}
		if($offset == -12)
		{
			$timezone_name = 'Pacific/Wake';
		}
		return $timezone_name;
	}
}
?>