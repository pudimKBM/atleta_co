<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCalendarModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'calendars';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	protected $i18n = array(
		'title', 'terms_url', 'terms_body',
		'confirm_subject_client', 'confirm_tokens_client', 'payment_subject_client', 'payment_tokens_client',
		'confirm_subject_admin', 'confirm_tokens_admin', 'payment_subject_admin', 'payment_tokens_admin',
		'reminder_subject_client', 'reminder_tokens_client',
		'confirm_sms_admin', 'payment_sms_admin', 'reminder_sms_client'
	);
	
	public static function factory($attr=array())
	{
		return new pjCalendarModel($attr);
	}
	
	public function init($user_id, $locale)
	{
		$calendar_id = $this->setAttributes(array(
			'user_id' => $user_id
		))->insert()->getInsertId();
		
		if ($calendar_id !== FALSE && (int) $calendar_id > 0)
		{
			pjCalendarUserModel::factory()->setAttributes(array('calendar_id' => $calendar_id, 'user_id' => $user_id))->insert();
			
			$pjMultiLangModel = pjMultiLangModel::factory();
			$i18n_arr = $pjMultiLangModel->getMultiLang(0, 'pjCalendar');
			$i18n_arr[$locale]['title'] = 'Calendar 1';
			$pjMultiLangModel->reset()->saveMultiLang($i18n_arr, $calendar_id, 'pjCalendar');
		}
		return $calendar_id;
	}
}
?>