<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php __('menuInstall'); ?></a></li>
			<li><a href="#tabs-2"><?php __('menuSeo'); ?></a></li>
		</ul>
		<div id="tabs-1">
			<?php pjUtil::printNotice(__('lblInstallJs1_title', true), __('lblInstallJs1_body', true), false, false); ?>

			<?php if (count($tpl['locale_arr']) > 1) : ?>
			<form action="" method="get" class="pj-form form">
				<fieldset class="fieldset white">
					<legend><?php __('install_legend'); ?></legend>
					<p>
						<label class="title"><?php __('lblInstallConfigLocale'); ?></label>
						<select class="pj-form-field w200" name="install_locale">
							<option value="">-- <?php __('lblChoose'); ?> --</option>
							<?php
							foreach ($tpl['locale_arr'] as $locale)
							{
								?><option value="<?php echo $locale['id']; ?>"><?php echo pjSanitize::html($locale['title']); ?></option><?php
							}
							?>
						</select>
					</p>
					<p>
						<label class="title"><?php __('lblInstallConfigHide'); ?></label>
						<span class="left">
							<input type="checkbox" name="install_hide" value="1" />
						</span>
					</p>
				</fieldset>
			</form>
			<?php endif; ?>
			
			<form id="pjInstallSettings" action="<?php echo PJ_INSTALL_URL; ?>preview.php" method="get" class="pj-form form" target="_blank">
				<fieldset class="fieldset white">
					<legend><?php __('install_legend'); ?></legend>
					<p>
						<label class="title"><?php __('lblCalendar'); ?></label>
						<select class="pj-form-field w200" id="install_calendar_id" name="cid">
							<?php
							foreach ($tpl['calendars'] as $calendar)
							{
								?><option value="<?php echo $calendar['id']; ?>"<?php echo $controller->getForeignId() == $calendar['id'] ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html($calendar['title']); ?></option><?php
							}
							?>
						</select>
					</p>
					<p>
						<label class="title"><?php __('install_layout'); ?></label>
						<select name="layout" class="pj-form-field">
							<?php
							foreach (__('front_views', true) as $key => $val)
							{
								if (!in_array($key, array(1, 2)))
								{
									continue;
								}
								?><option value="<?php echo $key; ?>" data-ok="<?php echo $key == 2 ? $tpl['is_weekly_ok']: 1; ?>"><?php echo pjSanitize::html($val); ?></option><?php
							}
							?>
						</select>
						
						<input type="submit" id="btnPreview" value="<?php __('menuPreview', false, true); ?>" class="pj-button" />
					</p>
					<p>
						<label class="title"><?php __('lblAllowSwitchLayout'); ?></label>
						<span class="block t6">
							<input type="checkbox" name="switch" id="switch_layout" checked="checked"/>
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblMultiCalendar'); ?></label>
						<span class="block t6">
							<input type="checkbox" name="multi" id="multi_calendar"/>
						</span>
					</p>
				</fieldset>
			</form>
			
			<div id="weekly_warn" style="display:none"><?php pjUtil::printNotice(@$titles['AO10'], @$bodies['AO10'], false); ?></div>
			
			<div class="install_stuff">
				<textarea class="pj-form-field w700 textarea_install" id="install_code" style="overflow: auto; height:120px">
&lt;link href="<?php echo PJ_INSTALL_URL.PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'; ?>pj.bootstrap.min.css" type="text/css" rel="stylesheet" /&gt;				
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&action=pjActionLoadCss&cid=<?php echo $controller->getForeignId(); ?>" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&action=pjActionLoad&cid=<?php echo $controller->getForeignId(); ?>"&gt;&lt;/script&gt;</textarea>
	
				<p class="bold t20 b10"><?php __('install_step_2'); ?></p>
				<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:40px">&lt;!DOCTYPE html&gt;</textarea>
				
				<p class="bold t20 b10"><?php __('install_step_3'); ?></p>
				<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:40px">&lt;meta name="viewport" content="width=device-width, initial-scale=1"&gt;</textarea>
			</div>
			<div style="display:none" id="hidden_code">&lt;link href="<?php echo PJ_INSTALL_URL.PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'; ?>pj.bootstrap.min.css" type="text/css" rel="stylesheet" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&action=pjActionLoadCss&cid=<?php echo $controller->getForeignId(); ?>" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&action=pjActionLoad&cid=<?php echo $controller->getForeignId(); ?>"&gt;&lt;/script&gt;</div>
		</div>
		
		<div id="tabs-2">
			<?php pjUtil::printNotice(@$titles['AO30'], @$bodies['AO30']); ?>
			<p style="margin: 20px 0 7px; font-weight: bold"><?php __('lblInstallSeo_1'); ?></p>
			<input type="text" id="uri_page" class="pj-form-field w700" value="myPage.php" />
			
			<p style="margin: 20px 0 7px; font-weight: bold"><?php __('lblInstallSeo_2'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:30px">
&lt;meta name="fragment" content="!"&gt;</textarea>

			<p style="margin: 20px 0 7px; font-weight: bold"><?php __('lblInstallSeo_3'); ?></p>
			<textarea class="pj-form-field w700 textarea_install" id="install_htaccess" style="overflow: auto; height:80px">
RewriteEngine On
RewriteCond %{QUERY_STRING} _escaped_fragment_=(.*)
RewriteRule ^myPage.php <?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontPublic&action=pjActionRouter&_escaped_fragment_=%1 [L,NC]</textarea>

			<div style="display: none" id="hidden_htaccess">RewriteEngine On
RewriteCond %{QUERY_STRING} _escaped_fragment_=(.*)
RewriteRule ^::URI_PAGE:: <?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontPublic&action=pjActionRouter&_escaped_fragment_=%1 [L,NC]</div>
		</div>
	</div>
	<?php
}
?>