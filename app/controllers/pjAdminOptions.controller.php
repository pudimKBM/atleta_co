<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOptions extends pjAdmin
{
	public function pjActionCopy()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['calendar_id']) && (int) $_POST['calendar_id'] > 0 && isset($_POST['tab_id']) && (int) $_POST['tab_id'] > 0)
			{
				$pjOptionModel = pjOptionModel::factory();
	
				$src = $pjOptionModel->where('t1.foreign_id', $_POST['calendar_id'])->where('t1.tab_id', $_POST['tab_id'])->findAll()->getData();
				$src_pair = $pjOptionModel->getDataPair('key', 'value');
				$pjOptionModel->begin();
				foreach ($src as $option)
				{
					$pjOptionModel
						->reset()
						->where('foreign_id', $this->getForeignId())
						->where('`key`', $option['key'])
						->limit(1)
						->modifyAll(array('value' => $option['value']));
				}
				$pjOptionModel->commit();
	
				$fields = array();
				if ((int) $_POST['tab_id'] === 5)
				{
					$fields = array('confirm_subject_client', 'confirm_tokens_client', 'payment_subject_client', 'payment_tokens_client', 
									'confirm_subject_admin', 'confirm_tokens_admin', 'payment_subject_admin', 'payment_tokens_admin', 
									'reminder_subject_client', 'reminder_tokens_client', 'confirm_sms_admin', 'payment_sms_admin',
									'reminder_sms_client'
					);
				} elseif ((int) $_POST['tab_id'] === 6) {
					$fields = array('terms_url', 'terms_body');
				}
	
				if (!empty($fields))
				{
					$pjMultiLangModel = pjMultiLangModel::factory();
						
					$src = $pjMultiLangModel
						->where('t1.model', 'pjCalendar')
						->where('t1.foreign_id', $_POST['calendar_id'])
						->whereIn('t1.field', $fields)
						->findAll()->getData();
	
					$pjMultiLangModel->begin();
					foreach ($src as $item)
					{
						$item['id'] = NULL;
						unset($item['id']);
						$item['foreign_id'] = $this->getForeignId();
							
						$pjMultiLangModel->prepare(sprintf(
								"INSERT INTO `%s` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`)
							VALUES (NULL, :foreign_id, :model, :locale, :field, :content)
							ON DUPLICATE KEY UPDATE `content` = :content", $pjMultiLangModel->getTable())
						)->exec($item);
					}
					$pjMultiLangModel->commit();
				}
			}
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();

		if (!$this->isAdmin())
		{
			$this->set('status', 2);
			return;
		}
		
		if (isset($_GET['cid']))
		{
			$this->setForeignId($_GET['cid']);
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=pjActionIndex&tab=" . $_GET['tab']);
		}
		
		if (isset($_GET['tab']) && in_array((int) $_GET['tab'], array(5,6)))
		{
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
					
			$lp_arr = array();
			foreach ($locale_arr as $v)
			{
				$lp_arr[$v['id']."_"] = $v['file'];
			}
			$this->set('lp_arr', $locale_arr);
			
			$arr = array();
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjCalendar');
			$this->set('arr', $arr);
			
			if ((int) $this->option_arr['o_multi_lang'] === 1)
			{
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			}
		} else {
			$tab_id = isset($_GET['tab']) && (int) $_GET['tab'] > 0 ? (int) $_GET['tab'] : 1;
			
			$arr = pjOptionModel::factory()
				->where("((t1.foreign_id='".$this->getForeignId()."' AND t1.`key` NOT IN('".implode("','", pjUtil::getCommonOptions())."')) OR (t1.foreign_id='0' AND t1.`key` IN('".implode("','", pjUtil::getCommonOptions())."')))")
				->where('tab_id', $tab_id)
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			
			$this->set('arr', $arr);
			
			$tmp = $this->models['Option']->reset()->where('foreign_id', $this->getForeignId())->findAll()->getData();
			$o_arr = array();
			foreach ($tmp as $item)
			{
				$o_arr[$item['key']] = $item;
			}
			$this->set('o_arr', $o_arr);
		}
		
		$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
		$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionInstall()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left outer')
				->orderBy('t1.sort ASC')->findAll()->getData();
			$this->set('locale_arr', $locale_arr);

			$slot_length = 0;
			$is_weekly_ok = 1;
			
			$wt_arr = pjWorkingTimeModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->limit(1)
				->findAll()
				->getDataIndex(0);
			if ($wt_arr !== FALSE)
			{
				foreach (array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') as $weekday)
				{
					if ($wt_arr[$weekday.'_dayoff'] == 'T')
					{
						continue;
					}
					if ($slot_length == 0)
					{
						$slot_length = $wt_arr[$weekday.'_length'];
					}
					if ($slot_length != $wt_arr[$weekday.'_length'])
					{
						$is_weekly_ok = 0;
						break;
					}
				}
			}
			
			$dt_arr = pjDateModel::factory()
				->select('t1.slot_length')
				->where('t1.foreign_id', $this->getForeignId())
				->where('t1.is_dayoff', 'F')
				->groupBy('t1.slot_length')
				->findAll()
				->getData();
				
			foreach ($dt_arr as $data)
			{
				if ($slot_length == 0)
				{
					$slot_length = $data['slot_length'];
				}
				if ($slot_length != $data['slot_length'])
				{
					$is_weekly_ok = 0;
					break;
				}
			}
			$this->set('is_weekly_ok', $is_weekly_ok);
			
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionPreview()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if (!$this->isAdmin())
		{
			$this->set('status', 2);
			return;
		}
		
		if (isset($_POST['options_update']))
		{
			if (isset($_POST['tab']) && in_array($_POST['tab'], array(5, 6)))
			{
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $this->getForeignId(), 'pjCalendar', 'data');
				}
			} else {
				$OptionModel = new pjOptionModel();
				$OptionModel
					->where('foreign_id', $this->getForeignId())
					->where('type', 'bool')
					->where('tab_id', $_POST['tab'])
					->modifyAll(array('value' => '1|0::0'));
					
				foreach ($_POST as $key => $value)
				{
					if (preg_match('/value-(string|text|int|float|enum|bool|color)-(.*)/', $key) === 1)
					{
						list(, $type, $k) = explode("-", $key);
						if (!empty($k))
						{
							if(in_array($k, pjUtil::getCommonOptions()))
							{
								$OptionModel
									->reset()
									->where('foreign_id', 0)
									->where('`key`', $k)
									->limit(1)
									->modifyAll(array(
										'value' => $OptionModel->escape($value, null, $type)
									));
							}else{
								$OptionModel
									->reset()
									->where('foreign_id', $this->getForeignId())
									->where('`key`', $k)
									->limit(1)
									->modifyAll(array(
										'value' => $OptionModel->escape($value, null, $type)
									));
							}
						}
					}
				}
			}
			if (isset($_POST['tab']))
			{
				switch ($_POST['tab'])
				{
					case '1':
						$err = 'AO01';
						break;
					case '2':
						$err = 'AO02';
						break;
					case '3':
						$err = 'AO03';
						break;
					case '4':
						$err = 'AO04';
						break;
					case '5':
						$err = 'AO05';
						break;
					case '6':
						$err = 'AO06';
						break;
					case '7':
						$err = 'AO07';
						break;
					case '8':
						$err = 'AO08';
						break;
				}
			}
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=" . @$_POST['next_action'] . "&tab=" . @$_POST['tab'] . "&err=$err");
		}
	}
	
	public function pjActionUpdateTheme()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjOptionModel::factory()
				->where('foreign_id', 0)
				->where('`key`', 'o_theme')
				->limit(1)
				->modifyAll(array('value' => 'theme1|theme2|theme3|theme4|theme5|theme6|theme7|theme8|theme9|theme10::theme' . $_GET['theme']));
	
		}
	}
}
?>