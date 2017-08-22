<?php
if (isset($tpl['status']) && $tpl['status'] == 'OK')
{
	$FORM = @$_SESSION[$controller->defaultForm];
	include PJ_VIEWS_PATH . 'pjFrontEnd/elements/summary.php';
	?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<?php __('front_booking_form'); ?>
		</div><!-- /.panel-heading -->

		<form action="" method="post" class="pjTsSelectorCheckoutForm">
			<input type="hidden" name="ts_checkout" value="1" />
			<div class="panel-body">
				<div class="pj-calendar-form">
					<?php
					if (in_array($tpl['option_arr']['o_bf_name'], array(2, 3)))
					{ 
						?>
						<div class="row">
							<label class="col-sm-4 control-label"><?php __('opt_o_bf_name'); ?><?php if ((int) $tpl['option_arr']['o_bf_name'] === 3) : ?><span class="tsAsterisk">*</span><?php endif; ?></label><!-- /.col-sm-4 -->
		
							<div class="col-sm-8">
								<input type="text" name="customer_name" class="form-control<?php echo $tpl['option_arr']['o_bf_name'] == 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['customer_name']) ? pjSanitize::html($FORM['customer_name']) : NULL; ?>" data-msg-required="<?php echo pjSanitize::html(__('front_v_name', false)); ?>">
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.col-sm-8 -->
						</div><!-- /.row -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_email'], array(2, 3)))
					{ 
						?>
						<div class="row">
							<label class="col-sm-4 control-label"><?php __('opt_o_bf_email'); ?><?php if ((int) $tpl['option_arr']['o_bf_email'] === 3) : ?><span class="tsAsterisk">*</span><?php endif; ?></label><!-- /.col-sm-4 -->
		
							<div class="col-sm-8">
								<input type="text" name="customer_email" class="form-control email<?php echo $tpl['option_arr']['o_bf_email'] == 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['customer_email']) ? pjSanitize::html($FORM['customer_email']) : NULL; ?>" data-msg-required="<?php echo pjSanitize::html(__('front_v_email', false)); ?>" data-msg-email="<?php echo pjSanitize::html(__('front_v_email_format', false)); ?>" />
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.col-sm-8 -->
						</div><!-- /.row -->
						<?php
					} 
					if (in_array($tpl['option_arr']['o_bf_phone'], array(2, 3)))
					{
						?>
						<div class="row">
							<label class="col-sm-4 control-label"><?php __('opt_o_bf_phone'); ?><?php if ((int) $tpl['option_arr']['o_bf_phone'] === 3) : ?><span class="tsAsterisk">*</span><?php endif; ?></label><!-- /.col-sm-4 -->
		
							<div class="col-sm-8">
								<input type="text" name="customer_phone" class="form-control<?php echo $tpl['option_arr']['o_bf_phone'] == 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['customer_phone']) ? pjSanitize::html($FORM['customer_phone']) : NULL; ?>" data-msg-required="<?php echo pjSanitize::html(__('front_v_phone', false)); ?>" />
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.col-sm-8 -->
						</div><!-- /.row -->
						<?php
					} 
					if (in_array($tpl['option_arr']['o_bf_country'], array(2, 3)))
					{
						?>
						<div class="row">
							<label class="col-sm-4 control-label"><?php __('opt_o_bf_country'); ?><?php if ((int) $tpl['option_arr']['o_bf_country'] === 3) : ?><span class="tsAsterisk">*</span><?php endif; ?></label><!-- /.col-sm-4 -->
		
							<div class="col-sm-8">
								<select name="customer_country" class="form-control<?php echo $tpl['option_arr']['o_bf_country'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo pjSanitize::html(__('front_v_country', false)); ?>">
									<option value="">-- <?php __('co_select_country'); ?> --</option>
									<?php
									if (isset($tpl['country_arr']) && is_array($tpl['country_arr']))
									{
										foreach ($tpl['country_arr'] as $v)
										{
											?><option value="<?php echo $v['id']; ?>"<?php echo isset($FORM['customer_country']) && $FORM['customer_country'] == $v['id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($v['name']); ?></option><?php
										}
									}
									?>
								</select>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.col-sm-8 -->
						</div><!-- /.row -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_state'], array(2, 3)))
					{ 
						?>
						<div class="row">
							<label class="col-sm-4 control-label"><?php __('opt_o_bf_state'); ?><?php if ((int) $tpl['option_arr']['o_bf_state'] === 3) : ?><span class="tsAsterisk">*</span><?php endif; ?></label><!-- /.col-sm-4 -->
		
							<div class="col-sm-8">
								<input type="text" name="customer_state" class="form-control<?php echo $tpl['option_arr']['o_bf_state'] == 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['customer_state']) ? pjSanitize::html($FORM['customer_state']) : NULL; ?>" data-msg-required="<?php echo pjSanitize::html(__('front_v_state', false)); ?>" />
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.col-sm-8 -->
						</div><!-- /.row -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_city'], array(2, 3)))
					{ 
						?>
						<div class="row">
							<label class="col-sm-4 control-label"><?php __('opt_o_bf_city'); ?><?php if ((int) $tpl['option_arr']['o_bf_city'] === 3) : ?><span class="tsAsterisk">*</span><?php endif; ?></label><!-- /.col-sm-4 -->
							
		
							<div class="col-sm-8">
								<input type="text" name="customer_city" class="form-control<?php echo $tpl['option_arr']['o_bf_city'] == 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['customer_city']) ? pjSanitize::html($FORM['customer_city']) : NULL; ?>" data-msg-required="<?php echo pjSanitize::html(__('front_v_city', false)); ?>" />
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.col-sm-8 -->
						</div><!-- /.row -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_address_1'], array(2, 3)))
					{ 
						?>
						<div class="row">
							<label class="col-sm-4 control-label"><?php __('booking_address_1'); ?><?php if ((int) $tpl['option_arr']['o_bf_address_1'] === 3) : ?><span class="tsAsterisk">*</span><?php endif; ?></label><!-- /.col-sm-4 -->
		
							<div class="col-sm-8">
								<input type="text" name="customer_address_1" class="form-control<?php echo $tpl['option_arr']['o_bf_address_1'] == 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['customer_address_1']) ? pjSanitize::html($FORM['customer_address_1']) : NULL; ?>" data-msg-required="<?php echo pjSanitize::html(__('front_v_address_1', false)); ?>" />
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.col-sm-8 -->
						</div><!-- /.row -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_address_2'], array(2, 3)))
					{
						?>
						<div class="row">
							<label class="col-sm-4 control-label"><?php __('booking_address_2'); ?><?php if ((int) $tpl['option_arr']['o_bf_address_2'] === 3) : ?><span class="tsAsterisk">*</span><?php endif; ?></label><!-- /.col-sm-4 -->
		
							<div class="col-sm-8">
								<input type="text" name="customer_address_2" class="form-control<?php echo $tpl['option_arr']['o_bf_address_2'] == 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['customer_address_2']) ? pjSanitize::html($FORM['customer_address_2']) : NULL; ?>" data-msg-required="<?php echo pjSanitize::html(__('front_v_address_2', false)); ?>" />
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.col-sm-8 -->
						</div><!-- /.row -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_zip'], array(2, 3)))
					{ 
						?>
						<div class="row">
							<label class="col-sm-4 control-label"><?php __('opt_o_bf_zip'); ?><?php if ((int) $tpl['option_arr']['o_bf_zip'] === 3) : ?><span class="tsAsterisk">*</span><?php endif; ?></label><!-- /.col-sm-4 -->
		
							<div class="col-sm-8">
								<input type="text" name="customer_zip" class="form-control<?php echo $tpl['option_arr']['o_bf_zip'] == 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['customer_zip']) ? pjSanitize::html($FORM['customer_zip']) : NULL; ?>" data-msg-required="<?php echo pjSanitize::html(__('front_v_zip', false)); ?>" />
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.col-sm-8 -->
						</div><!-- /.row -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_notes'], array(2, 3)))
					{ 
						?>
						<div class="row">
							<label class="col-sm-4 control-label"><?php __('opt_o_bf_notes'); ?><?php if ((int) $tpl['option_arr']['o_bf_notes'] === 3) : ?><span class="tsAsterisk">*</span><?php endif; ?></label><!-- /.col-sm-4 -->
		
							<div class="col-sm-8">
								<textarea name="customer_notes" cols="30" rows="10" class="form-control<?php echo $tpl['option_arr']['o_bf_notes'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo pjSanitize::html(__('front_v_notes', false)); ?>"><?php echo isset($FORM['customer_notes']) ? pjSanitize::html($FORM['customer_notes']) : NULL; ?></textarea>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.col-sm-8 -->
						</div><!-- /.row -->
						<?php
					}
					if ((int) $tpl['option_arr']['o_disable_payments'] === 0 &&
							(int) $tpl['option_arr']['o_hide_prices'] === 0 &&
							isset($tpl['amount']) && $tpl['amount']['deposit'] > 0)
					{
						?>
						<div class="row">
							<label class="col-sm-4 control-label"><?php __('booking_payment_method'); ?><span class="tsAsterisk">*</span></label>
							<div class="col-sm-8">
								<select name="payment_method" class="form-control required" data-msg-required="<?php echo pjSanitize::html(__('front_v_payment_method', false)); ?>">
									<option value="">-- <?php __('front_select_payment'); ?> --</option>
									<?php
									foreach (__('payment_methods', true) as $k => $v)
									{
										if ((int) @$tpl['option_arr']['o_allow_' . $k] === 1)
										{
											?><option value="<?php echo $k; ?>"<?php echo isset($FORM['payment_method']) && $FORM['payment_method'] == $k ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($v); ?></option><?php
										}
									}
									?>
								</select>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div>
						</div>
						<div class="row pjTsSelectorBank" style="display: <?php echo @$FORM['payment_method'] != 'bank' ? 'none' : NULL; ?>">
							<label class="col-sm-4 control-label"><?php __('booking_bank_account'); ?></label>
							<div class="col-sm-8"><span class="text-muted"><?php echo pjSanitize::html($tpl['option_arr']['o_bank_account']); ?></span></div>
						</div>
						<div class="row pjTsSelectorCCard" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
							<label class="col-sm-4 control-label"><?php __('booking_cc_type'); ?><span class="tsAsterisk">*</span></label>
							<div class="col-sm-8">
								<select name="cc_type" class="form-control required" data-msg-required="<?php echo pjSanitize::html(__('front_v_cc_type', false)); ?>">
									<option value="">---</option>
									<?php
									foreach (__('booking_cc_types', true) as $k => $v)
									{
										if (isset($FORM['cc_type']) && $FORM['cc_type'] == $k)
										{
											?><option value="<?php echo $k; ?>" selected="selected"><?php echo $v; ?></option><?php
										} else {
											?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
										}
									}
									?>
								</select>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div>
						</div>
						<div class="row pjTsSelectorCCard" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
							<label class="col-sm-4 control-label"><?php __('booking_cc_num'); ?><span class="tsAsterisk">*</span></label>
							<div class="col-sm-8">
								<input type="text" name="cc_num" class="form-control required" value="<?php echo isset($FORM['cc_num']) ? pjSanitize::html($FORM['cc_num']) : NULL; ?>" data-msg-required="<?php echo pjSanitize::html(__('front_v_cc_num', false)); ?>" />
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div>
						</div>
						<div class="row pjTsSelectorCCard" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
							<label class="col-sm-4 control-label"><?php __('booking_cc_code'); ?><span class="tsAsterisk">*</span></label>
							<div class="col-sm-8">
								<input type="text" name="cc_code" class="form-control required" value="<?php echo isset($FORM['cc_code']) ? pjSanitize::html($FORM['cc_code']) : NULL; ?>" data-msg-required="<?php echo pjSanitize::html(__('front_v_cc_code', false)); ?>" />
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div>
						</div>
						<div class="row pjTsSelectorCCard" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
							<label class="col-sm-4 control-label"><?php __('booking_cc_exp'); ?><span class="tsAsterisk">*</span></label>
							<div class="col-sm-8">
								<div class="row">
									<div class="col-sm-6">
									<?php
									echo pjTime::factory()
										->attr('name', 'cc_exp_month')
										->attr('id', 'cc_exp_month')
										->attr('class', 'form-control required')
										->attr('data-msg-required', __('front_v_cc_exp_month', true))
										->prop('format', 'M')
										->prop('selected', @$FORM['cc_exp_month'])
										->month();
									?>
									</div>
									<div class="col-sm-6">
									<?php
									echo pjTime::factory()
										->attr('name', 'cc_exp_year')
										->attr('id', 'cc_exp_year')
										->attr('class', 'form-control required')
										->attr('data-msg-required', __('front_v_cc_exp_year', true))
										->prop('left', 0)
										->prop('right', 10)
										->prop('selected', @$FORM['cc_exp_year'])
										->year();
									?>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_captcha'], array(2, 3)))
					{
						?>
						<div class="row">
							<label for="" class="col-sm-4 control-label">
								<?php __('opt_o_bf_captcha'); ?><?php if ((int) $tpl['option_arr']['o_bf_captcha'] === 3) : ?><span class="asterisk">*</span><?php endif; ?>
							</label>
							<div class="col-sm-8">
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-sx-12">
										<input type="text" name="captcha" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_captcha'] === 3 ? ' required' : NULL; ?>" maxlength="6" autocomplete="off" data-msg-required="<?php echo pjSanitize::html(__('front_v_captcha', false)); ?>" data-msg-remote="<?php echo pjSanitize::html(__('front_v_captcha_match', false)); ?>" />
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-sx-12">
										<img id="pjTsCaptchaImage" class="pjTsCaptchaImage" alt="Captcha" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&action=pjActionCaptcha&rand=<?php echo rand(1000, 999999); ?><?php echo isset($_GET['session_id']) ? '&session_id=' . $_GET['session_id'] : NULL;?>" style="vertical-align: middle" />
									</div>
								</div><!-- /.row -->
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.col-lg-8 col-md-8 col-sm-8 col-sx-12 -->
						</div>
						<?php
					}
					if (in_array((int) $tpl['option_arr']['o_bf_terms'], array(3)))
					{
						$terms_str = __('front_bf_terms', true, false);
						$term_agree = true;
						if (isset($tpl['terms_arr']) && !empty($tpl['terms_arr']))
						{
							$t_url = $tpl['terms_arr']['terms_url'];
							$t_body = trim($tpl['terms_arr']['terms_body']);
							if(empty($t_url) && empty($t_body))
							{
								$term_agree = false;
							}else{
								if (!empty($t_url) && preg_match('/^http(s)?:\/\//i', $t_url))
								{
									$terms_str = str_replace("{STAG}", '<a href="'.pjSanitize::html($t_url).'" class="tsServiceLink" target="_blank">', $terms_str);
								}else{
									$terms_str = str_replace("{STAG}", '<a href="#" data-target="#pjTsTermModal" data-toggle="modal" class="tsServiceLink">', $terms_str);
								}
								$terms_str = str_replace("{ETAG}", '</a>', $terms_str);
							}
								
						}
						if($term_agree == true)
						{
							?>
							<div class="row">
								<label class="col-sm-4 control-label">&nbsp;</label>
								<div class="col-sm-8">
									<div style="display: block; overflow: hidden;">
										<span style="float: left; width: 15px">
											<input type="checkbox" name="terms" id="terms_<?php echo $_GET['cid']; ?>" value="1" class="<?php echo (int) $tpl['option_arr']['o_bf_terms'] === 3 ? ' required' : NULL; ?>" style="margin: 0" data-msg-required="<?php echo pjSanitize::html(__('front_v_terms', false)); ?>" />
										</span>
										<span for="terms_<?php echo $_GET['cid']; ?>" style="float: left; width: 85%"><?php echo $terms_str; ?></span>
									</div>
									<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
								</div>
							</div>
							<?php
						}
					} 
					?>
				</div><!-- /.form -->
			</div><!-- /.panel-body -->
	
			<div class="panel-footer">
				<a href="#" class="btn btn-primary pull-left pjTsSelectorCalendar"><span class="glyphicon glyphicon-chevron-left"></span> <?php __('front_button_back', false, true); ?></a>
	
				<button type="submit" class="btn btn-primary pull-right"><?php __('front_button_continue', false, true); ?></button>
			</div><!-- /.panel-footer -->
		</form>
		
		<div class="modal fade pjTbModal" id="pjTsTermModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  	<div class="modal-dialog">
		    	<div class="modal-content">
				    <div class="modal-body">
				    	<?php
						echo nl2br(pjSanitize::html(@$tpl['terms_arr']['terms_body']));
						?>
				    </div>
			      	<div class="modal-footer">
			        	<button type="button" class="btn btn-default pjTbBtn pjTbBtnPrimary" data-dismiss="modal"><?php __('front_btn_close');?></button>
			      	</div>
		    	</div>
		  	</div>
		</div>
	</div><!-- /.panel -->
	<?php
} else {
	?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<?php __('front_system_msg'); ?>
		</div><!-- /.panel-heading -->
		<div class="panel-body">
			<?php __('front_checkout_na'); ?>
		</div>
		<div class="panel-footer">
			<a href="#" class="btn btn-primary pull-left pjTsSelectorCalendar"><?php __('front_start_over', false, true); ?></a>
		</div><!-- /.panel-footer -->
	</div>
	<?php
}
?>