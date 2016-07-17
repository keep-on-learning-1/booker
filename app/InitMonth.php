<?php
/*
 * Class incapsulates all initial values for a first month that will be displayed on page after
 * its loading.
 * methods list:
 * 	- getInitialMonth	- Get current month and year as a string
 *  - getDaysOfWeek		- Return shortened names of days
 *  - getWeeksInMonth	- Get calculated number of weeks in specified month
 *  - get_the_day		- Return current date. Using in loop.
 *  - getDate			- Return DateTime object of first day of current month
 */

class InitMonth{
	private $days_of_week = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
	private $days_of_week_sun = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	private $date;							// DataTime object for the first day of current month
	private $days_in_month;					// number of days in the month
	private $first_week_offset;				// number of empty cells in calendar, used for countdown
	private $weeks_in_month;				// number of weeks in the month
	private $first_week_offset_countdown;	// number of empty cells in calendar table before the 1-st day of month
	private $days_counter;					// counter that is used in the loop

	public $this_month;						// current month, 1..12
	public $this_year;						// current year

	
	public function __construct($first_day = 'monday'){
		$this->date = new DateTime('first day of this month');
		$this->days_in_month = (int)$this->date->format('t');// 28..31
		$this->days_counter = 1;
		if($first_day == 'monday') {
			$this->first_week_offset = (int)$this->date->format('N') - 1; // 1(Mon)..7(Sun)
		}else{
			$this->first_week_offset = (int)$this->date->format('w'); // 0(Sun)..6(Sat)
		}
		$this->weeks_in_month = ceil(($this->days_in_month + $this->first_week_offset) / 7);
		$this->first_week_offset_countdown = $this->first_week_offset;

		$this->this_month = (int)$this->date->format('n');
		$this->this_year = (int)$this->date->format('Y');
	}

	/*
	 * Used for CalendarController during page loading
	 */
	public function getInitialMonth(){
		return $this->date->format('F Y');
	}

	/*
	 * 	Returns array with textual representations of a days, three letters
	 */
	function getDaysOfWeek($first_day = ''){
		if($first_day == 'sunday'){
			return $this->days_of_week_sun;
		}
		return $this->days_of_week;
	}

	/*
	 * Returns number of weeks in current month
	 */
	public function getWeeksInMonth(){
		return $this->weeks_in_month;
	}

	/*
	 * Method returns null or date for current cell of calendar.
	 */
	public function get_the_day(){
		if($this->first_week_offset_countdown-- > 0 ){return;}
		if($this->days_counter > $this->days_in_month){return;}
		return $this->days_counter++;
	}

	/*
	 * Returns first day of current month (DateTime object)
	 */
	public function getDate(){
		return $this->date;
	}
}
