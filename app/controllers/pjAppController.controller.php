<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAppController extends pjController
{
	public $models = array();

	public $defaultLocale = 'admin_locale_id';
	
	public $defaultCalendarId = 'admin_calendar_id';
	
	private $layoutRange = array(1, 2);
	
	public $defaultFields = 'fields';
	
	public $defaultFieldsIndex = 'fields_index';
	
	protected function loadSetFields($force=FALSE, $locale_id=NULL, $fields=NULL)
	{
		if (is_null($locale_id))
		{
			$locale_id = $this->getLocaleId();
		}
		
		if (is_null($fields))
		{
			$fields = $this->defaultFields;
		}
		
		$registry = pjRegistry::getInstance();
		if ($force
				|| !isset($_SESSION[$this->defaultFieldsIndex])
				|| $_SESSION[$this->defaultFieldsIndex] != $this->option_arr['o_fields_index']
				|| !isset($_SESSION[$fields])
				|| empty($_SESSION[$fields]))
		{
			pjAppController::setFields($locale_id);
	
			# Update session
			if ($registry->is('fields'))
			{
				$_SESSION[$fields] = $registry->get('fields');
			}
			$_SESSION[$this->defaultFieldsIndex] = $this->option_arr['o_fields_index'];
		}
	
		if (isset($_SESSION[$fields]) && !empty($_SESSION[$fields]))
		{
			# Load fields from session
			$registry->set('fields', $_SESSION[$fields]);
		}
		
		return TRUE;
	}
	
	public function getLayoutRange()
	{
		return $this->layoutRange;
	}
	
	public static function setTimezone($timezone="UTC")
    {
    	if (in_array(version_compare(phpversion(), '5.1.0'), array(0,1)))
		{
			date_default_timezone_set($timezone);
		} else {
			$safe_mode = ini_get('safe_mode');
			if ($safe_mode)
			{
				putenv("TZ=".$timezone);
			}
		}
    }

	public static function setMySQLServerTime($offset="-0:00")
    {
		pjAppModel::factory()->prepare("SET SESSION time_zone = :offset;")->exec(array('offset' => $offset));
    }
    
	public function setTime()
	{
		if (isset($this->option_arr['o_timezone']))
		{
			$offset = $this->option_arr['o_timezone'] / 3600;
			if ($offset > 0)
			{
				$offset = "-".$offset;
			} elseif ($offset < 0) {
				$offset = "+".abs($offset);
			} elseif ($offset === 0) {
				$offset = "+0";
			}
	
			pjAppController::setTimezone('Etc/GMT' . $offset);
			if (strpos($offset, '-') !== false)
			{
				$offset = str_replace('-', '+', $offset);
			} elseif (strpos($offset, '+') !== false) {
				$offset = str_replace('+', '-', $offset);
			}
			pjAppController::setMySQLServerTime($offset . ":00");
		}
	}
    
    public function beforeFilter()
    {
    	$this->appendJs('jquery.min.js', PJ_THIRD_PARTY_PATH . 'jquery/');
		$dm = new pjDependencyManager(PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		$this->appendJs('jquery-migrate.min.js', $dm->getPath('jquery_migrate'), FALSE, FALSE);
		$this->appendJs('pjAdminCore.js');
		$this->appendCss('reset.css');
		 
		$this->appendJs('js/jquery-ui.custom.min.js', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
		$this->appendCss('css/smoothness/jquery-ui.min.css', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
				
		$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
		$this->appendCss('admin.css');
		
    	if ($_GET['controller'] != 'pjInstaller')
		{
			$this->models['Option'] = pjOptionModel::factory();
			$this->option_arr = $this->models['Option']->getPairs($this->getForeignId());
			$this->set('option_arr', $this->option_arr);
			$this->setTime();
			
			if (!isset($_SESSION[$this->defaultLocale]))
			{
				$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
				if (count($locale_arr) === 1)
				{
					$this->setLocaleId($locale_arr[0]['id']);
				}
			}
			$this->loadSetFields();
		}
		
    	if (!in_array($_GET['controller'], array('pjFront', 'pjInstaller')))
    	{
    		$pjCalendarModel = pjCalendarModel::factory();
    		if($this->isEditor())
    		{
    			$pjCalendarModel->where("t1.id IN (SELECT `TCU`.calendar_id FROM `".pjCalendarUserModel::factory()->getTable()."` AS `TCU` WHERE `TCU`.user_id=".$this->getUserId().")");
    		}
	    	$calendars = $pjCalendarModel
				->select('t1.*, t2.content AS `title`')
				->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->orderBy('t1.id ASC')
				->findAll()->getData();
	    	
			$this->set('calendars', $calendars);
			
			if ($this->getForeignId() === false && !empty($calendars))
			{
				$this->setForeignId($calendars[0]['id']);
			}
    	}
    }
    
	public function getForeignId()
	{
		if (isset($_SESSION[$this->defaultCalendarId]))
		{
			return $_SESSION[$this->defaultCalendarId];
		}
		return false;
	}
	
	public function setForeignId($calendar_id)
	{
		$_SESSION[$this->defaultCalendarId] = (int) $calendar_id;
		return $this;
	}
    
    public function isEditor()
    {
    	return (int) $this->getRoleId() === 2;
    }
    
	public function isInvoiceReady()
	{
		return $this->isAdmin() || $this->isEditor();
	}
	
	public function isCountryReady()
	{
		return $this->isAdmin();
	}
	public function isOneAdminReady()
	{
		return $this->isAdmin();
	}
	public function pjActionAfterInstall()
	{
		$calendar_id = pjCalendarModel::factory()->init(1, 1);
		
		if ($calendar_id !== false && (int) $calendar_id > 0)
		{
			$pjWorkingTimeModel = pjWorkingTimeModel::factory();
			$init_wt_arr = $pjWorkingTimeModel->where('foreign_id', 0)->limit(1)->findAll()->getData();
			$wt_data = $init_wt_arr[0];
			unset($wt_data['id']);
			$wt_data['foreign_id'] = $calendar_id;
			$pjWorkingTimeModel->reset()->setAttributes($wt_data)->insert();
			
			$pjOptionModel = pjOptionModel::factory();
			$init_option_arr = $pjOptionModel->where('foreign_id', 0)->findAll()->getData();
			
			$pjOptionModel->reset()->setBatchFields(array('foreign_id', 'key', 'tab_id', 'value', 'label', 'type', 'order', 'is_visible', 'style'));
			foreach ($init_option_arr as $record)
			{
				$record['foreign_id'] = $calendar_id;
				$pjOptionModel->addBatchRow($record);
			}
			$pjOptionModel->insertBatch();
		}
		
		pjLocaleModel::factory()->where('id!=', 1)->eraseAll();
		
		pjInvoiceConfigModel::factory()->set('id', 1)->modify(array(
			'o_booking_url' => "index.php?controller=pjAdminBookings&action=pjActionUpdate&uuid={ORDER_ID}"
		));
		
		$query = sprintf("UPDATE `%s`
			SET `content` = :content
			WHERE `model` = :model
			AND `foreign_id` = (SELECT `id` FROM `%s` WHERE `key` = :key LIMIT 1)
			AND `field` = :field",
			pjMultiLangModel::factory()->getTable(), pjFieldModel::factory()->getTable()
		);
		pjAppModel::factory()->prepare($query)->exec(array(
			'content' => 'Booking URL - Token: {ORDER_ID}',
			'model' => 'pjField',
			'field' => 'title',
			'key' => 'plugin_invoice_i_booking_url'
		));
		
		$query = sprintf("UPDATE `%s`
			SET `label` = :label
			WHERE `key` = :key
			LIMIT 1",
			pjFieldModel::factory()->getTable()
		);
		pjAppModel::factory()->prepare($query)->exec(array(
			'label' => 'Invoice plugin / Booking URL - Token: {ORDER_ID}',
			'key' => 'plugin_invoice_i_booking_url'
		));
	}

    public static function setFields($locale)
    {
    	if(isset($_SESSION['lang_show_id']) && (int) $_SESSION['lang_show_id'] == 1)
		{
			$fields = pjMultiLangModel::factory()
				->select('CONCAT(t1.content, CONCAT(":", t2.id, ":")) AS content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}else{
			$fields = pjMultiLangModel::factory()
				->select('t1.content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}
		$registry = pjRegistry::getInstance();
		$tmp = array();
		if ($registry->is('fields'))
		{
			$tmp = $registry->get('fields');
		}
		$arrays = array();
		foreach ($fields as $key => $value)
		{
			if (strpos($key, '_ARRAY_') !== false)
			{
				list($prefix, $suffix) = explode("_ARRAY_", $key);
				if (!isset($arrays[$prefix]))
				{
					$arrays[$prefix] = array();
				}
				$arrays[$prefix][$suffix] = $value;
			}
		}
		require PJ_CONFIG_PATH . 'settings.inc.php';
		$fields = array_merge($tmp, $fields, $settings, $arrays);
		$registry->set('fields', $fields);
    }

    public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}
	
	public static function jsonEncode($arr)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}
	
	public static function jsonResponse($arr)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo pjAppController::jsonEncode($arr);
		exit;
	}
		
	public static function getTokens($booking_arr, $option_arr)
	{
		$timeslots = NULL;
		$tmp = array();
		$hidePrices = (int) $option_arr['o_hide_prices'] === 1;
		if (isset($booking_arr['bs_arr']))
		{
			foreach ($booking_arr['bs_arr'] as $item)
			{
				$tmp[] = sprintf("%s | %s - %s%s",
					pjUtil::formatDate($item['booking_date'], 'Y-m-d', $option_arr['o_date_format']),
					date($option_arr['o_time_format'], $item['start_ts']),
					date($option_arr['o_time_format'], $item['end_ts']),
					$hidePrices ? NULL : " | " . pjUtil::formatCurrencySign(number_format($item['price'], 2), $option_arr['o_currency'])
				);
			}
		}
		if (!empty($tmp))
		{
			$timeslots = join("\n", $tmp);
		}
		
		$payment_methods = __('payment_methods', true);
		
		$search = array(
			'{Name}', '{Phone}', '{Email}', '{Notes}', '{Country}', '{CalendarName}', 
			'{State}', '{City}', '{Zip}', '{Address1}', '{Address2}',
		 	'{CCType}', '{CCNum}', '{CCExpMonth}', '{CCExpYear}', '{CCSec}',
		 	'{PaymentMethod}', '{Deposit}', '{Tax}', '{Total}', '{Price}',
			'{BookingID}', '{Timeslots}', '{CancelURL}'
		);
		$replace = array(
			@$booking_arr['customer_name'], @$booking_arr['customer_phone'], @$booking_arr['customer_email'], @$booking_arr['customer_notes'], @$booking_arr['country_name'], @$booking_arr['calendar_name'], 
			@$booking_arr['customer_state'], @$booking_arr['customer_city'], @$booking_arr['customer_zip'], @$booking_arr['customer_address_1'], @$booking_arr['customer_address_2'],
			@$booking_arr['cc_type'], @$booking_arr['cc_num'], @$booking_arr['cc_exp_month'], @$booking_arr['cc_exp_year'], @$booking_arr['cc_code'],
			@$payment_methods[$booking_arr['payment_method']],
				$hidePrices ? NULL : pjUtil::formatCurrencySign(number_format(@$booking_arr['booking_deposit'], 2), @$option_arr['o_currency']),
				$hidePrices ? NULL : pjUtil::formatCurrencySign(number_format(@$booking_arr['booking_tax'], 2), @$option_arr['o_currency']),
				$hidePrices ? NULL : pjUtil::formatCurrencySign(number_format(@$booking_arr['booking_total'], 2), @$option_arr['o_currency']),
				$hidePrices ? NULL : pjUtil::formatCurrencySign(number_format(@$booking_arr['booking_price'], 2), @$option_arr['o_currency']),
			@$booking_arr['uuid'], $timeslots,
				sprintf("%sindex.php?controller=pjFrontEnd&action=pjActionCancel&uuid=%s&hash=%s", PJ_INSTALL_URL, $booking_arr['uuid'], sha1($booking_arr['uuid'] . PJ_SALT))
		);
		
		return compact('search', 'replace');
	}
	
	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : false;
	}
	
	public function setLocaleId($locale_id)
	{
		$_SESSION[$this->defaultLocale] = (int) $locale_id;
	}
	
	protected function pjActionGenerateInvoice($booking_id)
	{
		if (!isset($booking_id) || (int) $booking_id <= 0)
		{
			return array('status' => 'ERR', 'code' => 400, 'text' => 'ID is not set ot invalid.');
		}
		$arr = pjBookingModel::factory()->find($booking_id)->getData();
		if (empty($arr))
		{
			return array('status' => 'ERR', 'code' => 404, 'text' => 'Order not found.');
		}
		
		$bs_arr = pjBookingSlotModel::factory()
			->where('t1.booking_id', $booking_id)
			->findAll()
			->getData();
		
		$timeslots = array();
		if (!empty($bs_arr))
		{
			foreach ($bs_arr as $timeslot)
			{
				$timeslots[] = array(
					'name' => sprintf("%s, %s - %s", pjUtil::formatDate($timeslot['booking_date'], 'Y-m-d', $this->option_arr['o_date_format']), $timeslot['start_time'], $timeslot['end_time']),
					'description' => NULL,
					'qty' => 1,
					'unit_price' => $timeslot['price'],
					'amount' => number_format(1 * $timeslot['price'], 2, ".", "")
				);
			}
		} else {
			$timeslots[] = array(
				'name' => 'Booking payment',
				'description' => '',
				'qty' => 1,
				'unit_price' => $arr['booking_total'],
				'amount' => $arr['booking_total']
			);
		}
		
		$map = array(
			'confirmed' => 'paid',
			'cancelled' => 'cancelled',
			'pending' => 'not_paid'
		);
		
		$response = $this->requestAction(
			array(
	    		'controller' => 'pjInvoice',
	    		'action' => 'pjActionCreate',
	    		'params' => array(
    				'key' => md5($this->option_arr['private_key'] . PJ_SALT),
					// -------------------------------------------------
					'uuid' => pjInvoiceModel::factory()->getInvoiceID(),
					'order_id' => $arr['uuid'],
					'foreign_id' => $arr['calendar_id'],
					'issue_date' => ':CURDATE()',
					'due_date' => ':CURDATE()',
					'created' => ':NOW()',
					// 'modified' => ':NULL',
					'status' => @$map[$arr['booking_status']],
	    			'payment_method' => $arr['payment_method'],
	    			'cc_type' => $arr['cc_type'],
	    			'cc_num' => $arr['cc_num'],
	    			'cc_exp_month' => $arr['cc_exp_month'],
	    			'cc_exp_year' => $arr['cc_exp_year'],
	    			'cc_code' => $arr['cc_code'],
					'subtotal' => $arr['booking_price'],
					// 'discount' => $arr['discount'],
					'tax' => $arr['booking_tax'],
					// 'shipping' => $arr['shipping'],
					'total' => $arr['booking_total'],
					'paid_deposit' => $arr['booking_deposit'],
					'amount_due' => $arr['booking_total'] - $arr['booking_deposit'],
					'currency' => $this->option_arr['o_currency'],
					'notes' => $arr['customer_notes'],
					// 'y_logo' => $arr[''],
					// 'y_company' => $arr[''],
					// 'y_name' => $arr[''],
					// 'y_street_address' => $arr[''],
					// 'y_city' => $arr[''],
					// 'y_state' => $arr[''],
					// 'y_zip' => $arr[''],
					// 'y_phone' => $arr[''],
					// 'y_fax' => $arr[''],
					// 'y_email' => $arr[''],
					// 'y_url' => $arr[''],
					'b_billing_address' => $arr['customer_address_1'],
					// 'b_company' => ':NULL',
					'b_name' => $arr['customer_name'],
					'b_address' => $arr['customer_address_1'],
					'b_street_address' => $arr['customer_address_2'],
					'b_city' => $arr['customer_city'],
					'b_state' => $arr['customer_state'],
					'b_zip' => $arr['customer_zip'],
					'b_phone' => $arr['customer_phone'],
					// 'b_fax' => ':NULL',
					'b_email' => $arr['customer_email'],
					// 'b_url' => $arr['url'],
					// 's_shipping_address' => (int) $arr['same_as'] === 1 ? $arr['b_address_1'] : $arr['s_address_1'],
					// 's_company' => ':NULL',
					// 's_name' => (int) $arr['same_as'] === 1 ? $arr['b_name'] : $arr['s_name'],
					// 's_address' => (int) $arr['same_as'] === 1 ? $arr['b_address_1'] : $arr['s_address_1'],
					// 's_street_address' => (int) $arr['same_as'] === 1 ? $arr['b_address_2'] : $arr['s_address_2'],
					// 's_city' => (int) $arr['same_as'] === 1 ? $arr['b_city'] : $arr['s_city'],
					// 's_state' => (int) $arr['same_as'] === 1 ? $arr['b_state'] : $arr['s_state'],
					// 's_zip' => (int) $arr['same_as'] === 1 ? $arr['b_zip'] : $arr['s_zip'],
					// 's_phone' => $arr['phone'],
					// 's_fax' => ':NULL',
					// 's_email' => $arr['email'],
					// 's_url' => $arr['url'],
					// 's_date' => ':NULL',
					// 's_terms' => ':NULL',
					// 's_is_shipped' => ':NULL',
					'items' => $timeslots
					// -------------------------------------------------
    			)
    		),
    		array('return')
		);

		return $response;
	}
	
	public static function getPrices($calendar_id, $wt_arr, $date, $start_ts, $end_ts, $qty)
	{
		$price = 0;
		
		$date_arr = pjDateModel::factory()->getDailyWorkingTime($calendar_id, $date);
		
		if ($date_arr !== false && $date_arr['is_dayoff'] == 'F')
		{
			if (empty($date_arr['price']) || (float) $date_arr['price'] == 0)
			{
				$price_arr = pjPriceModel::factory()
					->where('t1.calendar_id', $calendar_id)
					->where('t1.date', $date)
					->where('t1.start_ts', $start_ts)
					->where('t1.end_ts', $end_ts)
					->orderBy('t1.start_time ASC')
					->limit(1)
					->findAll()
					->getData();
				if (count($price_arr) === 1)
				{
					$price = $price_arr[0]['price'] * $qty;
				}
			} else {
				$price = $date_arr['price'] * $qty;
			}
		}else{
			$day = strtolower(date("l", $start_ts));
			if ($wt_arr !== false && $wt_arr['is_dayoff'] == 'F')
			{
				$price_day_arr = pjPriceDayModel::factory()
					->where('t1.calendar_id', $calendar_id)
					->where('t1.day', $day)
					->where('t1.start_time', date("H:i:s", $start_ts))
					->where('t1.end_time', date("H:i:s", $end_ts))
					->orderBy('t1.start_time ASC')
					->limit(1)
					->findAll()
					->getData();
				if (count($price_day_arr) === 1)
				{
					$price = $price_day_arr[0]['price'] * $qty;
				} else {
					$price = (float) $wt_arr['price'] * $qty;
				}
			}
		}
		return $price;
	}
	
	public static function getCartTotal($calendar_id, $cart, $option_arr)
	{
		$pjWorkingTimeModel = pjWorkingTimeModel::factory();
		
		$price = 0;
		if (!$cart->isEmpty())
		{
			$cart_arr = $cart->getAll();
			
			foreach ($cart_arr as $cid => $date_arr)
			{
				if ($cid != $calendar_id)
				{
					continue;
				}
				
				$wt_data = $pjWorkingTimeModel->getWorkingTime($cid);
				foreach ($date_arr as $date => $time_arr)
				{
					$wt_arr = $pjWorkingTimeModel->filterDate($wt_data, $date);
					foreach ($time_arr as $time => $qty)
					{
						$qty = 1; //FIXME
						list($start_ts, $end_ts) = explode("|", $time);
						$price += pjAppController::getPrices($cid, $wt_arr, $date, $start_ts, $end_ts, $qty);
					}
				}
			}
		}
		
		$tax = ($price * $option_arr['o_tax']) / 100;
		$total = $price + $tax;
		switch ($option_arr['o_deposit_type'])
		{
			case 'amount':
				$deposit = $option_arr['o_deposit'];
				break;
			case 'percent':
			default:
				$deposit = ($total * $option_arr['o_deposit']) / 100;
				break;
		}

		return array('price' => round($price, 2), 'total' => round($total, 2), 'deposit' => round($deposit, 2), 'tax' => round($tax, 2));
	}
	
	public static function getCartPrices($calendar_id, $cart)
	{
		$pjDateModel = pjDateModel::factory();
		$pjWorkingTimeModel = pjWorkingTimeModel::factory();
		$pjPriceModel = pjPriceModel::factory();
		$pjPriceDayModel = pjPriceDayModel::factory();
		
		$_arr = array();
		$cart_arr = $cart->getAll();
		foreach ($cart_arr as $cid => $date_arr)
		{
			if ($cid != $calendar_id)
			{
				continue;
			}
			$wt_data = $pjWorkingTimeModel->getWorkingTime($calendar_id);
			foreach ($date_arr as $date => $time_arr)
			{
				$wt_arr = $pjWorkingTimeModel->filterDate($wt_data, $date);
				foreach ($time_arr as $time => $q)
				{
					list($start_ts, $end_ts) = explode("|", $time);
					
					$index = $time;
					$_arr[$index] = 0;
					
					$_arr[$index] = pjAppController::getPrices($calendar_id, $wt_arr, $date, $start_ts, $end_ts, 1);
				}
			}
		}
		# NB: Higher RANK gets bigger precedence!
		return array($calendar_id => $_arr);
	}
	
	public static function getPricesDate($calendar_id, $date, $option_arr)
	{
		$t_arr = pjAppController::getDailySlots($calendar_id, $date, $option_arr);
		if ($t_arr === false)
		{
			# It's Day off
			return false;
		}
		
		$pjWorkingTimeModel = pjWorkingTimeModel::factory();
				
		$wt_data = $pjWorkingTimeModel->getWorkingTime($calendar_id);
		$wt_arr = $pjWorkingTimeModel->filterDate($wt_data, $date);
		
		$_arr = array();
		$step = $t_arr['slot_length'] * 60;
		# Fix for 24h support
		$offset = $t_arr['end_ts'] <= $t_arr['start_ts'] ? 86400 : 0;
		$day = strtolower(date("l", strtotime($date)));
	
		$slots = $t_arr['slots'];
		$i = $t_arr['start_ts'];
		$lunch_from_ts = $t_arr['lunch_start_ts'];
		$lunch_to_ts = $t_arr['lunch_end_ts'];
		if($lunch_from_ts < $i)
		{
			$lunch_from_ts = $lunch_from_ts + (24 * 60 * 60);
			$lunch_to_ts = $lunch_to_ts + (24 * 60 * 60);
		}
		for($n = 1; $n <= $slots; $n++)
		{
			$index = $i . "|" . ($i + $step);
			$_arr[$index] = 0;
			
			$_arr[$index] = pjAppController::getPrices($calendar_id, $wt_arr, $date, $i, ($i + $step), 1);
			if($i + $step == $lunch_from_ts)
			{
				$i = $lunch_to_ts;
			}else{
				$i = $i + $step;
			}
		}
		# NB: Higher RANK gets bigger precedence!
		return $_arr;
	}
	
	public static function getDailySlots($calendar_id, $date, $option_arr)
	{
		$date_arr = pjDateModel::factory()->getDailyWorkingTime($calendar_id, $date);
		
		if ($date_arr === false)
		{
			# There is not custom working time/prices for given date, so get for day of week (Monday, Tuesday...)
			$pjWorkingTimeModel = pjWorkingTimeModel::factory();
			$wt_data = $pjWorkingTimeModel->getWorkingTime($calendar_id);
			$wt_arr = $pjWorkingTimeModel->filterDate($wt_data, $date);

			//$wt_arr['slot_length'] = $option_arr['slot_length'];
			$t_arr = $wt_arr;
		} else {
			# There is custom working time/prices for given date
			$t_arr = $date_arr;
		}
		
		return $t_arr;
	}
	
	public static function getWeeklySlots($calendar_id, $iso_date)
	{
		$date_arr = pjDateModel::factory()->getWeeklyWorkingTime($calendar_id, $iso_date);
		
		$pjWorkingTimeModel = pjWorkingTimeModel::factory();
		$wt_data = $pjWorkingTimeModel->getWorkingTime($calendar_id);

		$t_arr = array();
		foreach ($date_arr as $date => $item)
		{
			$t_arr[$date] = array();
			
			# There is not custom working time/prices for given date, so get for day of week (Monday, Tuesday...)
			if (empty($item))
			{
				$t_arr[$date] = $pjWorkingTimeModel->filterDate($wt_data, $date);
				continue;
			}
			
			$t_arr[$date] = $item;
		}
		
		return $t_arr;
	}
	
	public static function getMonthlySlots($calendar_id, $month, $year)
	{
		$date_arr = pjDateModel::factory()->getMonthlyWorkingTime($calendar_id, $month, $year);
		
		$pjWorkingTimeModel = pjWorkingTimeModel::factory();
		$wt_data = $pjWorkingTimeModel->getWorkingTime($calendar_id);

		$t_arr = array();
		foreach ($date_arr as $date => $item)
		{
			$t_arr[$date] = array();
			
			# There is not custom working time/prices for given date, so get for day of week (Monday, Tuesday...)
			if (empty($item))
			{
				$t_arr[$date] = $pjWorkingTimeModel->filterDate($wt_data, $date);
				continue;
			}
			
			$t_arr[$date] = $item;
		}
		
		return $t_arr;
	}
	
	public static function getMonthStatus($calendar_id, $month, $year)
	{
		$numOfDays = date("t", mktime(0, 0, 0, $month, 1, $year));
		$_arr = array();
		foreach (range(1, $numOfDays) as $i)
		{
			$_arr[date("Y-m-d", mktime(0, 0, 0, $month, $i, $year))] = array('code' => 1, 'text' => 'free');
		}
		
		$bookings = pjBookingSlotModel::factory()->getBookings($calendar_id, $month, $year);
		$monthlySlots = pjAppController::getMonthlySlots($calendar_id, $month, $year);
		$now = time();
		
		foreach ($monthlySlots as $date => $t_arr)
		{
			if (empty($t_arr) || (isset($t_arr['is_dayoff']) && $t_arr['is_dayoff'] == 'T'))
			{
				continue;
			}
			$step = $t_arr['slot_length'] * 60;
			# Fix for 24h support
			$offset = $t_arr['end_ts'] <= $t_arr['start_ts'] ? 86400 : 0;

			$dailyAv = 0;
			$dailyBo = 0;
			for ($i = $t_arr['start_ts']; $i < $t_arr['end_ts'] + $offset; $i += $step)
			{
				if ($i < $now)
				{
					# Past
					continue;
				} elseif (isset($t_arr['lunch_start_ts']) && isset($t_arr['lunch_end_ts']) && $i >= $t_arr['lunch_start_ts'] && $i < $t_arr['lunch_end_ts']) {
					# Lunch break
					continue;
				}
				
				foreach ($bookings[$date] as $bs)
				{
					if ($bs['start_ts'] == $i && $bs['end_ts'] == $i + $step)
					{
						$dailyBo += 1;
					}
				}
				$dailyAv += $t_arr['slot_limit'];
			}
			
			if ($dailyBo > 0 && $dailyAv > $dailyBo)
			{
				$_arr[$date] = array('code' => 2, 'text' => 'partly');
			} elseif ($dailyAv <= $dailyBo) {
				$_arr[$date] = array('code' => 3, 'text' => 'fully');
			}
		}
		
		return $_arr;
	}
	
	public static function getAdminEmail()
	{
		$arr = pjUserModel::factory()->find(1)->getData();
		return $arr['email'];
	}
}
?>