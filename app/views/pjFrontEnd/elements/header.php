<?php
$multi_calendar = false;
$switch_layout = false;
if((isset($_GET['multi']) && (int) $_GET['multi'] == 1))
{ 
	if(count($tpl['calendars']) > 1)
	{
		$multi_calendar = true;
	}
}
if((isset($_SESSION[$controller->defaultSwitchLayout]) && (int) $_SESSION[$controller->defaultSwitchLayout] == 1) || !isset($_SESSION[$controller->defaultSwitchLayout]))
{ 
	$switch_layout = true;
} 
if($switch_layout == true || $multi_calendar == true)
{
	?>
	<div class="pj-calendar-views">
		<?php
		if($multi_calendar == true)
		{
			?>
			<div class="pull-left">
				<select id="pjTsSwitchCalendar" name="calendar_id" class="form-control pjTsSwitchCalendar">
					<?php
					foreach($tpl['calendars'] as $k => $v)
					{
						?><option value="<?php echo $v['id']; ?>"<?php echo $v['id'] == $controller->getForeignId() ? ' selected="selected"': NULL;?>><?php echo $v['title']; ?></option><?php
					} 
					?>	
				</select>
			</div>
			<?php 
		} 
		if($switch_layout == true)
		{
			?>
			<div class="btn-group pull-right" role="group" aria-label="...">
				<a href="#" class="btn btn-primary pjTsViewTab<?php echo isset($_GET['layout']) ? ($_GET['layout'] == 1 ? ' active' : NULL) : NULL;?>" title="monthly" data-layout="1"><span class="glyphicon glyphicon-calendar"></span></a>
				<a href="#" class="btn btn-primary pjTsViewTab<?php echo isset($_GET['layout']) ? ($_GET['layout'] == 2 ? ' active' : NULL) : NULL;?>" title="weekly" data-layout="2"><span class="glyphicon glyphicon-th"></span></a>
			</div>
			<?php
		}
		?>
	</div><!-- /.pj-calendar-views -->
	<?php
}
?>