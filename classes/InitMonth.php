<?php
/**
Class incapsulates all initial values for a first month that will be displayed on page after
its loading.
Class also provides methods to get list of events from database.

	methods list:
		- getInitialMonth
		- getDaysOfWeek
		- getWeeksInMonth
		- get_the_day
		
		- setDaysOfWeek
		- getDate
*/

class InitMonth{
	private $days_of_week = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
	private $date;
	private $days_in_month;
	private $first_week_offset;
	private $weeks_in_month;
	private $first_week_offset_countdown;
	private $days_counter;
	
	
	public function __construct($date_string = ''){

		$this->date = new DateTime('first day of this month');
		$this->days_in_month = (int)$this->date->format('t');//w
		$this->first_week_offset = (int)$this->date->format('N') - 1;
		$this->weeks_in_month = ceil(($this->days_in_month + $this->first_week_offset)/7);
		$this->first_week_offset_countdown = $this->first_week_offset;
		$this->days_counter = 1;
	}
	
	public function getInitialMonth(){
		return $this->date->format('F Y');
	}

	function getDaysOfWeek(){
		return $this->days_of_week;
	}
	
	public function getWeeksInMonth(){
		return $this->weeks_in_month;
	}
	
	public function get_the_day(){
		if($this->first_week_offset_countdown-- > 0 ){return;}
		if($this->days_counter > $this->days_in_month){return;}
		return $this->days_counter++;
	}
	
	function setDaysOfWeek($days){
		if(!is_array($days) || count($days) != 7){
			return false;
		}
		$this->days_of_week = $days;
	}
	
	public function getDate(){
		return $this->date;
	}
}
