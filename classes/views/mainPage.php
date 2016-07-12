<br>
<div id="calendar-controller-container" class="CalendarControl_controller-container">
	<span class="CalendarControl_button-left"> &lt; </span><span class="CalendarControl_month-caption">
				<?php echo $month->getInitialMonth() ?>
			</span><span class="CalendarControl_button-right"> &gt; </span>
</div><!-- /#calendar-controller-container -->

<div id="app_messages_container">
	<?php echo BoardroomBooker::getRenderedMessages()?>
</div>

<div id="calendar_container" data-first_day='<?php echo $config['booker']['first_day']?>'>
	<table id='bb_calendar'>
		<thead>
		<tr>
			<?php foreach($month->getDaysOfWeek($config['booker']['first_day']) as $day_caption): ?>
				<td>
					<?php echo $day_caption; ?>
				</td>
			<?php endforeach; ?>
		</tr>
		</thead>
		<tbody>
		<?php for($weeks=0; $weeks < $month->getWeeksInMonth(); $weeks++): ?>
			<tr>
				<?php for($day=0; $day<7; $day++): ?>
					<td>
						<?php $current_date = $month->get_the_day() ?>
						<span class="bb_calendar_date">
							<?php echo $current_date?>
						</span>
						<?php $day_events = $events[$current_date]?>
						<?php if(is_array($day_events)):?>
							<?php foreach($day_events as $event):?>
								<a class="event_time" href="" data-id="<?php echo $event['id']; ?>">
									<?php echo $event['start'],' - ', $event['end'],'<br>' ?>
								</a>
							<?php endforeach; ?>
						<?php endif;?>

					</td>
				<?php endfor; ?>
			</tr>
		<?php endfor;?>
		</tbody>
	</table>
</div><!-- /#calendar_container -->

<div id="main_buttons_container">
	<a class="bb_button" href="index.php?action=book_it">
		Book It!
	</a>
	<br>
	<a class="bb_button"href="index.php?action=employee_list">
		Employee list
	</a>
</div><!-- /#main_buttons_container -->