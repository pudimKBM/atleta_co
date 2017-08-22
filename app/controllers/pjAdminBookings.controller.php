<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminBookings extends pjAdmin
{
	public function pjActionCheckTimeSlots()
	{
		$this->setAjax(true);
		
		$pjBookingSlotModel = pjBookingSlotModel::factory();
		if (isset($_SESSION[$this->adminBooking]) && isset($_SESSION[$this->adminBooking][$_GET['hash']]) && !empty($_SESSION[$this->adminBooking][$_GET['hash']]))
		{
			foreach ($_SESSION[$this->adminBooking][$_GET['hash']] as $key => $slot)
			{
				$t_arr = pjAppController::getDailySlots($_GET['calendar_id'], $slot['booking_date'], $this->option_arr);
				$cnt = $pjBookingSlotModel
					->reset()
					->join('pjBooking', 't2.id=t1.booking_id', 'inner')
					->where('t2.calendar_id', $_GET['calendar_id'])
					->where('t2.booking_status !=', 'cancelled')
					->where('t1.booking_date', $slot['booking_date'])
					->where('t1.start_time', $slot['start_time'])
					->where('t1.end_time', $slot['end_time'])
					->where('t1.start_ts', $slot['start_ts'])
					->where('t1.end_ts', $slot['end_ts'])
					->findCount()
					->getData();
				
				if($cnt > 0 && $cnt > (int) $t_arr['slot_limit'])
				{
					echo 'false';
					exit;
				}
			}
			echo 'true';
		}else{
			echo 'true';
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['booking_create']))
			{
				$pjBookingSlotModel = pjBookingSlotModel::factory();
				if (isset($_SESSION[$this->adminBooking]) && isset($_SESSION[$this->adminBooking][$_POST['hash']]) && !empty($_SESSION[$this->adminBooking][$_POST['hash']]))
				{
					foreach ($_SESSION[$this->adminBooking][$_POST['hash']] as $key => $slot)
					{
						$t_arr = pjAppController::getDailySlots($_POST['calendar_id'], $slot['booking_date'], $this->option_arr);
						$cnt = $pjBookingSlotModel
							->reset()
							->join('pjBooking', 't2.id=t1.booking_id', 'inner')
							->where('t2.calendar_id', $_POST['calendar_id'])
							->where('t2.booking_status !=', 'cancelled')
							->where('t1.booking_date', $slot['booking_date'])
							->where('t1.start_time', $slot['start_time'])
							->where('t1.end_time', $slot['end_time'])
							->where('t1.start_ts', $slot['start_ts'])
							->where('t1.end_ts', $slot['end_ts'])
							->findCount()
							->getData();
						if($cnt > 0 && $cnt > (int) $t_arr['slot_limit'])
						{
							pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=ABK04");
						}							
					}
				}
				
				$pjBookingModel = pjBookingModel::factory();
				$required = array('uuid', 'booking_status', 'booking_price', 'booking_tax', 'booking_total', 'booking_deposit');
				$isPaymentDisabled = (int) $this->option_arr['o_disable_payments'] === 1;
				if (!$isPaymentDisabled)
				{
					$required[] = 'payment_method';
				}
				if (!$pjBookingModel->validateRequest($required, $_POST))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=ABK15");
				}
				
				$data = array();
				$data['locale_id'] = $this->getLocaleId();
				$data['ip'] = pjUtil::getClientIp();
				if ($isPaymentDisabled || (isset($_POST['payment_method']) && $_POST['payment_method'] != "creditcard"))
				{
					$data['cc_type'] = ':NULL';
					$data['cc_num'] = ':NULL';
					$data['cc_code'] = ':NULL';
					$data['cc_exp_year'] = ':NULL';
					$data['cc_exp_month'] = ':NULL';
				}
				if ($isPaymentDisabled)
				{
					$data['payment_method'] = ':NULL';
				}
				$id = pjBookingModel::factory(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					if (isset($_SESSION[$this->adminBooking]) && isset($_SESSION[$this->adminBooking][$_POST['hash']]) && !empty($_SESSION[$this->adminBooking][$_POST['hash']]))
					{
						$pjBookingSlotModel = pjBookingSlotModel::factory();
						$pjBookingSlotModel->setBatchFields(array('booking_id', 'booking_date', 'start_time', 'end_time', 'start_ts', 'end_ts', 'price'));
						foreach ($_SESSION[$this->adminBooking][$_POST['hash']] as $key => $slot)
						{
							$pjBookingSlotModel->addBatchRow(array(
								$id, $slot['booking_date'], $slot['start_time'], $slot['end_time'], $slot['start_ts'], $slot['end_ts'], $slot['price']
							));
						}
						$pjBookingSlotModel->insertBatch();
					}
					
					$invoice_arr = $this->pjActionGenerateInvoice($id);
					
					$err = 'ABK03';
				} else {
					$err = 'ABK04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=$err");
			} else {
				$hash = md5(uniqid(rand(), true));
				
				$add_slot = false;
				if(isset($_GET['date']) && isset($_GET['start_ts']) && isset($_GET['end_ts']))
				{
					$date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
					$start_ts = $_GET['start_ts'];
					$end_ts =  $_GET['end_ts'];
					$pjWorkingTimeModel = pjWorkingTimeModel::factory();
					$wt_data = $pjWorkingTimeModel->getWorkingTime($this->getForeignId());
					$wt_arr = $pjWorkingTimeModel->filterDate($wt_data, $date);
					$price = pjAppController::getPrices($this->getForeignId(), $wt_arr, $date, $start_ts, $end_ts, 1);
					
					if (!isset($_SESSION[$this->adminBooking]))
					{
						$_SESSION[$this->adminBooking] = array();
					}
					if (!isset($_SESSION[$this->adminBooking][$hash]))
					{
						$_SESSION[$this->adminBooking][$hash] = array();
					}
					
					$start_time = date("H:i:s", $_GET['start_ts']);
					$key = $date . '~' . $start_time;
					$_SESSION[$this->adminBooking][$hash][$key] = array(
							'booking_date' => $date,
							'start_time' => $start_time,
							'end_time' => date("H:i:s", $end_ts),
							'start_ts' => $start_ts,
							'end_ts' => $end_ts,
							'price' => $price
					);
					$add_slot = true;
				}
				
				$this->set('hash', $hash);
				$this->set('add_slot', $add_slot);
				$this->set('country_arr', pjCountryModel::factory()
					->select('t1.*, t2.content AS `name`')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('t1.status', 'T')
					->orderBy('`name` ASC')
					->findAll()->getData());
				
				$this
					->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
					->appendJs('jquery.noty.packaged.min.js', PJ_THIRD_PARTY_PATH . 'noty/packaged/')
					->appendJs('pjAdminBookings.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$b_arr = pjBookingModel::factory()->find($_GET['id'])->getData();
			if (isset($_GET['id']) && (int) $_GET['id'] > 0 && pjBookingModel::factory()->set('id', $_GET['id'])->erase()->getAffectedRows() == 1)
			{
				pjBookingSlotModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				pjInvoiceModel::factory()->where('order_id', $b_arr['uuid'])->eraseAll();
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
		exit;
	}
	
	public function pjActionDeleteBookingBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['record']) && !empty($_POST['record']))
			{
				$uiid_arr = pjBookingModel::factory()->whereIn('id', $_POST['record'])->findAll()->getDataPair(NULL, 'uuid');
				pjBookingModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjBookingSlotModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				if(!empty($uiid_arr))
				{
					pjInvoiceModel::factory()->whereIn('order_id', $uiid_arr)->eraseAll();
				}
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
		exit;
	}
	
	public function pjActionExportICal()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if(isset($_POST['booking_export']))
			{
				$arr = pjBookingModel::factory()
					->select('t1.*, t2.*')
					->join('pjBookingSlot', 't2.booking_id=t1.id', 'left outer')
					->where('t1.id', $_POST['id'])
					->findAll()
					->getData();
				foreach($arr as $k => $v)
				{
					$v['date_from'] = $v['booking_date'] . ' ' . date('H:i:s', $v['start_ts']);
					$v['date_to'] = $v['booking_date'] . ' ' . date('H:i:s', $v['end_ts']);
					$_arr = array();
					$_arr[] = $v['customer_name'];
					if(!empty($v['customer_email']))
					{
						$_arr[] = 'Email: ' . pjSanitize::html($v['customer_email']);
					}
					if(!empty($v['customer_phone']))
					{
						$_arr[] = 'Phone: ' . pjSanitize::html($v['customer_phone']);
					}
					if(!empty($v['booking_total']))
					{
						$_arr[] = 'Price: ' . pjSanitize::html($v['booking_total']);
					}
					if(!empty($v['customer_notes']))
					{
						$_arr[] = 'Notes: ' . pjSanitize::html(preg_replace('/\n|\r|\r\n/', ' ', $v['customer_notes']));
					}
					$_arr[] = 'Status: ' . pjSanitize::html($v['booking_status']);
						
					$v['desc'] = join("\; ", $_arr);
					$v['location'] = '';
					$v['summary'] = 'Booking';
					$arr[$k] = $v;
				}
				
				$ical = new pjICal();
				$ical
					->setName("Export-".time().".ics")
					->setProdID('Time Slots Booking Calendar')
					->setSummary('summary')
					->setCName('desc')
					->setLocation('location')
					->setTimezone(pjUtil::getTimezoneName($this->option_arr['o_timezone']))
					->process($arr)
					->download();
			}
			exit;
		}
	}
	
	public function pjActionExport()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
			if(isset($_POST['bookings_export']))
			{
				$pjBookingModel = pjBookingModel::factory()
					->select("t1.*, t2.*,
						AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_num`,
						AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`,
						AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,
						AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`,
						AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`")
					->join('pjBookingSlot', 't2.booking_id=t1.id', 'left outer');
				
				if(isset($_POST['calendar_id']) && (int) $_POST['calendar_id'] > 0)
				{
					$pjBookingModel->where('t1.calendar_id', $_POST['calendar_id']);
				}	
				if($_POST['period'] == 'next')
				{
					$column = 'booking_date';
					$direction = 'ASC';
	
					$where_str = pjUtil::getComingWhere($_POST['coming_period'], $this->option_arr['o_week_start']);
					if($where_str != '')
					{
						$pjBookingModel->where($where_str);
					}
				}else{
					$column = 'created';
					$direction = 'ASC';
					$where_str = pjUtil::getMadeWhere($_POST['made_period'], $this->option_arr['o_week_start']);
					if($where_str != '')
					{
						$pjBookingModel->where($where_str);
					}
				}
	
				$arr= $pjBookingModel
					->orderBy("$column $direction")
					->findAll()
					->getData();
				if($_POST['type'] == 'file')
				{
					$this->setLayout('pjActionEmpty');
	
					if($_POST['format'] == 'csv')
					{
						$csv = new pjCSV();
						$csv
							->setHeader(true)
							->setName("Export-".time().".csv")
							->process($arr)
							->download();
					}
					if($_POST['format'] == 'xml')
					{
						$xml = new pjXML();
						$xml
							->setEncoding('UTF-8')
							->setName("Export-".time().".xml")
							->process($arr)
							->download();
					}
					if($_POST['format'] == 'ical')
					{
						foreach($arr as $k => $v)
						{
							$v['uuid'] = $v['uuid'] . '-' . $k;
							$v['date_from'] = $v['booking_date'] . ' ' . date('H:i:s', $v['start_ts']);
							$v['date_to'] = $v['booking_date'] . ' ' . date('H:i:s', $v['end_ts']);
							$_arr = array();
							$_arr[] = $v['customer_name'];
							if(!empty($v['customer_email']))
							{
								$_arr[] = 'Email: ' . pjSanitize::html($v['customer_email']);
							}
							if(!empty($v['customer_phone']))
							{
								$_arr[] = 'Phone: ' . pjSanitize::html($v['customer_phone']);
							}
							if(!empty($v['booking_total']))
							{
								$_arr[] = 'Price: ' . pjSanitize::html($v['booking_total']);
							}
							if(!empty($v['customer_notes']))
							{
								$_arr[] = 'Notes: ' . pjSanitize::html(preg_replace('/\n|\r|\r\n/', ' ', $v['customer_notes']));
							}
							$_arr[] = 'Status: ' . pjSanitize::html($v['booking_status']);
							
							$v['desc'] = join("\; ", $_arr);
							$v['location'] = '';
							$v['summary'] = 'Booking';
							$arr[$k] = $v;
						}
						
						$ical = new pjICal();
						$ical
							->setName("Export-".time().".ics")
							->setProdID('Time Slots Booking Calendar')
							->setSummary('summary')
							->setCName('desc')
							->setLocation('location')
							->setTimezone(pjUtil::getTimezoneName($this->option_arr['o_timezone']))
							->process($arr)
							->download();
					}
					exit;
				}else{
					$pjPasswordModel = pjPasswordModel::factory();
					$password = md5($_POST['password'].PJ_SALT);
					$arr = $pjPasswordModel
						->where("t1.password", $password)
						->limit(1)
						->findAll()
						->getData();
					if (count($arr) != 1)
					{
						$pjPasswordModel->setAttributes(array('password' => $password))->insert();
					}
					$this->set('password', $password);
				}
			}
			$calendar_arr = pjCalendarModel::factory()
				->select('t1.*, t2.content AS `title`')
				->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->orderBy('t1.id ASC')
				->findAll()
				->getData();
			$this->set('calendar_arr', $calendar_arr);
			
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminBookings.js');
		} else {
			
			$this->set('status', 2);
		}
	}
	
	public function pjActionExportFeed()
	{
		$this->setLayout('pjActionEmpty');
		$access = true;
		if(isset($_GET['p']))
		{
			$pjPasswordModel = pjPasswordModel::factory();
			$arr = $pjPasswordModel
				->where('t1.password', $_GET['p'])
				->limit(1)
				->findAll()
				->getData();
			if (count($arr) != 1)
			{
				$access = false;
			}
		}
		if($access == true)
		{
			$arr = $this->pjGetFeedData($_GET);
			
			if(!empty($arr))
			{
				if($_GET['format'] == 'xml')
				{
					$xml = new pjXML();
					echo $xml
						->setEncoding('UTF-8')
						->process($arr)
						->getData();
						
				}
				if($_GET['format'] == 'csv')
				{
					$csv = new pjCSV();
					echo $csv
						->setHeader(true)
						->process($arr)
						->getData();
						
				}
				if($_GET['format'] == 'ical')
				{
					foreach($arr as $k => $v)
					{
						$v['uuid'] = $v['uuid'] . '-' . $k;
						$v['date_from'] = $v['booking_date'] . ' ' . date('H:i:s', $v['start_ts']);
						$v['date_to'] = $v['booking_date'] . ' ' . date('H:i:s', $v['end_ts']);
						$_arr = array();
						$_arr[] = $v['customer_name'];
						if(!empty($v['customer_email']))
						{
							$_arr[] = 'Email: ' . pjSanitize::html($v['customer_email']);
						}
						if(!empty($v['customer_phone']))
						{
							$_arr[] = 'Phone: ' . pjSanitize::html($v['customer_phone']);
						}
						if(!empty($v['booking_total']))
						{
							$_arr[] = 'Price: ' . pjSanitize::html($v['booking_total']);
						}
						if(!empty($v['customer_notes']))
						{
							$_arr[] = 'Notes: ' . pjSanitize::html(preg_replace('/\n|\r|\r\n/', ' ', $v['customer_notes']));
						}
						$_arr[] = 'Status: ' . pjSanitize::html($v['booking_status']);
						
						$v['desc'] = join("\; ", $_arr);
						$v['location'] = '';
						$v['summary'] = 'Booking';
						$arr[$k] = $v;
					}
					
					$ical = new pjICal();
					echo $ical
						->setProdID('Time Slots Booking Calendar')
						->setSummary('summary')
						->setCName('desc')
						->setLocation('location')
						->setTimezone(pjUtil::getTimezoneName($this->option_arr['o_timezone']))
						->process($arr)
						->getData();
						
				}
			}
		}else{
			__('lblNoAccessToFeed');
		}
		exit;
	}
	public function pjGetFeedData($get)
	{
		$arr = array();
		$status = true;
		$type = '';
		$period = '';
		if(isset($get['period']))
		{
			if(!ctype_digit($get['period']))
			{
				$status = false;
			}else{
				$period = $get['period'];
			}
		}else{
			$status = false;
		}
		if(isset($get['type']))
		{
			if(!ctype_digit($get['type']))
			{
				$status = false;
			}else{
				$type = $get['type'];
			}
		}else{
			$status = false;
		}
		if($status == true && $type != '' && $period != '')
		{
			$pjBookingModel = pjBookingModel::factory()
				->select("t1.*, t2.*,
						AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_num`,
						AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`,
						AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,
						AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`,
						AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`")
				->join('pjBookingSlot', 't2.booking_id=t1.id', 'left outer');

			if(isset($get['cid']) && (int) $get['cid'] > 0)
			{
				$pjBookingModel->where('t1.calendar_id', $get['cid']);
			}
			if($type == '1')
			{
				$column = 'booking_date';
				$direction = 'ASC';
					
				$where_str = pjUtil::getComingWhere($period, $this->option_arr['o_week_start']);
				if($where_str != '')
				{
					$pjBookingModel->where($where_str);
				}
			}else{
				$column = 'created';
				$direction = 'DESC';
				$where_str = pjUtil::getMadeWhere($period, $this->option_arr['o_week_start']);
				if($where_str != '')
				{
					$pjBookingModel->where($where_str);
				}
			}
			$arr= $pjBookingModel
				->orderBy("$column $direction")
				->findAll()
				->getData();
		}
		return $arr;
	}
	
	public function pjActionGetBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjBookingModel = pjBookingModel::factory()->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.calendar_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left');
			$pjBookingSlotModel = pjBookingSlotModel::factory();

			if (isset($_GET['calendar_id']) && (int) $_GET['calendar_id'] > 0)
			{
				$pjBookingModel->where('t1.calendar_id', $_GET['calendar_id']);
			}
			if($this->isEditor())
			{
				$calendar_id_arr = pjCalendarUserModel::factory()->where("user_id", $this->getUserId())->findAll()->getDataPair(null, 'calendar_id');
				if(!empty($calendar_id_arr))
				{
					$pjBookingModel->whereIn('t1.calendar_id', $calendar_id_arr);
				}
			}
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = $pjBookingModel->escapeStr($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjBookingModel->where(sprintf("(t1.uuid LIKE '%1\$s' OR t1.customer_email LIKE '%1\$s' OR t1.customer_name LIKE '%1\$s')", "%$q%"));
			}

			if (isset($_GET['booking_status']) && !empty($_GET['booking_status']) && in_array($_GET['booking_status'], array('confirmed', 'pending', 'cancelled')))
			{
				$pjBookingModel->where('t1.booking_status', $_GET['booking_status']);
			}
			
			if (isset($_GET['date_from']) && isset($_GET['date_to']) && !empty($_GET['date_from']) && !empty($_GET['date_to']))
			{
				$date_from = pjUtil::formatDate($_GET['date_from'], $this->option_arr['o_date_format']);
				$date_to = pjUtil::formatDate($_GET['date_to'], $this->option_arr['o_date_format']);
				$pjBookingModel->where(sprintf("t1.id IN (SELECT `booking_id` FROM `%s` WHERE `booking_date` BETWEEN '%s' AND '%s')", $pjBookingSlotModel->getTable(), $date_from, $date_to));
			} else {
				if (isset($_GET['date_from']) && !empty($_GET['date_from']))
				{
					$date_from = pjUtil::formatDate($_GET['date_from'], $this->option_arr['o_date_format']);
					$pjBookingModel->where(sprintf("t1.id IN (SELECT `booking_id` FROM `%s` WHERE `booking_date` >= '%s')", $pjBookingSlotModel->getTable(), $date_from));
				}
				if (isset($_GET['date_to']) && !empty($_GET['date_to']))
				{
					$date_to = pjUtil::formatDate($_GET['date_to'], $this->option_arr['o_date_format']);
					$pjBookingModel->where(sprintf("t1.id IN (SELECT `booking_id` FROM `%s` WHERE `booking_date` <= '%s')", $pjBookingSlotModel->getTable(), $date_to));
				}
			}
			
			$column = 'id';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjBookingModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjBookingModel
				->select(sprintf("t1.*, t2.content as calendar_title, AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
								AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`,
								AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,
								AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`,
								AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`,
					(SELECT GROUP_CONCAT(CONCAT_WS('~.~', bs.booking_date,
							DATE_FORMAT(FROM_UNIXTIME(bs.start_ts), '%%Y-%%m-%%d %%H:%%i:%%s'),
							DATE_FORMAT(FROM_UNIXTIME(bs.end_ts), '%%Y-%%m-%%d %%H:%%i:%%s')) SEPARATOR '~:~')
						FROM `%1\$s` AS `bs`
						WHERE bs.booking_id = t1.id) AS `items`
					", $pjBookingSlotModel->getTable(), pjMultiLangModel::factory()->getTable(), $this->getLocaleId()))
				->orderBy("$column $direction")->limit($rowCount, $offset)
				->findAll()
				->toArray('items', '~:~')
				->getData();

			foreach ($data as $k => $v)
			{
				foreach ($data[$k]['items'] as $key => $val)
				{
					$tmp = explode('~.~', $val);
					$tmp[0] = date($this->option_arr['o_date_format'], strtotime($tmp[0]));
					$tmp[1] = date($this->option_arr['o_time_format'], strtotime($tmp[1]));
					$tmp[2] = date($this->option_arr['o_time_format'], strtotime($tmp[2]));
					$data[$k]['items'][$key] = join("~.~", $tmp);
				}
				$data[$k]['customer_name'] = pjSanitize::html($v['customer_name']);
				$data[$k]['customer_email'] = pjSanitize::html($v['customer_email']);
				$data[$k]['customer_phone'] = pjSanitize::html($v['customer_phone']);
				$data[$k]['total_formated'] = pjUtil::formatCurrencySign(number_format($v['booking_total'], 2), $this->option_arr['o_currency']);
			}
				
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionGetPrice()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			$price = $deposit = $tax = $total = 0;
			$has_slot = 0;
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				$bs_arr = pjBookingSlotModel::factory()->where('t1.booking_id', $_POST['id'])->findAll()->getData();
				foreach ($bs_arr as $slot)
				{
					$price += $slot['price'];
					$has_slot = 1;
				}
			} elseif (isset($_POST['hash']) && !empty($_POST['hash']) && isset($_SESSION[$this->adminBooking]) && isset($_SESSION[$this->adminBooking][$_POST['hash']])) {
				foreach ($_SESSION[$this->adminBooking][$_POST['hash']] as $key => $slot)
				{
					$price += $slot['price'];
					$has_slot = 1;
				}
			}
			
			if ((float) $this->option_arr['o_tax'] > 0)
			{
				$tax = ($price * (float) $this->option_arr['o_tax']) / 100;
			}
			
			$total = $price + $tax;
			
			switch ($this->option_arr['o_deposit_type'])
			{
				case 'percent':
					$deposit = ($total * (float) $this->option_arr['o_deposit']) / 100;
					break;
				case 'amount':
					$deposit = (float) $this->option_arr['o_deposit'];
					break;
			}
			
			$data = compact('price', 'deposit', 'tax', 'total', 'has_slot');
			$data = array_map('floatval', $data);
			
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Price has been updated', 'data' => $data));
		}
		exit;
	}
	
	public function pjActionGetSlots()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['date']) && !empty($_GET['date']))
			{
				$iso_date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
				$this->getSlots($_GET['calendar_id'], $iso_date, isset($_GET['hash']) ? $_GET['hash'] : NULL);
			}
		}
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminBookings.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjBookingModel = pjBookingModel::factory();
			if (!in_array($_POST['column'], $pjBookingModel->getI18n()))
			{
				$pjBookingModel->set('id', $_GET['id'])->modify(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjBooking');
			}
		}
		exit;
	}
	
	private function __getSchedule($iso_date, $calendar_id)
	{
		$arr = array();
		foreach (range(0,4) as $i)
		{
			$arr[date("Y-m-d", strtotime("+$i day", strtotime($iso_date)))] = array();
		}
		
		$pjBookingSlotModel = pjBookingSlotModel::factory();
		if((int) $calendar_id > 0)
		{
			$pjBookingSlotModel->where('t2.calendar_id', $calendar_id);
		}else{
			if($this->isEditor())
			{
				$pjBookingSlotModel->where("(t2.calendar_id IN (SELECT TCU.calendar_id FROM `".pjCalendarUserModel::factory()->getTable()."` AS TCU WHERE TCU.user_id=".$this->getUserId().") )");
			}
		}
		$tmp = $pjBookingSlotModel
			->select('t1.*, t2.uuid, t2.customer_name, t3.content as calendar_title')
			->join('pjBooking', "t2.id=t1.booking_id", 'inner')
			->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t2.calendar_id AND t3.field='title' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
			->where(sprintf("t1.booking_date BETWEEN '%1\$s' AND ADDDATE('%1\$s', 4)", $iso_date))
			->where('t2.booking_status !=', 'cancelled')
			->orderBy('t1.booking_date ASC, t1.start_ts ASC')
			->findAll()
			->getData();
			
		foreach ($tmp as $booking)
		{
			$arr[$booking['booking_date']][] = $booking;
		}

		if($this->isEditor())
		{
			$calendars = pjCalendarModel::factory()
				->select('t1.*, t2.content AS `title`')
				->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->where("(t1.id IN (SELECT TCU.calendar_id FROM `".pjCalendarUserModel::factory()->getTable()."` AS TCU WHERE TCU.user_id=".$this->getUserId().") )")
				->orderBy('t1.id ASC')
				->findAll()->getData();
		}else{
			$calendars = pjCalendarModel::factory()
				->select('t1.*, t2.content AS `title`')
				->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->orderBy('t1.id ASC')
				->findAll()->getData();
		}
				
		$t_arr = array();
		foreach ($arr as $date => $bookings)
		{
			if((int) $calendar_id > 0)
			{
				$daily_slots = pjAppController::getDailySlots($calendar_id, $date, $this->option_arr);
				$t_arr[$date] = $daily_slots;
			}else{
				foreach($calendars as $calendar)
				{
					$daily_slots = pjAppController::getDailySlots($calendar['id'], $date, $this->option_arr);
					
					if(!isset($t_arr[$date]))
					{
						$t_arr[$date] = $daily_slots;
					}else{
						$start = strtotime($t_arr[$date]['start_hour'].":".$t_arr[$date]['start_minutes'].":00");
						$end = strtotime($t_arr[$date]['end_hour'].":".$t_arr[$date]['end_minutes'].":00");
						
						$_start = strtotime($daily_slots['start_hour'].":".$daily_slots['start_minutes'].":00");
						$_end = strtotime($daily_slots['end_hour'].":".$daily_slots['end_minutes'].":00");
						
						if($_start < $start)
						{
							$t_arr[$date]['start_hour'] = $daily_slots['start_hour'];
							$t_arr[$date]['start_minutes'] = $daily_slots['start_minutes'];
						}
						if($_end > $end)
						{
							$t_arr[$date]['end_hour'] = $daily_slots['end_hour'];
							$t_arr[$date]['end_minutes'] = $daily_slots['end_minutes'];
						}
						if($daily_slots['is_dayoff'] == 'F')
						{
							$t_arr[$date]['is_dayoff'] = 'F';
						}
					}
				}
			}
		}
		
		$this->set('arr', $arr);
		$this->set('t_arr', $t_arr);
	}
	
	public function pjActionGetSchedule()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$this->__getSchedule($_GET['date'], $_GET['calendar_id']);
		}
	}
	
	public function pjActionSchedule()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('pjAdminBookings.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSchedulePrint()
	{
		$this->setLayout('pjActionPrint');
		
		$this->__getSchedule($_GET['date'], $_GET['calendar_id']);
	}
	
	public function pjActionSave()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (!isset($_POST['pk']) || !isset($_POST['name']) || !isset($_POST['value']) || empty($_POST['name']) || (int) $_POST['pk'] <= 0)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing, invalid or empty parameters.'));
			}
			
			$pjBookingModel = pjBookingModel::factory();
			if (!in_array($_POST['name'], $pjBookingModel->getI18n()))
			{
				if ($pjBookingModel->set('id', $_POST['pk'])->modify(array($_POST['name'] => $_POST['value']))->getAffectedRows() == 1)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'All changes have been saved.'));
				} else {
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Sorry, value was not saved.'));
				}
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['name'] => $_POST['value'])), $_POST['pk'], 'pjBooking');
			}
			
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'All changes have been saved.'));
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$pjBookingModel = pjBookingModel::factory();
			if (isset($_REQUEST['id']) && (int) $_REQUEST['id'] > 0)
			{
				$pjBookingModel->where('t1.id', $_REQUEST['id']);
			} elseif (isset($_GET['uuid']) && !empty($_GET['uuid'])) {
				$pjBookingModel->where('t1.uuid', $_GET['uuid']);
			}
			$arr = $pjBookingModel
				->limit(1)
				->findAll()
				->getData();
				
			if (empty($arr))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=ABK08");
			}
			$arr = $arr[0];
			
			if (isset($_POST['booking_update']))
			{
				$required = array('id', 'uuid', 'booking_status', 'booking_price', 'booking_tax', 'booking_total', 'booking_deposit');
				$isPaymentDisabled = (int) $this->option_arr['o_disable_payments'] === 1;
				if (!$isPaymentDisabled)
				{
					$required[] = 'payment_method';
				}
				if (!$pjBookingModel->validateRequest($required, $_POST))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=ABK15");
				}

				$data = array();
				if ($isPaymentDisabled || (isset($_POST['payment_method']) && $_POST['payment_method'] != "creditcard"))
				{
					$data['cc_type'] = ':NULL';
					$data['cc_num'] = ':NULL';
					$data['cc_code'] = ':NULL';
					$data['cc_exp_year'] = ':NULL';
					$data['cc_exp_month'] = ':NULL';
				}
				if ($isPaymentDisabled)
				{
					$data['payment_method'] = ':NULL';
				}
				$data['modified'] = date('Y-m-d H:i:s');
				
				$pjBookingModel = pjBookingModel::factory();
				
				$arr = $pjBookingModel->find($_POST['id'])->getData();
				$pjInvoiceModel = pjInvoiceModel::factory();
				$_arr = $pjInvoiceModel->where('t1.order_id', $arr['uuid'])->limit(1)->findAll()->getData();
				$_arr = $_arr[0];
				$pjInvoiceModel->reset()->set('id', $_arr['id'])->modify(array('order_id'=>$_POST['uuid']));
				
				$pjBookingModel->reset()->set('id', $_POST['id'])->modify(array_merge($_POST, $data));
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminBookings&action=pjActionIndex&err=ABK01");
				
			} else {
				$this->set('arr', $arr)
					->set('country_arr', pjCountryModel::factory()
						->select('t1.*, t2.content AS `name`')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`name` ASC')
						->findAll()->getData());
				
				$this->set('bi_arr', pjBookingSlotModel::factory()
					->where('t1.booking_id', $arr['id'])
					->findAll()
					->getData()
				);
				
				$this
					->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
					->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')
					->appendJs('jquery.noty.packaged.min.js', PJ_THIRD_PARTY_PATH . 'noty/packaged/')
					->appendJs('pjAdminBookings.js')
				;
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionItemAdd()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			$pjBookingSlotModel = pjBookingSlotModel::factory();
			$pjBsModel = pjBookingSlotModel::factory();
			
			if (isset($_POST['item_add']))
			{
				if (isset($_POST['timeslot']) && !empty($_POST['timeslot']))
				{
					$date = pjUtil::formatDate($_POST['date'], $this->option_arr['o_date_format']);
				
					if (isset($_POST['booking_id']) && (int) $_POST['booking_id'] > 0)
					{
						$pjBookingSlotModel->setBatchFields(array(
							'booking_id', 'booking_date', 'start_time',
							'end_time', 'start_ts', 'end_ts', 'price'
						));
						$duplicated = false;
						foreach ($_POST['timeslot'] as $start_ts => $end_ts)
						{
							$cnt = $pjBsModel
								->reset()
								->where('t1.booking_id', $_POST['booking_id'])
								->where('t1.booking_date', $date)
								->where('t1.start_time', date("H:i:s", $start_ts))
								->where('t1.end_time', date("H:i:s", $end_ts))
								->where('t1.start_ts', $start_ts)
								->where('t1.end_ts', $end_ts)
								->findCount()
								->getData();
							if($cnt > 0)
							{
								$duplicated = true;
								break;
							}
							$pjBookingSlotModel->addBatchRow(array(
								$_POST['booking_id'],
								$date,
								date("H:i:s", $start_ts),
								date("H:i:s", $end_ts),
								$start_ts,
								$end_ts,
								$_POST['price'][$start_ts]
							));
						}
						if($duplicated == false)
						{
							$pjBookingSlotModel->insertBatch();
						}else{
							pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => __('lblDuplidatedSlot', true)));
						}
					} elseif (isset($_POST['hash']) && !empty($_POST['hash'])) {
						
						if (!isset($_SESSION[$this->adminBooking]))
						{
							$_SESSION[$this->adminBooking] = array();
						}
						if (!isset($_SESSION[$this->adminBooking][$_POST['hash']]))
						{
							$_SESSION[$this->adminBooking][$_POST['hash']] = array();
						}
						
						foreach ($_POST['timeslot'] as $start_ts => $end_ts)
						{
							$start_time = date("H:i:s", $start_ts);
							$key = $date . '~' . $start_time;
							$_SESSION[$this->adminBooking][$_POST['hash']][$key] = array(
								'booking_date' => $date,
								'start_time' => $start_time,
								'end_time' => date("H:i:s", $end_ts),
								'start_ts' => $start_ts,
								'end_ts' => $end_ts,
								'price' => $_POST['price'][$start_ts]
							);
						}
					}
					
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Booking slot(s) has been added.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Time slots couldn\'t be empty.'));
			}
		}
	}
	
	public function pjActionItemDelete()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				$pjBookingSlotModel = pjBookingSlotModel::factory();
				$arr = $pjBookingSlotModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Slot not found.'));
				}
				if (1 == $pjBookingSlotModel->set('id', $_POST['id'])->erase()->getAffectedRows())
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Slot has been deleted.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Slot has not been deleted.'));
			} elseif (isset($_POST['hash']) && !empty($_POST['hash']) && isset($_POST['key']) && !empty($_POST['key'])) {
				if (isset($_SESSION[$this->adminBooking]) &&
					isset($_SESSION[$this->adminBooking][$_POST['hash']]) &&
					isset($_SESSION[$this->adminBooking][$_POST['hash']][$_POST['key']])
				)
				{
					$_SESSION[$this->adminBooking][$_POST['hash']][$_POST['key']] = NULL;
					unset($_SESSION[$this->adminBooking][$_POST['hash']][$_POST['key']]);
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Slot has been deleted.'));
				} else {
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Slot not found.'));
				}
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing parameters.'));
		}
		pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Access denied.'));
		exit;
	}
	
	public function pjActionItemGet()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$bi_arr = pjBookingSlotModel::factory()
					->select("t1.*")
					->join('pjBooking', 't2.id=t1.booking_id', 'inner')
					->where('t1.booking_id', $_GET['id'])->findAll()->getData();
			} elseif (isset($_GET['hash']) && !empty($_GET['hash'])) {
				$bi_arr = $_SESSION[$this->adminBooking][$_GET['hash']];
			}
			
			$this->set('bi_arr', $bi_arr);
		}
	}
	
	public function pjActionReminderEmail()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['send_email']) && isset($_POST['to']) && !empty($_POST['to']) && !empty($_POST['from']) &&
				!empty($_POST['subject']) && !empty($_POST['message']) && !empty($_POST['id']))
			{
				$Email = new pjEmail();
				$Email->setContentType('text/html');
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$Email
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
						->setSender($this->option_arr['o_smtp_user']);
				}
				$user_arr = pjUserModel::factory()->where('t1.status', 'T')->where("(t1.notify_email LIKE '%reminder%')")->findAll()->getDataPair('id', 'email');
				
				$recipient_arr = array_merge($_POST['to'], $user_arr);
				foreach ($recipient_arr as $recipient)
				{
					$r = $Email
						->setTo($recipient)
						->setFrom($_POST['from'])
						->setSubject($_POST['subject'])
						->send(pjUtil::textToHtml($_POST['message']));
				}
					
				if (isset($r) && $r)
				{
					pjBookingModel::factory()->set('id', $_POST['id'])->modify(array('reminder_email' => 1));
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email failed to send.'));
			}
			
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$booking_arr = pjBookingModel::factory()
					->select('t1.*, t1.id AS `booking_id`, t2.content AS `country_name`,
						t3.content AS `reminder_subject_client`, t4.content AS `reminder_tokens_client`,
						t6.email AS `admin_email`, t7.content AS `calendar_name`')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.customer_country AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t1.calendar_id AND t3.locale=t1.locale_id AND t3.field='reminder_subject_client'", 'left outer')
					->join('pjMultiLang', "t4.model='pjCalendar' AND t4.foreign_id=t1.calendar_id AND t4.locale=t1.locale_id AND t4.field='reminder_tokens_client'", 'left outer')
					->join('pjCalendar', 't5.id=t1.calendar_id', 'left outer')
					->join('pjUser', 't6.id=t5.user_id', 'left outer')
					->join('pjMultiLang', "t7.model='pjCalendar' AND t7.foreign_id=t1.calendar_id AND t7.locale=t1.locale_id AND t7.field='title'", 'left outer')
					->find($_GET['id'])
					->getData();
						
				if (!empty($booking_arr))
				{
					$booking_arr['bs_arr'] = pjBookingSlotModel::factory()->where('t1.booking_id', $_GET['id'])->findAll()->getData();
				
					$tokens = pjAppController::getTokens($booking_arr, $this->option_arr);
					
					$subject_client = str_replace($tokens['search'], $tokens['replace'], $booking_arr['reminder_subject_client']);
					$message_client = str_replace($tokens['search'], $tokens['replace'], $booking_arr['reminder_tokens_client']);
					$from = !empty($booking_arr['admin_email']) ? $booking_arr['admin_email'] : $booking_arr['customer_email'];
					if(!empty($this->option_arr['o_from_email']))
					{
						$from = $this->option_arr['o_from_email'];
					}
					$this->set('arr', array(
						'id' => $_GET['id'],
						'client_email' => $booking_arr['customer_email'],
						'client_name' => $booking_arr['customer_name'],
						'from' => $from,
						'message' => $message_client,
						'subject' => $subject_client
					));
				}
			} else {
				exit;
			}
		}
	}
	
	public function pjActionReminderSms()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['send_sms']) && isset($_POST['to']) && !empty($_POST['to']) && !empty($_POST['message']) && !empty($_POST['id']))
			{
				$params = array(
					'text' => $_POST['message'],
					'key' => md5($this->option_arr['private_key'] . PJ_SALT),
					'type' => 'unicode'
				);
				
				foreach ($_POST['to'] as $recipient)
				{
					$params['number'] = $recipient;
					$result = $this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
				}

				if (isset($result) && (int) $result === 1)
				{
					pjBookingModel::factory()->set('id', $_POST['id'])->modify(array('reminder_sms' => 1));
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'SMS has been sent.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'SMS failed to send.'));
			}
			
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$booking_arr = pjBookingModel::factory()
					->select('t1.*, t3.email AS `admin_email`, t4.content AS `country_name`, t5.content AS `reminder_sms_client`, t6.content AS `calendar_name`')
					->join('pjCalendar', 't2.id=t1.calendar_id', 'inner')
					->join('pjUser', 't3.id=t2.user_id', 'left outer')
					->join('pjMultiLang', "t4.model='pjCountry' AND t4.foreign_id=t1.customer_country AND t4.locale=t1.locale_id AND t4.field='name'", 'left outer')
					->join('pjMultiLang', "t5.model='pjCalendar' AND t5.foreign_id=t2.id AND t5.locale=t1.locale_id AND t5.field='reminder_sms_client'", 'left outer')
					->join('pjMultiLang', "t6.model='pjCalendar' AND t6.foreign_id=t1.calendar_id AND t6.locale=t1.locale_id AND t6.field='title'", 'left outer')
					->find($_GET['id'])
					->getData();

				if (!empty($booking_arr))
				{
					$booking_arr['bs_arr'] = pjBookingSlotModel::factory()->where('t1.booking_id', $_GET['id'])->findAll()->getData();
					
					$tokens = pjAppController::getTokens($booking_arr, $this->option_arr);
					
					$message_client = str_replace($tokens['search'], $tokens['replace'], $booking_arr['reminder_sms_client']);
					
					$this->set('arr', array(
						'id' => $_GET['id'],
						'client_phone' => pjUtil::formatPhone($booking_arr['customer_phone']),
						'client_name' => pjSanitize::html($booking_arr['customer_name']),
						'message' => $message_client
					));
				}
			} else {
				exit;
			}
		}
	}
}
?>