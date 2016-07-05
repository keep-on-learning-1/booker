<?php
class MainPage extends PagePattern {
    private $config;
    private $number_of_bookers;
    private $month;
    function __construct(){
        $this->config = BoardroomBooker::getConfig();

        $this->number_of_bookers = $this->config['booker']['number_of_bookers'];
        if(!(int)$this->number_of_bookers){$this->number_of_bookers = 3;}
        require_once('./classes/InitMonth.php');
        $this->month = new InitMonth();
    }

    function render(){
        $month = $this->month;
        $this->getHeader('BoardroomBooker', array(), array('CalendarController.js', 'bb_calendar.js'));
        ?>
        <?php echo $this->renderTopSection('Booardroom 1')?>
        <br>

        <div id="calendar-controller-container" class="CalendarControl_controller-container">
			<span class="CalendarControl_button-left"> &lt; </span><span class="CalendarControl_month-caption">
				<?php echo $month->getInitialMonth() ?>
			</span><span class="CalendarControl_button-right"> &gt; </span>
		</div><!-- /#calendar-controller-container -->

        <?php echo $this->renderAppMessages();?>

		<div id="calendar_container">
			<table id='bb_calendar'>
				<thead>
					<tr>
					<?php foreach($month->getDaysOfWeek() as $day_caption): ?>
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
								<span class="bb_calendar_date">
									<?php echo $month->get_the_day()?>
								</span>
							</td>
						<?php endfor; ?>
					</tr>
					<?php endfor;?>
				</tbody>
			</table>
		</div>
        <div id="main_buttons_container">
            <a class="bb_button" href="index.php?action=book_it">
                Book It!
            </a>
			<br>
            <a class="bb_button"href="index.php?action=employee_list">
                Employee list
            </a>
        </div>

        <?php
        $this->getFooter(array('main.js'));
    }
}