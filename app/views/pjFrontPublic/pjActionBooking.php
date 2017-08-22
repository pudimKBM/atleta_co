<?php
if (isset($tpl['status']) && $tpl['status'] == "OK")
{
	?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<?php __('front_system_msg'); ?>
		</div><!-- /.panel-heading -->
		
		<?php
		$status = __('front_booking_status', true);
		if (isset($tpl['booking_arr']))
		{
			switch ($tpl['booking_arr']['payment_method'])
			{
				case 'paypal':
					if ($tpl['invoice_arr']['status'] == 'not_paid')
					{
						?>
						<div class="panel-body">
							<?php 
							echo $status[11];
							if (pjObject::getPlugin('pjPaypal') !== NULL)
							{
								$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
							} 
							?>
						</div>
						<?php
					} else {
						?>
						<div class="panel-body">
							<?php 
							echo $status[3];
							?>
						</div>
						<div class="panel-footer">
							<a href="#" class="btn btn-primary pull-left pjTsSelectorCalendar"><?php __('front_start_over', false, true); ?></a>
						</div><!-- /.panel-footer -->
						<?php
					}
					break;
				case 'authorize':
					if ($tpl['invoice_arr']['status'] == 'not_paid')
					{
						?>
						<div class="panel-body">
							<?php 
							echo $status[11];
							if (pjObject::getPlugin('pjAuthorize') !== NULL)
							{
								$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
							}
							?>
						</div>
						<?php
					} else {
						?>
						<div class="panel-body">
							<?php 
							echo $status[3];
							?>
						</div>
						<div class="panel-footer">
							<a href="#" class="btn btn-primary pull-left pjTsSelectorCalendar"><?php __('front_start_over', false, true); ?></a>
						</div><!-- /.panel-footer -->
						<?php
					}
					break;
				case 'bank':
					?>
					<div class="panel-body">
						<?php 
						echo $status[1] . '<br/>' . pjSanitize::html(nl2br($tpl['option_arr']['o_bank_account']));
						?>
					</div>
					<div class="panel-footer">
						<a href="#" class="btn btn-primary pull-left pjTsSelectorCalendar"><?php __('front_start_over', false, true); ?></a>
					</div><!-- /.panel-footer -->
					<?php
					break;
				case 'creditcard':
				case 'none':
				default:
					?>
					<div class="panel-body">
						<?php 
						echo $status[1];
						?>
					</div>
					<div class="panel-footer">
						<a href="#" class="btn btn-primary pull-left pjTsSelectorCalendar"><?php __('front_start_over', false, true); ?></a>
					</div><!-- /.panel-footer -->
					<?php
			}
		}else {
			?>
			<div class="panel-body">
				<?php 
				echo $status[4];
				?>
			</div>
			<?php
		}
		?>
	</div>
</div>
	<?php
} elseif (isset($tpl['status']) && $tpl['status'] == 'ERR') {
	?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<?php __('front_system_msg'); ?>
		</div><!-- /.panel-heading -->
		<div class="panel-body">
			<?php __('front_booking_na'); ?>
		</div>
		<div class="panel-footer">
			<a href="#" class="btn btn-primary pull-left pjTsSelectorPreview"><?php __('front_return_back', false, true); ?></a>
			<a href="#" class="btn btn-primary pull-right pjTsSelectorCalendar"><?php __('front_start_over', false, true); ?></a>
		</div><!-- /.panel-footer -->
	</div>
	<?php
}
?>