<?php
/*
 * Class incapsulates all initial values for a first month that will be displayed on page after
 * its loading.
 * methods list:
 * 	- getInitialMonth
 *  - getDaysOfWeek
 *  - getWeeksInMonth
 *  - get_the_day
 *  - setDaysOfWeek
 *  - getDate
 */

class InitMonth{
	private $days_of_week = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
	private $date;
	private $days_in_month;
	private $first_week_offset;
	private $weeks_in_month;
	private $first_week_offset_countdown;
	private $days_counter;

	public $this_month;
	public $this_year;

	
	public function __construct($date_string = ''){

		$this->date = new DateTime('first day of this month');
		$this->days_in_month = (int)$this->date->format('t');// 28..31
		$this->first_week_offset = (int)$this->date->format('N') - 1; // 1(Mon)..7(Sun) //see also 'w'
		$this->weeks_in_month = ceil(($this->days_in_month + $this->first_week_offset)/7);
		$this->first_week_offset_countdown = $this->first_week_offset;
		$this->days_counter = 1;

		$this->this_month = (int)$this->date->format('m');
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
	function getDaysOfWeek(){
		return $this->days_of_week;
	}

	/*
	 * Returns number of weeks in current month
	 */
	public function getWeeksInMonth(){
		return $this->weeks_in_month;
	}

	/*
	 * Method returns null or date for cells of calendar.
	 */
	public function get_the_day(){
		if($this->first_week_offset_countdown-- > 0 ){return;}
		if($this->days_counter > $this->days_in_month){return;}
		return $this->days_counter++;
	}

	/*
	 * TODO: delete
	 * Provides posibility to set russian names of days for calendar
	 */
	function setDaysOfWeek($days){
		if(!is_array($days) || count($days) != 7){
			return false;
		}
		$this->days_of_week = $days;
	}

	/*
	 * Returns first day of current month (DateTime object)
	 */
	public function getDate(){
		return $this->date;
	}
}
