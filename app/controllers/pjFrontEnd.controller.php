<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontEnd extends pjFront
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setAjax(true);
		
		$this->setLayout('pjActionEmpty');
	}
	
	public function pjActionCancel()
	{
		$this->setAjax(false);
		$this->setLayout('pjActionCancel');
		$this->appendCss('TSBCalendar1.css');
		$this->appendCss('index.php?controller=pjFrontEnd&action=pjActionLoadCss&cid=1', PJ_INSTALL_URL, true);
		
		if (!isset($_GET['uuid'], $_GET['hash'])
			|| empty($_GET['uuid'])
			|| empty($_GET['hash'])
			|| $_GET['hash'] != sha1($_GET['uuid'] . PJ_SALT))
		{
			$this->set('response', array('status' => 'ERR', 'code' => 100, 'text' => __('front_cancel_100', true)));
			return;
		}
		
		$pjBookingModel = pjBookingModel::factory();
		$arr = $pjBookingModel
			->select('t1.*, t2.content AS `country_name`')
			->join('pjMultiLang', sprintf("t2.model='pjCountry' AND t2.foreign_id=t1.customer_country AND t2.locale=t1.locale_id AND t2.field='name'"), 'left outer')
			->where('t1.uuid', $_GET['uuid'])
			->limit(1)
			->findAll()
			->getDataIndex(0);
			
		if ($arr === FALSE || empty($arr))
		{
			$this->set('response', array('status' => 'ERR', 'code' => 101, 'text' => __('front_cancel_101', true)));
			return;
		}
		
		if (isset($_GET['status']) && $arr['booking_status'] == 'cancelled')
		{
			$this->set('response', array('status' => 'ERR', 'code' => 103, 'text' => __('front_cancel_103', true)));
			return;
		}
		
		if ($arr['booking_status'] == 'cancelled')
		{
			$this->set('response', array('status' => 'ERR', 'code' => 102, 'text' => __('front_cancel_102', true)));
			return;
		}
		
		if (isset($_POST['ts_cancel_booking']))
		{
			$result = $pjBookingModel
				->reset()
				->set('id', $arr['id'])
				->modify(array('booking_status' => 'cancelled'))
				->getAffectedRows();
			if ($result == 1)
			{
				# Cancellation email(s)
				$booking_arr = $pjBookingModel
					->reset()
					->select("t1.*, t1.id AS `booking_id`, t2.content AS `country_name`, t3.content AS `cancel_subject_client`, t4.content AS `cancel_tokens_client`,
						t5.content AS `cancel_subject_admin`, t6.content AS `cancel_tokens_admin`, t8.email AS `admin_email`, t9.content AS `confirm_sms_admin`,
						t10.content AS `payment_sms_admin`, t11.content AS `calendar_name`, AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
								AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`,
								AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,
								AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`,
								AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`")
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.customer_country AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t1.calendar_id AND t3.locale=t1.locale_id AND t3.field='cancel_subject_client'", 'left outer')
					->join('pjMultiLang', "t4.model='pjCalendar' AND t4.foreign_id=t1.calendar_id AND t4.locale=t1.locale_id AND t4.field='cancel_tokens_client'", 'left outer')
					->join('pjMultiLang', "t5.model='pjCalendar' AND t5.foreign_id=t1.calendar_id AND t5.locale=t1.locale_id AND t5.field='cancel_subject_admin'", 'left outer')
					->join('pjMultiLang', "t6.model='pjCalendar' AND t6.foreign_id=t1.calendar_id AND t6.locale=t1.locale_id AND t6.field='cancel_tokens_admin'", 'left outer')
					->join('pjCalendar', 't7.id=t1.calendar_id', 'left outer')
					->join('pjUser', 't8.id=t7.user_id', 'left outer')
					->join('pjMultiLang', "t9.model='pjCalendar' AND t9.foreign_id=t1.calendar_id AND t9.locale=t1.locale_id AND t9.field='confirm_sms_admin'", 'left outer')
					->join('pjMultiLang', "t10.model='pjCalendar' AND t10.foreign_id=t1.calendar_id AND t10.locale=t1.locale_id AND t10.field='payment_sms_admin'", 'left outer')
					->join('pjMultiLang', "t11.model='pjCalendar' AND t11.foreign_id=t1.calendar_id AND t11.locale=t1.locale_id AND t11.field='title'", 'left outer')
					->find($arr['id'])
					->getData();

				$booking_arr['bs_arr'] = pjBookingSlotModel::factory()
					->join('pjBooking', 't2.id=t1.booking_id', 'inner')
					->where('t1.booking_id', $arr['id'])
					->findAll()
					->getData();
				
				pjFrontEnd::pjActionConfirmSend($this->option_arr, $booking_arr, 'cancel');
				
				pjUtil::redirect(sprintf("%sindex.php?controller=pjFrontEnd&action=pjActionCancel&uuid=%s&hash=%s&status=1", PJ_INSTALL_URL, $_GET['uuid'], $_GET['hash']));
				return;
			}
		}
		
		$arr['bs_arr'] = pjBookingSlotModel::factory()
			->where('t1.booking_id', $arr['id'])
			->orderBy('t1.booking_date ASC, t1.start_ts ASC')
			->findAll()
			->getData();
		
		$this->set('arr', $arr);
		$this->set('response', array('status' => 'OK', 'code' => 200, 'text' => ''));
	}
	
	public function pjActionCaptcha()
	{
		header("Cache-Control: max-age=3600, private");
		$pjCaptcha = new pjCaptcha(PJ_WEB_PATH . 'obj/Anorexia.ttf', $this->defaultCaptcha, 6);
		$pjCaptcha->setImage(PJ_IMG_PATH . 'frontend/as-captcha.png')->init(@$_GET['rand']);
		exit;
	}
	
	public function pjActionCheckCaptcha()
	{
		if ($this->isXHR())
		{
			echo isset($_SESSION[$this->defaultCaptcha])
				&& isset($_GET['captcha'])
				&& pjCaptcha::validate($_GET['captcha'], $_SESSION[$this->defaultCaptcha])
				? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionConfirmAuthorize()
	{
		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}
		
		if (!isset($_POST['x_invoice_num']))
		{
			$this->log('Missing arguments');
			exit;
		}
		
		$pjInvoiceModel = pjInvoiceModel::factory();
		$pjBookingModel = pjBookingModel::factory();
		
		$invoice_arr = $pjInvoiceModel
			->where('t1.uuid', $_POST['x_invoice_num'])
			->limit(1)
			->findAll()
			->getData();
		if (!empty($invoice_arr))
		{
			$invoice_arr = $invoice_arr[0];
			$booking_arr = $pjBookingModel
				->select('t1.*, t1.id AS `booking_id`, t2.content AS `country_name`, t3.content AS `payment_subject_client`, t4.content AS `payment_tokens_client`,
					t5.content AS `payment_subject_admin`, t6.content AS `payment_tokens_admin`, t8.email AS `admin_email`, 
						t9.content AS `confirm_sms_admin`, t10.content AS `payment_sms_admin`, t11.content AS `calendar_name`')
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.customer_country AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
				->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t1.calendar_id AND t3.locale=t1.locale_id AND t3.field='payment_subject_client'", 'left outer')
				->join('pjMultiLang', "t4.model='pjCalendar' AND t4.foreign_id=t1.calendar_id AND t4.locale=t1.locale_id AND t4.field='payment_tokens_client'", 'left outer')
				->join('pjMultiLang', "t5.model='pjCalendar' AND t5.foreign_id=t1.calendar_id AND t5.locale=t1.locale_id AND t5.field='payment_subject_admin'", 'left outer')
				->join('pjMultiLang', "t6.model='pjCalendar' AND t6.foreign_id=t1.calendar_id AND t6.locale=t1.locale_id AND t6.field='payment_tokens_admin'", 'left outer')
				->join('pjCalendar', 't7.id=t1.calendar_id', 'left outer')
				->join('pjUser', 't8.id=t7.user_id', 'left outer')
				->join('pjMultiLang', "t9.model='pjCalendar' AND t9.foreign_id=t1.calendar_id AND t9.locale=t1.locale_id AND t9.field='confirm_sms_admin'", 'left outer')
				->join('pjMultiLang', "t10.model='pjCalendar' AND t10.foreign_id=t1.calendar_id AND t10.locale=t1.locale_id AND t10.field='payment_sms_admin'", 'left outer')
				->join('pjMultiLang', "t11.model='pjCalendar' AND t11.foreign_id=t1.calendar_id AND t11.locale=t1.locale_id AND t11.field='title'", 'left outer')
				->where('t1.uuid', $invoice_arr['order_id'])
				->limit(1)
				->findAll()
				->getData();
			if (!empty($booking_arr))
			{
				$booking_arr = $booking_arr[0];
				$option_arr = pjOptionModel::factory()->getPairs($booking_arr['calendar_id']);

				$params = array(
					'transkey' => $option_arr['o_authorize_key'],
					'x_login' => $option_arr['o_authorize_mid'],
					'md5_setting' => $option_arr['o_authorize_hash'],
					'key' => md5($this->option_arr['private_key'] . PJ_SALT)
				);
				
				$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
				if ($response !== FALSE && $response['status'] === 'OK')
				{
					$pjBookingModel
						->reset()
						->set('id', $booking_arr['id'])
						->modify(array('booking_status' => $option_arr['o_status_if_paid']));
						
					$pjInvoiceModel
						->reset()
						->set('id', $invoice_arr['id'])
						->modify(array('status' => 'paid', 'modified' => ':NOW()'));
					
					$booking_arr['bs_arr'] = pjBookingSlotModel::factory()
						->join('pjBooking', 't2.id=t1.booking_id', 'inner')
						->where('t1.booking_id', $booking_id)
						->findAll()
						->getData();
						
					pjFrontEnd::pjActionConfirmSend($option_arr, $booking_arr, 'payment');
					
				} elseif (!$response) {
					$this->log('Authorization failed');
				} else {
					$this->log('Booking not confirmed. ' . $response['response_reason_text']);
				}
			} else {
				$this->log('Booking not found');
			}
		} else {
			$this->log('Invoice not found');
		}
		exit;
	}

	public function pjActionConfirmPaypal()
	{
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			exit;
		}
		
		$pjInvoiceModel = pjInvoiceModel::factory();
		$pjBookingModel = pjBookingModel::factory();

		$invoice_arr = $pjInvoiceModel
			->where('t1.uuid', $_POST['custom'])
			->limit(1)
			->findAll()
			->getData();

		if (!empty($invoice_arr))
		{
			$invoice_arr = $invoice_arr[0];
			$booking_arr = $pjBookingModel
				->select('t1.*, t1.id AS `booking_id`, t2.content AS `country_name`, t3.content AS `payment_subject_client`, t4.content AS `payment_tokens_client`,
					t5.content AS `payment_subject_admin`, t6.content AS `payment_tokens_admin`, t8.email AS `admin_email`, 
					t9.content AS `confirm_sms_admin`, t10.content AS `payment_sms_admin`, t11.content AS `calendar_name`')
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.customer_country AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
				->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t1.calendar_id AND t3.locale=t1.locale_id AND t3.field='payment_subject_client'", 'left outer')
				->join('pjMultiLang', "t4.model='pjCalendar' AND t4.foreign_id=t1.calendar_id AND t4.locale=t1.locale_id AND t4.field='payment_tokens_client'", 'left outer')
				->join('pjMultiLang', "t5.model='pjCalendar' AND t5.foreign_id=t1.calendar_id AND t5.locale=t1.locale_id AND t5.field='payment_subject_admin'", 'left outer')
				->join('pjMultiLang', "t6.model='pjCalendar' AND t6.foreign_id=t1.calendar_id AND t6.locale=t1.locale_id AND t6.field='payment_tokens_admin'", 'left outer')
				->join('pjCalendar', 't7.id=t1.calendar_id', 'left outer')
				->join('pjUser', 't8.id=t7.user_id', 'left outer')
				->join('pjMultiLang', "t9.model='pjCalendar' AND t9.foreign_id=t1.calendar_id AND t9.locale=t1.locale_id AND t9.field='confirm_sms_admin'", 'left outer')
				->join('pjMultiLang', "t10.model='pjCalendar' AND t10.foreign_id=t1.calendar_id AND t10.locale=t1.locale_id AND t10.field='payment_sms_admin'", 'left outer')
				->join('pjMultiLang', "t11.model='pjCalendar' AND t11.foreign_id=t1.calendar_id AND t11.locale=t1.locale_id AND t11.field='title'", 'left outer')
				->where('t1.uuid', $invoice_arr['order_id'])
				->limit(1)
				->findAll()
				->getData();
			if (!empty($booking_arr))
			{
				$booking_arr = $booking_arr[0];
				$option_arr = pjOptionModel::factory()->getPairs($booking_arr['calendar_id']);
				$params = array(
					'txn_id' => @$booking_arr['txn_id'],
					'paypal_address' => @$option_arr['o_paypal_address'],
					'deposit' => @$invoice_arr['paid_deposit'],
					'currency' => @$invoice_arr['currency'],
					'key' => md5($this->option_arr['private_key'] . PJ_SALT)
				);

				$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
				if ($response !== FALSE && $response['status'] === 'OK')
				{
					$this->log('Booking confirmed');
					$pjBookingModel->reset()->set('id', $booking_arr['id'])->modify(array(
						'booking_status' => $option_arr['o_status_if_paid'],
						'txn_id' => $response['transaction_id'],
						'processed_on' => ':NOW()'
					));
					
					$pjInvoiceModel
						->reset()
						->set('id', $invoice_arr['id'])
						->modify(array('status' => 'paid', 'modified' => ':NOW()'));
						
					$booking_arr['bs_arr'] = pjBookingSlotModel::factory()
						->join('pjBooking', 't2.id=t1.booking_id', 'inner')
						->where('t1.booking_id', $booking_id)
						->findAll()
						->getData();
						
					pjFrontEnd::pjActionConfirmSend($option_arr, $booking_arr, 'payment');
					
				} elseif (!$response) {
					$this->log('Authorization failed');
				} else {
					$this->log('Booking not confirmed');
				}
			} else {
				$this->log('Booking not found');
			}
		} else {
			$this->log('Invoice not found');
		}
		exit;
	}
	
	public function pjActionConfirmSend($option_arr, $booking_arr, $type)
	{
		if (!in_array($type, array('confirm', 'payment', 'cancel')))
		{
			return false;
		}
		$Email = new pjEmail();
		$Email->setContentType('text/html');
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
				->setTransport('smtp')
				->setSmtpHost($option_arr['o_smtp_host'])
				->setSmtpPort($option_arr['o_smtp_port'])
				->setSmtpUser($option_arr['o_smtp_user'])
				->setSmtpPass($option_arr['o_smtp_pass'])
				->setSender($option_arr['o_smtp_user']);
		}
		$tokens = pjAppController::getTokens($booking_arr, $option_arr);
		$from = $booking_arr['admin_email'];
		if(!empty($option_arr['o_from_email']))
		{
			$from = $option_arr['o_from_email'];
		}
		switch ($type)
		{
			case 'confirm':
				// Client
				$subject = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['confirm_subject_client']);
				$message = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['confirm_tokens_client']);
				if (!empty($subject) && !empty($message))
				{
					$message = pjUtil::textToHtml($message);
					$Email
						->setTo($booking_arr['customer_email'])
						->setFrom($from)
						->setSubject($subject)
						->send($message);
				}
				// Admin
				$subject = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['confirm_subject_admin']);
				$message = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['confirm_tokens_admin']);
				if (!empty($subject) && !empty($message))
				{
					$user_arr = pjUserModel::factory()->where('t1.status', 'T')->where("(t1.notify_email LIKE '%confirm%')")->findAll()->getDataPair('id', 'email');
					if(!in_array($booking_arr['admin_email'], $user_arr))
					{
						$user_arr[] = $booking_arr['admin_email'];
					}
					$message = pjUtil::textToHtml($message);
					foreach ($user_arr as $recipient)
					{
						$r = $Email
							->setTo($recipient)
							->setFrom($from)
							->setSubject($subject)
							->send($message);
					}
				}
				
				/*Sms to admin*/
				$message = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['confirm_sms_admin']);
				if (!empty($message))
				{
					$isSms = (pjObject::getPlugin('pjSms') !== NULL);
					$priv_key = md5($option_arr['private_key'] . PJ_SALT);
					if ($isSms)
					{
						$user_arr = pjUserModel::factory()->where('t1.status', 'T')->where("(t1.notify_sms LIKE '%confirm%')")->findAll()->getDataPair('id', 'phone');
						if(!in_array($booking_arr['customer_phone'], $user_arr))
						{
							$user_arr[] = $booking_arr['customer_phone'];
						}
						foreach ($user_arr as $recipient)
						{
							$number = preg_replace('/\D/', '', $recipient);
							$result = $this->requestAction(array(
									'controller' => 'pjSms',
									'action' => 'pjActionSend',
									'params' => array(
											'key' => $priv_key,
											'number' => $number,
											'type' => 'unicode',
											'text' => $message
									)), array('return'));
						}
					}
				}
				break;
			case 'payment':
				// Client
				$subject = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['payment_subject_client']);
				$message = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['payment_tokens_client']);
				if (!empty($subject) && !empty($message))
				{
					$message = pjUtil::textToHtml($message);
					$Email
						->setTo($booking_arr['customer_email'])
						->setFrom($from)
						->setSubject($subject)
						->send($message);
				}
				// Admin
				$subject = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['payment_subject_admin']);
				$message = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['payment_tokens_admin']);
				if (!empty($subject) && !empty($message))
				{
					$user_arr = pjUserModel::factory()->where('t1.status', 'T')->where("(t1.notify_email LIKE '%payment%')")->findAll()->getDataPair('id', 'email');
					if(!in_array($booking_arr['admin_email'], $user_arr))
					{
						$user_arr[] = $booking_arr['admin_email'];
					}
					$message = pjUtil::textToHtml($message);
					foreach ($user_arr as $recipient)
					{
						$r = $Email
							->setTo($recipient)
							->setFrom($from)
							->setSubject($subject)
							->send($message);
					}
				}
				
				/*Sms to admin*/
				$message = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['payment_sms_admin']);
				if (!empty($message))
				{
					$isSms = (pjObject::getPlugin('pjSms') !== NULL);
					$priv_key = md5($option_arr['private_key'] . PJ_SALT);
					if ($isSms)
					{
						$user_arr = pjUserModel::factory()->where('t1.status', 'T')->where("(t1.notify_sms LIKE '%payment%')")->findAll()->getDataPair('id', 'phone');
						if(!in_array($booking_arr['customer_phone'], $user_arr))
						{
							$user_arr[] = $booking_arr['customer_phone'];
						}
						foreach ($user_arr as $recipient)
						{
							$number = preg_replace('/\D/', '', $recipient);
							$result = $this->requestAction(array(
									'controller' => 'pjSms',
									'action' => 'pjActionSend',
									'params' => array(
											'key' => $priv_key,
											'number' => $number,
											'type' => 'unicode',
											'text' => $message
									)), array('return'));
						}
					}
				}
				break;
			
			case 'cancel':
				// Client
				$subject = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['cancel_subject_client']);
				$message = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['cancel_tokens_client']);
				if (!empty($subject) && !empty($message))
				{
					$message = pjUtil::textToHtml($message);
					$Email
					->setTo($booking_arr['customer_email'])
					->setFrom($from)
					->setSubject($subject)
					->send($message);
				}
				// Admin
				$subject = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['cancel_subject_admin']);
				$message = str_replace($tokens['search'], $tokens['replace'], @$booking_arr['cancel_tokens_admin']);
				if (!empty($subject) && !empty($message))
				{
					$user_arr = pjUserModel::factory()->where('t1.status', 'T')->where("(t1.notify_email LIKE '%cancel%')")->findAll()->getDataPair('id', 'email');
					if(!in_array($booking_arr['admin_email'], $user_arr))
					{
						$user_arr[] = $booking_arr['admin_email'];
					}
					$message = pjUtil::textToHtml($message);
					echo $message;
					foreach ($user_arr as $recipient)
					{
						$r = $Email
						->setTo($recipient)
						->setFrom($from)
						->setSubject($subject)
						->send($message);
					}
				}
				break;
		}
	}
	
	public function pjActionGetTerms()
	{
		if ($this->isXHR())
		{
			if (isset($_GET['cid']) && (int) $_GET['cid'] > 0)
			{
				$this->set('terms_arr', $this->getTerms($_GET['cid']));
			}
		}
	}
	
	public function pjActionLoad()
	{
		$this->setAjax(false);
		$this->setLayout('pjActionFront');
		
		ob_start();
		header("Content-Type: text/javascript; chartset=utf-8");
		
		if(isset($_GET['switch']))
		{
			$_SESSION[$this->defaultSwitchLayout] = $_GET['switch'];
		}else{
			$_SESSION[$this->defaultSwitchLayout] = 0;
		}
		$cid = @$_GET['cid'];
		
		$days_off = $dates_off = $dates_on = array();
		$w_arr = pjWorkingTimeModel::factory()->where('t1.foreign_id', $cid)->findAll()->getData();
		if (!empty($w_arr))
		{
			$w_arr = $w_arr[0];
			
			if ($w_arr['monday_dayoff'] == 'T')
			{
				$days_off[] = 1;
			}
			if ($w_arr['tuesday_dayoff'] == 'T')
			{
				$days_off[] = 2;
			}
			if ($w_arr['wednesday_dayoff'] == 'T')
			{
				$days_off[] = 3;
			}
			if ($w_arr['thursday_dayoff'] == 'T')
			{
				$days_off[] = 4;
			}
			if ($w_arr['friday_dayoff'] == 'T')
			{
				$days_off[] = 5;
			}
			if ($w_arr['saturday_dayoff'] == 'T')
			{
				$days_off[] = 6;
			}
			if ($w_arr['sunday_dayoff'] == 'T')
			{
				$days_off[] = 0;
			}
		}

		$d_arr = pjDateModel::factory()
			->where('t1.foreign_id', $cid)
			->where('t1.date >= CURDATE()')
			->findAll()
			->getData();

		foreach ($d_arr as $date)
		{
			if ($date['is_dayoff'] == 'T')
			{
				$dates_off[] = $date['date'];
			} else {
				$dates_on[] = $date['date'];
			}
		}

		$this->set('days_off', $days_off);
		$this->set('dates_off', $dates_off);
		$this->set('dates_on', $dates_on);
	}
	
	public function pjActionLoadCss()
	{
		$dm = new pjDependencyManager(PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		$theme = isset($_GET['theme']) ? $_GET['theme'] : $this->option_arr['o_theme'];
		if((int) $theme > 0)
		{
			$theme = 'theme' . $theme;
		}
		$arr = array(
			array('file' => 'tooltipster.css', 'path' => $dm->getPath('pj_tooltipster')),
			array('file' => 'pj-calendar.css', 'path' => PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'),
			array('file' => "style.css", 'path' => PJ_CSS_PATH),
			array('file' => "$theme.css", 'path' => PJ_CSS_PATH . "themes/")
		);
		
		header("Content-Type: text/css; charset=utf-8");
		$cid = (int) @$_GET['cid'];
		foreach ($arr as $item)
		{
			$string = FALSE;
			if ($stream = fopen($item['path'] . $item['file'], 'rb'))
			{
				$string = stream_get_contents($stream);
				fclose($stream);
			}
			
			if ($string !== FALSE)
			{
				echo str_replace(
					array(
						'images/', "pjWrapper"
					),
					array(
						PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/',
						"pjWrapperTSBCalendar_" . $this->getForeignId()
					),
					$string
				) . "\n";
			}
		}
		exit;
	}

	public function pjActionOrder()
	{
		if ($this->isXHR())
		{
			if (!isset($_GET['cid']) || (int) $_GET['cid'] <= 0)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			
			$response = $this->validateCheckout($_GET['cid']);
			if ($response['status'] == 'ERR')
			{
				pjAppController::jsonResponse($response);
			}
			
			$response = $this->validateCart($_GET['cid']);
			if ($response['status'] == 'ERR')
			{
				pjAppController::jsonResponse($response);
			}
			
			if (!isset($_POST['ts_preview']) ||
				!isset($_SESSION[$this->defaultForm]) || empty($_SESSION[$this->defaultForm]) ||
				!isset($_SESSION[$this->defaultCart]) || empty($_SESSION[$this->defaultCart]))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing or empty parameters.'));
			}
			
			$FORM = $_SESSION[$this->defaultForm];
			$CART = $_SESSION[$this->defaultCart];
			$cid = $_GET['cid'];
			$locale = $_GET['locale'];
			
			if (isset($CART[$cid]) && !empty($CART[$cid]))
			{
				$pjBookingSlotModel = pjBookingSlotModel::factory();
				foreach ($CART[$cid] as $date => $items)
				{
					$t_arr = pjAppController::getDailySlots($cid, $date, $this->option_arr);
					foreach ($items as $key => $qty)
					{
						list($start_ts, $end_ts) = explode("|", $key);
						
						$cnt = $pjBookingSlotModel
							->reset()
							->join('pjBooking', "t2.id=t1.booking_id", 'inner')
							->where('t1.booking_date', $date)
							->where('t1.start_time', date("H:i:s", $start_ts))
							->where('t1.end_time', date("H:i:s", $end_ts))
							->where('t1.start_ts', $start_ts)
							->where('t1.end_ts', $end_ts)
							->where('t2.booking_status <>', 'cancelled')
							->where('t2.calendar_id', $cid)
							->orderBy('t1.booking_date ASC, t1.start_ts ASC')
							->findCount()
							->getData();
						
						if($cnt > 0 && $cnt >= $t_arr['slot_limit'])
						{
							pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' =>'Duplicated slots'));
							exit;
						}
					}
				}
			}
				
			$data = array();
			$data['uuid'] = pjUtil::uuid();
			$data['calendar_id'] = $cid;
			$data['locale_id'] = $locale;
			$data['ip'] = pjUtil::getClientIp();
			$data['booking_status'] = $this->option_arr['o_status_if_not_paid'];
			if (isset($FORM['payment_method']) && $FORM['payment_method'] != 'creditcard')
			{
				unset($FORM['cc_type']);
				unset($FORM['cc_num']);
				unset($FORM['cc_code']);
				unset($FORM['cc_exp_month']);
				unset($FORM['cc_exp_year']);
			}
			$info = pjAppController::getCartTotal($cid, $this->cart, $this->option_arr);
			$data['booking_price'] = $info['price'];
			$data['booking_tax'] = $info['tax'];
			$data['booking_deposit'] = $info['deposit'];
			$data['booking_total'] = $info['total'];
			
			$data = array_merge($_POST, $FORM, $data);
			
			$required = array('uuid', 'calendar_id', 'locale_id', 'booking_status', 'booking_price', 'booking_deposit', 'booking_tax', 'booking_total');
			$pjBookingModel = pjBookingModel::factory();
			if (!$pjBookingModel->validateRequest($required, $data))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Data not valid.'));
			}
			
			$booking_id = $pjBookingModel->reset()->setAttributes($data)->insert()->getInsertId();
			if ($booking_id === false || empty($booking_id))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Order has not been stored.'));
			}
			
			if (isset($CART[$cid]) && !empty($CART[$cid]))
			{
				$prices = pjAppController::getCartPrices($cid, $this->cart);
				$pjBookingSlotModel = pjBookingSlotModel::factory();
				$pjBookingSlotModel->setBatchFields(array('booking_id', 'booking_date', 'start_time', 'end_time', 'start_ts', 'end_ts', 'price'));
				foreach ($CART[$cid] as $date => $items)
				{
					foreach ($items as $key => $qty)
					{
						list($start_ts, $end_ts) = explode("|", $key);
						$pjBookingSlotModel->addBatchRow(array(
							$booking_id,
							$date,
							date("H:i:s", $start_ts),
							date("H:i:s", $end_ts),
							$start_ts,
							$end_ts,
							@$prices[$cid][$key]
						));
					}
				}
				$pjBookingSlotModel->insertBatch();
			}
			
			$invoice_arr = $this->pjActionGenerateInvoice($booking_id);
			
			# Confirmation email(s)
			$booking_arr = $pjBookingModel
				->reset()
				->select('t1.*, t1.id AS `booking_id`, t2.content AS `country_name`, t3.content AS `confirm_subject_client`, t4.content AS `confirm_tokens_client`,
					t5.content AS `confirm_subject_admin`, t6.content AS `confirm_tokens_admin`, t8.email AS `admin_email`, t9.content AS `confirm_sms_admin`, 
						t10.content AS `payment_sms_admin`, t11.content AS `calendar_name`')
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.customer_country AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
				->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t1.calendar_id AND t3.locale=t1.locale_id AND t3.field='confirm_subject_client'", 'left outer')
				->join('pjMultiLang', "t4.model='pjCalendar' AND t4.foreign_id=t1.calendar_id AND t4.locale=t1.locale_id AND t4.field='confirm_tokens_client'", 'left outer')
				->join('pjMultiLang', "t5.model='pjCalendar' AND t5.foreign_id=t1.calendar_id AND t5.locale=t1.locale_id AND t5.field='confirm_subject_admin'", 'left outer')
				->join('pjMultiLang', "t6.model='pjCalendar' AND t6.foreign_id=t1.calendar_id AND t6.locale=t1.locale_id AND t6.field='confirm_tokens_admin'", 'left outer')
				->join('pjCalendar', 't7.id=t1.calendar_id', 'left outer')
				->join('pjUser', 't8.id=t7.user_id', 'left outer')
				->join('pjMultiLang', "t9.model='pjCalendar' AND t9.foreign_id=t1.calendar_id AND t9.locale=t1.locale_id AND t9.field='confirm_sms_admin'", 'left outer')
				->join('pjMultiLang', "t10.model='pjCalendar' AND t10.foreign_id=t1.calendar_id AND t10.locale=t1.locale_id AND t10.field='payment_sms_admin'", 'left outer')
				->join('pjMultiLang', "t11.model='pjCalendar' AND t11.foreign_id=t1.calendar_id AND t11.locale=t1.locale_id AND t11.field='title'", 'left outer')
				->find($booking_id)
				->getData();
				
			$booking_arr['bs_arr'] = $pjBookingSlotModel
				->reset()
				->join('pjBooking', 't2.id=t1.booking_id', 'inner')
				->where('t1.booking_id', $booking_id)
				->findAll()
				->getData();

			#$bs_ids = $pjBookingSlotModel->getDataPair('id', null);
				
			pjFrontEnd::pjActionConfirmSend($this->option_arr, $booking_arr, 'confirm');
			# Confirmation email(s)
			
			$_SESSION[$this->defaultCart] = NULL;
			unset($_SESSION[$this->defaultCart]);
			
			$_SESSION[$this->defaultForm] = NULL;
			unset($_SESSION[$this->defaultForm]);
			
			$_SESSION[$this->defaultCaptcha] = NULL;
			unset($_SESSION[$this->defaultCaptcha]);
			
			pjAppController::jsonResponse(array(
				'status' => 'OK',
				'code' => 200,
				'text' => 'Order has been stored.',
				'booking_uuid' => $data['uuid'],
				'payment_method' => @$data['payment_method']
			));
		}
		exit;
	}
}
?>