<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{
	public $defaultForm = 'TSBC_Form';
	
	public $defaultCaptcha = 'TSBC_Captcha';
	
	public $defaultCart = 'TSBC_Cart';
	
	public $defaultLocale = 'TSBC_Locale';
	
	public $defaultSwitchLayout = 'TSBC_Switch_Layout';
	
	public $cart = NULL;
	
	public function __construct()
	{
		$this->setLayout('pjActionFront');
		
		if (!isset($_SESSION[$this->defaultCart]))
		{
			$_SESSION[$this->defaultCart] = array();
		}
		
		$this->cart = new pjCart($_SESSION[$this->defaultCart]);
		
		self::allowCORS();
	}
	
	public function afterFilter()
	{
		$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
			->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')->findAll()->getData();
		
		$calendars = pjCalendarModel::factory()
			->select('t1.*, t2.content AS `title`')
			->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
			->orderBy('t1.id ASC')
			->findAll()->getData();
		
		$calendar_cart = $this->getCart($this->getForeignId());
				
		$this->set('calendars', $calendars);
		$this->set('locale_arr', $locale_arr);
		$this->set('calendar_cart', $calendar_cart);
	}
	
	public function beforeFilter()
	{
		if(isset($_GET['cid']) && (int) $_GET['cid'] > 0)
		{
			$this->setForeignId($_GET['cid']);
		}else{
			$this->setForeignId(1);
		}
		
		$pjOptionModel = pjOptionModel::factory();
		$this->option_arr = $pjOptionModel->getPairs($this->getForeignId());
		$this->set('option_arr', $this->option_arr);
		$this->setTime();
		if (isset($_GET['locale']) && (int) $_GET['locale'] > 0)
		{
			$this->pjActionSetLocale($_GET['locale']);
		}
		
		if ($this->pjActionGetLocale() === FALSE)
		{
			$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
			if (count($locale_arr) === 1)
			{
				$this->pjActionSetLocale($locale_arr[0]['id']);
			}
		}
		$this->loadSetFields();
		
		if (!isset($_SESSION[$this->defaultForm]))
		{
			$_SESSION[$this->defaultForm] = array(
				'date_from' => NULL,
				'date_to' => NULL,
				'hour_from' => NULL,
				'hour_to' => NULL,
				'minute_from' => NULL,
				'minute_to' => NULL
			);
		}
	}

	public function beforeRender()
	{
		
	}
	
	protected function getCart($cid)
	{
		return $this->cart->getAll();
	}
	
	protected function getCalendar($cid, $year=null, $month=null, $day=null)
	{
		list($y, $n, $j) = explode("-", date("Y-n-j"));
		$year = is_null($year) ? $y : $year;
		$month = is_null($month) ? $n : $month;
		
		$pjWorkingTimeModel = pjWorkingTimeModel::factory();
		
		$pjTSBCalendar = new pjTSBCalendar();
		$daysoff = $pjWorkingTimeModel->getDaysOff($cid);
		$dateoff = pjDateModel::factory()->getDatesOff($cid, $month, $year);
		$monthStatus = pjAppController::getMonthStatus($cid, $month, $year);
		
		$days_arr = array();
		$dates = array();
		$days_in_month = $pjTSBCalendar->getDaysInMonth($month, $year);
		
		for($i = 1; $i <= $days_in_month; $i++)
		{
			$can_added = true;
			$timestamp = mktime(0, 0, 0, $month, $i, $year);
			$iso_date = date('Y-m-d', $timestamp);
			
			$date = getdate($timestamp);
			$weekday = strtolower($date['weekday']);
			
			if ($timestamp < strtotime(date('Y-m-d 00:00:00', time()))){
				$can_added = false;
			} elseif (isset($dateoff[$iso_date]) && isset($dateoff[$iso_date]['is_dayoff']) && $dateoff[$iso_date]['is_dayoff'] == 'T') {
				$can_added = false;
				
			} elseif (isset($daysoff[$weekday])) {
				$can_added = false;
			}
			if(isset($dateoff[$iso_date]) && isset($dateoff[$iso_date]['is_dayoff']) && $dateoff[$iso_date]['is_dayoff'] == 'F')
			{
				$can_added = true;
			}
			if($can_added == true)
			{
				$days_arr[$timestamp] = $iso_date;
			}
		}
		
		foreach($days_arr as $timestamp => $iso_date)
		{
			$dates[$timestamp] = $this->getTimeslots($cid, $iso_date);
		}
		
		$title_arr = array();
		$title_arr['selected'] = __('front_selected', true);
		$title_arr['available'] = __('front_available', true);
		$title_arr['booked'] = __('front_booked', true);
		$title_arr['past'] = __('front_cart_passed', true);
		$title_arr['lunch'] = __('front_cart_lunch', true);
		$title_arr['before'] = __('front_cart_before', true);
		$title_arr['dayoff'] = __('front_cart_dayoff', true);
		
		$pjTSBCalendar
			->setPrevLink("&nbsp;")
			->setNextLink("&nbsp;")
			->setStartDay($this->option_arr['o_week_start'])
			->setWeekNumbers($this->option_arr['o_show_week_numbers'] == 1 ? 'left' : NULL)
			->setDayNames(__('days_short', true))
			->setMonthNames(__('months', true))
			->setShowTooltip(true)
			->set('options', $this->option_arr)
			->set('dayoff', $daysoff)
			->set('dateoff', $dateoff)
			->set('monthStatus', $monthStatus)
			->set('dates', $dates)
			->set('cart', $this->cart->getAll())
			->set('titles', $title_arr);
		
		if (!is_null($day))
		{
			$pjTSBCalendar->setCurrentDate(mktime(0, 0, 0, $month, $day, $year));
		}
		
		return $pjTSBCalendar;
	}
	
	protected function getWeeklyTimeslots($cid, $iso_date)
	{
		$t_arr = pjAppController::getWeeklySlots($cid, $iso_date);
		
		$d_arr = array();
		# 0-6 (7 dates)
		# 0-7 (8 dates) In case the last day works 24h
		foreach (range(0,7) as $i)
		{
			$d_arr[] = date("Y-m-d", strtotime($iso_date) + 86400 * $i);
		}
		
		$bs_arr = pjBookingSlotModel::factory()
			->select()
			->join('pjBooking', sprintf("t2.id=t1.booking_id AND t2.calendar_id='%u' AND t2.booking_status IN ('pending', 'confirmed')", $cid), 'inner')
			->whereIn('t1.booking_date', $d_arr)
			->where('t2.booking_status !=', 'cancelled')
			->findAll()
			->getData();
			
		return compact('bs_arr', 't_arr');
	}
	
	protected function getTimeslots($cid, $iso_date)
	{
		$t_arr = pjAppController::getDailySlots($cid, $iso_date, $this->option_arr);
		if ($t_arr['is_dayoff'] == 'T')
		{
			# It's Day off
			return array('dayoff' => true);
		}
		
		$price_arr = pjAppController::getPricesDate($cid, $iso_date, $this->option_arr);

		$pjBookingSlotModel = pjBookingSlotModel::factory();
		
		# Get booked slots for given date.
		# If 24h, include next date
		$d_arr = array($pjBookingSlotModel->escapeStr($iso_date));
		if ($t_arr['end_ts'] < $t_arr['start_ts'])
		{
			$d_arr[] = date("Y-m-d", strtotime($iso_date) + 86400);
		}
		
		$bs_arr = $pjBookingSlotModel
			->select()
			->join('pjBooking', sprintf("t2.id=t1.booking_id AND t2.calendar_id='%u' AND t2.booking_status IN ('pending', 'confirmed')", $cid), 'inner')
			->whereIn('t1.booking_date', $d_arr)
			->where('t2.booking_status !=', 'cancelled')
			->findAll()
			->getData();
			
		return compact('price_arr', 'bs_arr', 't_arr');
	}
	
	protected function getTerms($cid)
	{
		return pjCalendarModel::factory()
			->select('t1.*, t2.content AS terms_url, t3.content AS terms_body')
			->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='terms_url' AND t2.locale='".$this->pjActionGetLocale()."'", 'left outer')
			->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t1.id AND t3.field='terms_body' AND t3.locale='".$this->pjActionGetLocale()."'", 'left outer')
			->find($cid)
			->getData();
	}
	
	protected function validateCart($cid)
	{
		if (!isset($_SESSION[$this->defaultCart]) || empty($_SESSION[$this->defaultCart]))
		{
			return array('status' => 'ERR', 'code' => 120, 'text' => 'Cart is empty');
		}
		$CART = $_SESSION[$this->defaultCart];
		if (!isset($CART[$cid]) || empty($CART[$cid]))
		{
			return array('status' => 'ERR', 'code' => 121, 'text' => 'Cart is empty');
		}

		$now = time();
		foreach ($CART[$cid] as $date => $items)
		{
			foreach ($items as $key => $qty)
			{
				list($start_ts,) = explode("|", $key);
				if ($start_ts < $now)
				{
					return array('status' => 'ERR', 'code' => 122, 'text' => 'Start Time is in past');
				} elseif ($start_ts < $now + $this->option_arr['o_hours_before'] * 3600) {
					return array('status' => 'ERR', 'code' => 123, 'text' => sprintf("Bookings are not allowed %u hour(s) before", $this->option_arr['o_hours_before']));
				}
			}
		}
		
		return array('status' => 'OK', 'code' => 220, 'text' => 'Cart is OK');
	}
	
	protected function validateCheckout($cid)
	{
		$FORM = $_SESSION[$this->defaultForm];
		
		$disablePayments = (int) $this->option_arr['o_disable_payments'] === 1;
		$hidePrices = (int) $this->option_arr['o_hide_prices'] === 1;
		
		$amount = pjAppController::getCartTotal($cid, $this->cart, $this->option_arr);
		
		$required = array();
		$prefixed_fields = array('name', 'email', 'phone', 'notes', 'country', 'state', 'city', 'zip', 'address_1', 'address_2');
		$simple_fields = array('captcha', 'terms');
		
		foreach ($prefixed_fields as $field)
		{
			if ((int) $this->option_arr['o_bf_' . $field] === 3)
			{
				$required[] = 'customer_' . $field;
			}
		}
		
		foreach ($simple_fields as $field)
		{
			if ((int) $this->option_arr['o_bf_' . $field] === 3)
			{
				$required[] = $field;
			}
		}
		if (!$disablePayments && !$hidePrices && $amount['price'] > 0 && $amount['deposit'] > 0)
		{
			$required[] = 'payment_method';
			if (isset($FORM['payment_method']) && $FORM['payment_method'] == 'creditcard')
			{
				$required = array_merge($required, array('cc_code', 'cc_num', 'cc_type', 'cc_exp_month', 'cc_exp_year'));
			}
		}
		
		# Check required fields
		$pjBookingModel = pjBookingModel::factory();
		if (!$pjBookingModel->validateRequest($required, $FORM))
		{
			return array('status' => 'ERR', 'code' => 100, 'text' => 'Checkout form not valid.');
		}
		
		# Check captcha
		if ((int) $this->option_arr['o_bf_captcha'] === 3 && (
			!isset($_SESSION[$this->defaultCaptcha]) || empty($_SESSION[$this->defaultCaptcha]) ||
			!pjCaptcha::validate($FORM['captcha'], $_SESSION[$this->defaultCaptcha])
		))
		{
			return array('status' => 'ERR', 'code' => 100, 'text' => 'Captcha does not match');
		}
		
		# Check CC details
		if (!$disablePayments && !$hidePrices && $amount['price'] > 0 && $FORM['payment_method'] == 'creditcard' && (
			empty($FORM['cc_code']) || empty($FORM['cc_num']) ||
			empty($FORM['cc_type']) || empty($FORM['cc_exp_month']) || empty($FORM['cc_exp_year'])
		))
		{
			return array('status' => 'ERR', 'code' => 100, 'text' => 'CC info not valid.');
		}
		
		# All check-ins passed
		return array('status' => 'OK', 'code' => 211, 'text' => 'Checkout form is OK');
	}
	
	private function pjActionSetLocale($locale)
	{
		if ((int) $locale > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $locale;
		}
		return $this;
	}
	
	public function pjActionGetLocale()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : FALSE;
	}
	
	public function isXHR()
	{
		// CORS
		return parent::isXHR() || isset($_SERVER['HTTP_ORIGIN']);
	}
	
	static protected function allowCORS()
	{
		$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
		header('P3P: CP="ALL DSP COR CUR ADM TAI OUR IND COM NAV INT"');
		header("Access-Control-Allow-Origin: $origin");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With");
	}
}
?>