<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminTime extends pjAdmin
{
	private $weekDays = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
	
	public function pjActionIndex()
	{
		$this->checkLogin();

		if (!$this->isAdmin())
		{
			$this->set('status', 2);
			return;
		}
		
		if (isset($_POST['working_time']))
		{
			
			$pjWorkingTimeModel = pjWorkingTimeModel::factory();
			
			$data = array();
			$day = $_POST['day_of_week'];
			if (!isset($_POST[$day . '_dayoff']))
			{
				$lunch_from = $_POST[$day . '_lunch_from'];
				$lunch_length = $_POST['lunch_length'] != '' ? intval($_POST['lunch_length']) : 0;
				$lunch_to = strtotime($lunch_from) + ($lunch_length * 60);
				
				$data[$day . '_from'] = date('H:i', strtotime($_POST[$day . '_from']));
				$data[$day . '_to'] = date('H:i', strtotime($_POST[$day . '_to']));
				if($_POST['lunch_break'] == 'T')
				{
					$data[$day . '_lunch_from'] = date('H:i', strtotime($lunch_from));
					$data[$day . '_lunch_to'] = date('H:i', $lunch_to);
				}else{
					$data[$day . '_lunch_from'] = '00:00:00';
					$data[$day . '_lunch_to'] = '00:00:00';
				}
				$data[$day . '_slots'] = $_POST[$day . '_slots'];
				$data[$day . '_price'] = !empty($_POST[$day . '_price']) ? (float) $_POST[$day . '_price'] : 0;
				$data[$day . '_dayoff'] = "F";
				
			} else {
				/*$data[$day . '_from'] = ":NULL";
				$data[$day . '_to'] = ":NULL";
				$data[$day . '_lunch_from'] = ":NULL";
				$data[$day . '_lunch_to'] = ":NULL";
				$data[$day . '_price'] = ":NULL";
				$data[$day . '_limit'] = 1;
				$data[$day . '_length'] = 60;*/
				$data[$day . '_dayoff'] = "T";
			}
			if (!empty($data))
			{
				$pjWorkingTimeModel
					->set('id', $_POST['id'])
					->modify(array_merge($_POST, $data));
			}

			pjUtil::redirect(sprintf("%sindex.php?controller=pjAdminTime&action=pjActionIndex&day=$day&err=AT01", PJ_INSTALL_URL));
		}
		$calendar = pjCalendarModel::factory()
			->select('t1.*, t2.content AS `title`')
			->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
			->find($this->getForeignId())
			->getData();
		
		$wt_arr = pjWorkingTimeModel::factory()
			->where('t1.foreign_id', $this->getForeignId())
			->limit(1)
			->findAll()
			->getData();
		
		$this->set('wt_arr', !empty($wt_arr) ? $wt_arr[0] : array());
		$this->set('calendar', $calendar);
		
		$this->appendCss('jquery.ui.timepicker.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
		$this->appendJs('jquery.ui.timepicker.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
		$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
		$this->appendJs('pjAdminTime.js');
	}
	
	public function pjActionCustom()
	{
		$this->checkLogin();

		if (!$this->isAdmin())
		{
			$this->set('status', 2);
			return;
		}
		
		if (isset($_POST['custom_time']))
		{
			$pjPriceModel = pjPriceModel::factory();
			$date = pjUtil::formatDate($_POST['date'], $this->option_arr['o_date_format']);
			
			$pjPriceModel
				->where('calendar_id', $this->getForeignId())
				->where('`date`', $date)
				->eraseAll()
				->reset();

			$data = $_data = array();
			$data['foreign_id'] = $this->getForeignId();
			$data['date'] = $date;
			$data['is_dayoff'] = isset($_POST['is_dayoff']) ? 'T' : 'F';
			
			$lunch_from_ts = strtotime($date . ' ' . $_POST['lunch_from']);
			$lunch_length = !empty($_POST['lunch_length']) ? intval($_POST['lunch_length']) : 0;
			$lunch_to = $lunch_from_ts + ($lunch_length * 60);
			if ($data['is_dayoff'] == 'T')
			{
				$data['start_time'] = !empty($_POST['start']) ? date('H:i', strtotime($_POST['start'])) : '00:00';
				$data['end_time'] = !empty($_POST['end']) ? date('H:i', strtotime($_POST['end'])) : '00:00';
				$data['start_lunch'] = date('H:i', $lunch_from_ts);
				$data['end_lunch'] = date('H:i', $lunch_to);
				$data['price'] = !empty($_POST['price']) ? (float) $_POST['price'] : 0.00;
				/*$data['start_time'] = ':NULL';
				$data['end_time'] = ':NULL';
				$data['start_lunch'] = ':NULL';
				$data['end_lunch'] = ':NULL';
				$data['slot_length'] = ':NULL';
				$data['slot_limit'] = ':NULL';
				$data['price'] = ':NULL';*/
			} else {
				$data['start_time'] = !empty($_POST['start']) ? date('H:i', strtotime($_POST['start'])) : '00:00';
				$data['end_time'] = !empty($_POST['end']) ? date('H:i', strtotime($_POST['end'])) : '00:00';
				$data['start_lunch'] = date('H:i', $lunch_from_ts);
				$data['end_lunch'] = date('H:i', $lunch_to);
				$data['price'] = !empty($_POST['price']) ? (float) $_POST['price'] : 0.00;
				
				if (!isset($_POST['single_price']))
				{
					$pjPriceModel->setBatchFields(array('calendar_id', 'date', 'start_time', 'end_time', 'start_ts', 'end_ts', 'price'));
					foreach ($_POST['price'] as $k => $price)
					{
						if (empty($price) && strlen($price) === 0) continue;
						list($start_ts, $end_ts) = explode("|", $k);
						$pjPriceModel->addBatchRow(array(
							$this->getForeignId(),
							$date,
							date("H:i:s", $start_ts),
							date("H:i:s", $end_ts),
							$start_ts,
							$end_ts,
							$price
						));
					}
					$pjPriceModel->insertBatch();
					$_data['price'] = ':NULL';
				}
			}
			
			pjDateModel::factory()
				->where('foreign_id', $this->getForeignId())
				->where('`date`', $date)
				->limit(1)
				->eraseAll()
				->reset()
				->setAttributes(array_merge($_POST, $data, $_data))
				->insert();
			
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTime&action=pjActionCustom&err=AT02");
		}

		$calendar = pjCalendarModel::factory()
			->select('t1.*, t2.content AS `title`')
			->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
			->find($this->getForeignId())
			->getData();
		
		$this->set('calendar', $calendar);
		
		$this->appendCss('jquery.ui.timepicker.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
		$this->appendJs('jquery.ui.timepicker.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
		$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminTime.js');
	}
	
	public function pjActionDeleteDate()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjDateModel = pjDateModel::factory();
				$arr = $pjDateModel->find($_GET['id'])->getData();
				if (empty($arr))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Record not found.'));
				}
				
				if (pjDateModel::factory()->set('id', $_GET['id'])->erase()->getAffectedRows() == 1)
				{
					pjPriceModel::factory()
						->where('calendar_id', $arr['foreign_id'])
						->where('`date`', $arr['date'])
						->eraseAll();
						
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Date has been deleted.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Date has not been deleted.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing or empty params.'));
		}
		exit;
	}
	
	public function pjActionDeleteDateBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			if (isset($_POST['record']) && !empty($_POST['record']))
			{
				$pjDateModel = pjDateModel::factory();
				$arr = $pjDateModel
					->select("CONCAT_WS('_', `foreign_id`, `date`) AS `fd`")
					->whereIn('id', $_POST['record'])
					->findAll()
					->getDataPair(null, 'fd');
				
				if (empty($arr))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Record(s) not found.'));
				}
				$pjDateModel->reset()->whereIn('id', $_POST['record'])->limit(count($_POST['record']))->eraseAll();
				pjPriceModel::factory()->whereIn("CONCAT_WS('_', `calendar_id`, `date`)", $arr)->eraseAll();
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Date(s) has been deleted.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing or empty params.'));
		}
		exit;
	}
	
	public function pjActionGetDate()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			$pjDateModel = pjDateModel::factory();
				
			$pjDateModel->where('t1.foreign_id', $this->getForeignId());
			
			if (isset($_GET['is_dayoff']) && strlen($_GET['is_dayoff']) > 0 && in_array($_GET['is_dayoff'], array('T', 'F')))
			{
				$pjDateModel->where('t1.is_dayoff', $_GET['is_dayoff']);
			}
				
			$column = '`date`';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjDateModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjDateModel
				->select("t1.*,
					TIME_FORMAT(`start_time`, '%H:%i') AS `start_time`, TIME_FORMAT(`end_time`, '%H:%i') AS `end_time`,
					TIME_FORMAT(`start_lunch`, '%H:%i') AS `start_lunch`, TIME_FORMAT(`end_lunch`, '%H:%i') AS `end_lunch`")
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
				
			foreach ($data as $k => $v)
			{
				$data[$k]['price_format'] = pjUtil::formatCurrencySign(number_format($v['price'], 2), $this->option_arr['o_currency']);
				$data[$k]['start_time'] = date($this->option_arr['o_time_format'], strtotime($v['start_time']) );
				$data[$k]['end_time'] = date($this->option_arr['o_time_format'], strtotime($v['end_time']) );
				$data[$k]['start_lunch'] = date($this->option_arr['o_time_format'], strtotime($v['start_lunch']) );
				$data[$k]['end_lunch'] = date($this->option_arr['o_time_format'], strtotime($v['end_lunch']) );
			}
				
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionGetSlots()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			
		}
	}
	
	public function pjActionSaveDate()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0 && isset($_POST['column']) && isset($_POST['value']) && !empty($_POST['column']))
			{
				$pjDateModel = pjDateModel::factory();
				if (!in_array($_POST['column'], $pjDateModel->getI18n()))
				{
					$data = array();
					$data[$_POST['column']] = $_POST['value'];
					if ($_POST['column'] == 'is_dayoff' && $_POST['value'] == 'T')
					{
						$data['start_time'] = ':NULL';
						$data['end_time'] = ':NULL';
						$data['start_lunch'] = ':NULL';
						$data['end_lunch'] = ':NULL';
						$data['slot_length'] = ':NULL';
						$data['slot_limit'] = ':NULL';
						$data['price'] = ':NULL';
					}
					$pjDateModel->set('id', $_GET['id'])->modify($data);
				} else {
					pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjDate');
				}
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Time changes has been saved.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing or empty params.'));
		}
		exit;
	}
	
	public function pjActionUpdateCustom()
	{
		$this->checkLogin();

		if (!$this->isAdmin())
		{
			$this->set('status', 2);
			return;
		}
		
		if (isset($_POST['custom_time']))
		{
			$required = array(
				'date', 'start', 'end',
				'price', 'lunch_from', 'lunch_length',
				'slot_length', 'slot_limit', 'slots'
			);
			
			$pjPriceModel = pjPriceModel::factory();
			if (!$pjPriceModel->validateRequest($required, $_POST))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTime&action=pjActionCustom&err=AT05");
			}
			$date = pjUtil::formatDate($_POST['date'], $this->option_arr['o_date_format']);
			
			$pjPriceModel
				->where('calendar_id', $this->getForeignId())
				->where('`date`', $date)
				->eraseAll()
				->reset();

			$data = $_data = array();
			$data['date'] = $date;
			$data['is_dayoff'] = isset($_POST['is_dayoff']) ? 'T' : 'F';
			
			$lunch_from_ts = strtotime($date . ' ' . $_POST['lunch_from']);
			$lunch_length = !empty($_POST['lunch_length']) ? intval($_POST['lunch_length']) : 0;
			$lunch_to = $lunch_from_ts + ($lunch_length * 60);
			
			if ($data['is_dayoff'] == 'T')
			{
				/*$data['start_time'] = ':NULL';
				$data['end_time'] = ':NULL';
				$data['start_lunch'] = ':NULL';
				$data['end_lunch'] = ':NULL';
				$data['slot_length'] = ':NULL';
				$data['slot_limit'] = ':NULL';
				$data['price'] = ':NULL';*/
			} else {
				$data['start_time'] = !empty($_POST['start']) ? date('H:i', strtotime($_POST['start'])) : '00:00';
				$data['end_time'] = !empty($_POST['end']) ? date('H:i', strtotime($_POST['end'])) : '00:00';
				$data['start_lunch'] = date('H:i', $lunch_from_ts);
				$data['end_lunch'] = date('H:i', $lunch_to);
				$data['price'] = !empty($_POST['price']) ? (float) $_POST['price'] : 0.00;
				
				if (!isset($_POST['single_price']))
				{
					$pjPriceModel->setBatchFields(array('calendar_id', 'date', 'start_time', 'end_time', 'start_ts', 'end_ts', 'price'));
					foreach ($_POST['price'] as $k => $price)
					{
						if (empty($price) && strlen($price) === 0) continue;
						list($start_ts, $end_ts) = explode("|", $k);
						$pjPriceModel->addBatchRow(array(
							$this->getForeignId(),
							$date,
							date("H:i:s", $start_ts),
							date("H:i:s", $end_ts),
							$start_ts,
							$end_ts,
							$price
						));
					}
					$pjPriceModel->insertBatch();
					$_data['price'] = ':NULL';
				}
			}
			
			pjDateModel::factory()
				->set('id', $_POST['id'])
				->modify(array_merge($_POST, $data, $_data));

			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTime&action=pjActionCustom&err=AT03");
		}
		
		if (!isset($_GET['id']) || (int) $_GET['id'] <= 0)
		{
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTime&action=pjActionIndex&err=AT05");
		}
		$arr = pjDateModel::factory()->find($_GET['id'])->getData();
		if (empty($arr))
		{
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTime&action=pjActionCustom&err=AT08");
		}
		$this->set('arr', $arr);
		$this->set('price_arr', pjPriceModel::factory()
			->where('t1.calendar_id', $arr['foreign_id'])
			->where('t1.date', $arr['date'])
			->orderBy('t1.start_time ASC')
			->findAll()
			->getData()
		);
		
		$this->appendCss('jquery.ui.timepicker.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
		$this->appendJs('jquery.ui.timepicker.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
		$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
		$this->appendJs('pjAdminTime.js');
	}

	public function pjActionGetPrices()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			$this->set('wt_arr', pjWorkingTimeModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->findAll()
				->getDataIndex(0)
			);
				
			$price_day_arr = array();
			if (isset($_GET['day']) && !empty($_GET['day']) && in_array($_GET['day'], $this->weekDays))
			{
				$price_day_arr = pjPriceDayModel::factory()
					->where('t1.calendar_id', $this->getForeignId())
					->where('t1.day', $_GET['day'])
					->orderBy('t1.start_time ASC')
					->findAll()
					->getData();
			}
			$this->set('price_day_arr', $price_day_arr);
		}
	}
	
	public function pjActionSetPrices()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			if (!isset($_POST['day']) || !in_array($_POST['day'], $this->weekDays))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing, empty or invalid params.'));
			}
			
			$pjPriceDayModel = new pjPriceDayModel();
			$pjPriceDayModel
				->where('calendar_id', $this->getForeignId())
				->where('`day`', $_POST['day'])
				->eraseAll();
			if (!isset($_POST['delete']) && isset($_POST['price']) && !empty($_POST['price']))
			{
				$pjPriceDayModel->reset()->setBatchFields(array('calendar_id', 'day', 'start_time', 'end_time', 'price'));
				foreach ($_POST['price'] as $k => $price)
				{
					if (empty($price) && strlen($price) === 0) continue;
					list($start_ts, $end_ts) = explode("|", $k);
					$pjPriceDayModel->addBatchRow(array($this->getForeignId(), $_POST['day'], date("H:i:s", $start_ts), date("H:i:s", $end_ts), $price));
				}
				$pjPriceDayModel->insertBatch();
				
				//Sasho ne iska da se update-va default cenata za denq v tozi sluchai (vodi do promqna i v AppCotroller::getPricesDate)
				//pjWorkingTimeModel::factory()->where('foreign_id', $this->getForeignId())->limit(1)->modifyAll(array($_POST['day'] . '_price' => ':NULL'));
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Price(s) has been saved.'));
			} else {
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 201, 'text' => 'Price(s) has been deleted.'));
			}
		}
		exit;
	}

	public function pjActionGetLunchBreak()
	{
		$this->setAjax(true);
		
		if (isset($_POST['working_time']))
		{
			
		}
	}
	
	public function pjActionGenerateSlots()
	{
		$this->setAjax(true);
		
		if (isset($_POST['working_time']))
		{
				
		}
	}
	
	public function pjActionGetCustomLunchBreak()
	{
		$this->setAjax(true);
	
	}
	
	public function pjActionGenerateCustomSlots()
	{
		$this->setAjax(true);
	}
}
?>