<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminCalendars extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin() && !$this->isEditor())
		{
			$this->set('status', 2);
			return;
		}

		if (isset($_POST['calendar_create']))
		{
			$data = $required = array();
			if ($this->isEditor())
			{
				$data['user_id'] = $this->getUserId();
			} else {
				$required[] = 'user_id';
			}
			$data = array_merge($_POST, $data);
			
			$pjCalendarModel = pjCalendarModel::factory();
			if (!$pjCalendarModel->validateRequest($required, $data))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCalendars&action=pjActionIndex&err=AC13");
			}

			if (!$pjCalendarModel->validates($data))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCalendars&action=pjActionIndex&err=AC13");
			}
			
			$id = $pjCalendarModel->setAttributes($data)->insert()->getInsertId();
			if ($id !== false && (int) $id > 0)
			{
				$err = 'AC03';
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjCalendar');
				}
				
				if(is_array($_POST['user_id']) && !empty($_POST['user_id']))
				{
					$pjCalendarUserModel = pjCalendarUserModel::factory();
					foreach ($_POST['user_id'] as $user_id)
					{
						$pjCalendarUserModel->addBatchRow(array($id, $user_id));
					}
					$pjCalendarUserModel->setBatchFields(array('calendar_id', 'user_id'))->insertBatch();
				}
				
				$pjMultiLangModel = pjMultiLangModel::factory();
				$i18n_arr = $pjMultiLangModel->getMultiLang(0, 'pjCalendar');
				unset($i18n_arr[1]['title']);
				if (isset($_POST['i18n']))
				{
					foreach ($_POST['i18n'] as $locale => $locale_arr)
					{
						$i18n_arr[$locale] = $i18n_arr[1];
					}
					$pjMultiLangModel->reset()->saveMultiLang($i18n_arr, $id, 'pjCalendar');
				}else{
					$pjMultiLangModel->reset()->saveMultiLang($i18n_arr, $id, 'pjCalendar');
				}
				
				$pjWorkingTimeModel = pjWorkingTimeModel::factory();
				$init_wt_arr = $pjWorkingTimeModel->where('foreign_id', 0)->limit(1)->findAll()->getData();
				$wt_data = $init_wt_arr[0];
				unset($wt_data['id']);
				$wt_data['foreign_id'] = $id;
				$pjWorkingTimeModel->reset()->setAttributes($wt_data)->insert();
					
				$pjOptionModel = pjOptionModel::factory();
				$init_option_arr = $pjOptionModel->where('foreign_id', 0)->findAll()->getData();
				$pjOptionModel->reset()->setBatchFields(array('foreign_id', 'key', 'tab_id', 'value', 'label', 'type', 'order', 'is_visible', 'style'));
				foreach ($init_option_arr as $record)
				{
					$record['foreign_id'] = $id;
					$pjOptionModel->addBatchRow($record);
				}
				$pjOptionModel->insertBatch();
							
			} else {
				$err = 'AC04';
			}
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCalendars&action=pjActionIndex&err=$err");
		} else {
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
					
			$lp_arr = array();
			foreach ($locale_arr as $item)
			{
				$lp_arr[$item['id']."_"] = $item['file'];
			}
			$this->set('lp_arr', $locale_arr);
			
			$this->set('user_arr', pjUserModel::factory()->orderBy('t1.name ASC')->findAll()->getData());
	
			$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
			$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			if ((int) $this->option_arr['o_multi_lang'] === 1)
			{
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			}
			$this->appendJs('pjAdminCalendars.js');
		}
	}
	
	public function pjActionDeleteCalendar()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				if ($_GET['id'] == $this->getForeignId())
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Current calendar can not be deleted.'));
				}
				
				if (pjCalendarModel::factory()->set('id', $_GET['id'])->erase()->getAffectedRows() == 1)
				{
					pjMultiLangModel::factory()->where('model', 'pjCalendar')->where('foreign_id', $_GET['id'])->eraseAll();
					pjDateModel::factory()->where('foreign_id', $_GET['id'])->eraseAll();
					pjPriceModel::factory()->where('calendar_id', $_GET['id'])->eraseAll();
					pjPriceDayModel::factory()->where('calendar_id', $_GET['id'])->eraseAll();
					pjWorkingTimeModel::factory()->where('foreign_id', $_GET['id'])->limit(1)->eraseAll();
					pjOptionModel::factory()->where('foreign_id', $_GET['id'])->eraseAll();
					pjCalendarUserModel::factory()->where('calendar_id', $_GET['id'])->eraseAll();
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Calendar has been deleted.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Calendar has not been deleted.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing or empty params.'));
		}
		exit;
	}
	
	public function pjActionDeleteCalendarBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['record']) && !empty($_POST['record']))
			{
				$cnt = count($_POST['record']);
				pjCalendarModel::factory()
					->where('id !=', $this->getForeignId())
					->whereIn('id', $_POST['record'])
					->limit($cnt)
					->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjCalendar')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				pjDateModel::factory()->whereIn('foreign_id', $_POST['record'])->eraseAll();
				pjPriceModel::factory()->whereIn('calendar_id', $_POST['record'])->eraseAll();
				pjPriceDayModel::factory()->whereIn('calendar_id', $_POST['record'])->eraseAll();
				pjCalendarUserModel::factory()->whereIn('calendar_id', $_POST['record'])->eraseAll();
				pjWorkingTimeModel::factory()->whereIn('foreign_id', $_POST['record'])->limit($cnt)->eraseAll();
				pjOptionModel::factory()->whereIn('foreign_id', $_POST['record'])->eraseAll();
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Calendar(s) has been deleted.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing or empty params.'));
		}
		exit;
	}
	
	private function __getCalendar($cid, $year, $month, $view=1)
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
				
			if ($timestamp < strtotime(date('Y-m-d 00:00:00', time())))
			{
				$can_added = false;
			} elseif ($dateoff[$iso_date]['is_dayoff'] == 'T') {
				$can_added = false;
			} elseif (isset($daysoff[$weekday])) {
				$can_added = false;
			}
			if(isset($dateoff[$iso_date]) && $dateoff[$iso_date]['is_dayoff'] == 'F')
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
		
		$pjTSBCalendar
			->setPrevLink("&nbsp;")
			->setNextLink("&nbsp;")
			->setStartDay($this->option_arr['o_week_start'])
			->setWeekNumbers($this->option_arr['o_show_week_numbers'] == 1 ? 'left' : NULL)
			->setDayNames(__('day_names', true))
			->setMonthNames(__('months', true))
			->setShowTooltip(true)
			->set('options', $this->option_arr)
			->set('dayoff', $daysoff)
			->set('dateoff', $dateoff)
			->set('monthStatus', $monthStatus)
			->set('dates', $dates);

		$this->set('TSBCalendar', $pjTSBCalendar);
	}
	
	public function pjActionGetCal()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			$this->__getCalendar($this->getForeignId(), $_GET['year'], $_GET['month']);
		}
	}
	
	public function pjActionGetCalendar()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjCalendarModel = pjCalendarModel::factory()
				->join('pjMultiLang', sprintf("t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='%u'", $this->getLocaleId()), 'left outer')
			;

			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = $pjCalendarModel->escapeStr(trim($_GET['q']));
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjCalendarModel->where("((t2.content LIKE '%$q%') OR (t1.id IN (SELECT TCU.calendar_id FROM `".pjCalendarUserModel::factory()->getTable()."` AS TCU WHERE TCU.user_id IN (SELECT TU.id FROM `".pjUserModel::factory()->getTable()."` AS TU WHERE TU.name LIKE '%$q%%' OR TU.email LIKE '%$q%%') )) )");
				
			}
			if ($this->isAdmin())
			{
				if (isset($_GET['user_id']) && (int) $_GET['user_id'] > 0)
				{
					$pjCalendarModel->where("(t1.id IN (SELECT TCU.calendar_id FROM `".pjCalendarUserModel::factory()->getTable()."` AS TCU WHERE TCU.user_id=".$_GET['user_id']."))");
				}
			}
			
			if ($this->isEditor())
			{
				$pjCalendarModel->where("(t1.id IN (SELECT TCU.calendar_id FROM `".pjCalendarUserModel::factory()->getTable()."` AS TCU WHERE TCU.user_id=".$this->getUserId()."))");
			}

			$column = '`title`';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjCalendarModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjCalendarModel
				->select('t1.id, t2.content AS `title`')
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			$email_arr = $name_arr = array();
			$calendar_id_arr = $pjCalendarModel->findAll()->getDataPair(null, 'id');
			if(!empty($calendar_id_arr))
			{
				$user_arr = pjUserModel::factory()
					->select('t1.*, t2.calendar_id')
					->join('pjCalendarUser', 't1.id=t2.user_id', 'inner')
					->whereIn('calendar_id', $calendar_id_arr)
					->findAll()->getData();
				foreach($user_arr as $k => $v)
				{
					if(isset($email_arr[$v['calendar_id']]))
					{
						if(!in_array($v['email'], $email_arr[$v['calendar_id']]))
						{
							$email_arr[$v['calendar_id']][] = $v['email'];
						}
					}else{
						$email_arr[$v['calendar_id']][] = $v['email'];
					}
					if(isset($name_arr[$v['calendar_id']]))
					{
						if(!in_array($v['name'], $name_arr[$v['calendar_id']]))
						{
							$name_arr[$v['calendar_id']][] = $v['name'];
						}
					}else{
						$name_arr[$v['calendar_id']][] = $v['name'];
					}
				}
			}
			foreach($data as $k => $v)
			{
				$v['email'] = isset($email_arr[$v['id']]) ? implode("<br/>", $email_arr[$v['id']]) : NULL;
				$v['name'] = isset($name_arr[$v['id']]) ? implode("<br/>", $name_arr[$v['id']]) : NULL;
				$data[$k] = $v;
			}
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin() && !$this->isEditor())
		{
			$this->set('status', 2);
			return;
		}
		
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminCalendars.js');
	}
	
	public function pjActionSaveCalendar()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (!isset($_POST['column']) || empty($_POST['column']) || !isset($_POST['value']) || !isset($_GET['id']) || (int) $_GET['id'] <= 0)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing, invalid or empty params.'));
			}
			$pjCalendarModel = pjCalendarModel::factory();
			if (!in_array($_POST['column'], $pjCalendarModel->getI18n()))
			{
				$pjCalendarModel->set('id', $_GET['id'])->modify(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjCalendar');
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Calendar has been updated.'));
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin() && !$this->isEditor())
		{
			$this->set('status', 2);
			return;
		}
		
		if (isset($_POST['calendar_update']))
		{
			$required = array('id');
			if (!$this->isEditor())
			{
				$required[] = 'user_id';
			} else {
				if (isset($_POST['user_id']))
				{
					unset($_POST['user_id']);
				}
			}
			
			$pjCalendarModel = pjCalendarModel::factory();
			if (!$pjCalendarModel->validateRequest($required, $_POST))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCalendars&action=pjActionIndex&err=AC13");
			}

			if (!$pjCalendarModel->validates($_POST))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCalendars&action=pjActionIndex&err=AC13");
			}
			
			$pjCalendarModel->set('id', $_POST['id'])->modify($_POST);
			if (isset($_POST['i18n']))
			{
				pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjCalendar');
			}
			
			pjCalendarUserModel::factory()->where('calendar_id', $_POST['id'])->eraseAll();;
			if(is_array($_POST['user_id']) && !empty($_POST['user_id']))
			{
				$pjCalendarUserModel = pjCalendarUserModel::factory();
				
				foreach ($_POST['user_id'] as $user_id)
				{
					$pjCalendarUserModel->addBatchRow(array($_POST['id'], $user_id));
				}
				$pjCalendarUserModel->setBatchFields(array('calendar_id', 'user_id'))->insertBatch();
			}
			
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCalendars&action=pjActionIndex&err=AC01");
			
		} else {
			if (!isset($_GET['id']) || (int) $_GET['id'] <= 0)
			{
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCalendars&action=pjActionIndex&err=AC13");
			}
			
			$arr = pjCalendarModel::factory()->find($_GET['id'])->getData();
			if (empty($arr))
			{
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCalendars&action=pjActionIndex&err=AC08");
			}
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjCalendar');
			$arr['user_id_arr'] = pjCalendarUserModel::factory()->where('calendar_id', $_GET['id'])->findAll()->getDataPair(null, 'user_id');
			$this->set('arr', $arr);
			
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
			
			$lp_arr = array();
			foreach ($locale_arr as $item)
			{
				$lp_arr[$item['id']."_"] = $item['file']; 
			}
			$this->set('lp_arr', $locale_arr);
			
			$this->set('user_arr', pjUserModel::factory()->orderBy('t1.name ASC')->findAll()->getData());
			
			$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
			$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			if ((int) $this->option_arr['o_multi_lang'] === 1)
			{
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			}
			$this->appendJs('pjAdminCalendars.js');
		}
	}
	
	public function pjActionView()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin() && !$this->isEditor())
		{
			$this->set('status', 2);
			return;
		}
		
		if (isset($_GET['id']) && (int) $_GET['id'] > 0)
		{
			if ((int) pjCalendarModel::factory()->where('t1.id', $_GET['id'])->findCount()->getData() !== 1)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCalendars&action=pjActionIndex");
			}
			$this->setForeignId($_GET['id']);
		}

		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendCss('pj.bootstrap.min.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
		$this->appendCss('index.php?controller=pjFrontEnd&action=pjActionLoadCss&skip_jqueryui=1&cid=' . $this->getForeignId() . '&' . rand(1,99999), PJ_INSTALL_URL, true);
		$this->appendJs('jquery.noty.packaged.min.js', PJ_THIRD_PARTY_PATH . 'noty/packaged/');
		$this->appendJs('pjAdminCalendars.js');
	}
	
	function pjActionGetSlots()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['date']) && !empty($_GET['date']))
			{
				$this->getSlots($this->getForeignId(), $_GET['date']);
			}
		}
	}
	
	public function pjActionDeleteTimeslot()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				if (1 == pjBookingSlotModel::factory()->set('id', $_POST['id'])->erase()->getAffectedRows())
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Time slot has been deleted.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Time slot has not been deleted.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing or empty parameters.'));
		}
		exit;
	}
	
	public function pjActionDeleteBooking()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['booking_id']) && (int) $_POST['booking_id'] > 0)
			{
				if (1 == pjBookingModel::factory()->set('id', $_POST['booking_id'])->erase()->getAffectedRows())
				{
					pjBookingSlotModel::factory()->where('booking_id', $_POST['booking_id'])->eraseAll();
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Booking has been deleted.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Booking has not been deleted.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing or empty parameters.'));
		}
		exit;
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
}
?>