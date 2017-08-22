<div class="pj-calendar">
	<?php
	include PJ_VIEWS_PATH . 'pjFrontEnd/elements/header.php';
	
	list($year, $month,) = explode("-", $_GET['date']);
	echo $tpl['calendar']->getMonthHTML((int) $month, $year);
	?>
	<div class="pj-calendar-tooltip-view">
		<div class="pj-calendar-footer active">
			<?php
			$slots = 0;
			if(isset($tpl['calendar_cart'][$_GET['cid']]))
			{
				foreach($tpl['calendar_cart'][$_GET['cid']] as $date => $items)
				{
					$slots += count($items);
				}
			}
			if ($slots > 0)
			{
				?><p class="pull-left"><?php $slots != 1 ? printf(__('front_slots_selected', true, true), $slots) : printf(__('front_slot_selected', true, true), $slots); ?></p><!-- /.pull-left --><?php
			} 
			?>
	
			<a href="#" class="pull-right btn btn-primary pjTsSelectorCart"><?php __('front_goto_cart');?> <span class="glyphicon glyphicon-chevron-right"></span></a>
		</div><!-- /.pj-calendar-footer -->
	</div>
</div><!-- /.pj-calendar -->