/**
 * A class of calendar (IE 9+)
 *
 * Constructor accepts a link to container element (DIV element) and an instance of CaltndarController
 * CSS class "bb_calendar" will be added to container.
 * A link to a BB_Calendar instance is stored as 'bb_calendar' property of container element
 *
 * When the month is changed using CaltndarController an 'month_change' is generated.
 * An event is triggered on CaltndarController container element so links to the same instance of
 * CaltndarController will be stored in event.target and BB_Calendar.controller
 * Event object has 2 additional properties: month and year.
 *
 * Methods
 * 	- depends_of( event ) - check if the 'month_change' event was triggered on the
 * 	  same element that was put as an argument 'controller' to BB_Calendar constructor
 *	- change_month( year, months ) - Change the HTML of container to display specified month
 *  - setData - set an object of data that will be used to create a calendar HTML when new month specified
 *  	e.g.{
 *  		"12":[
 *  				{"start":"07:00am","end":"08:00am","event_id":"27","id":"40"}
 *  			 ],
 *  		"18":[
 *  				{"start":"07:00am","end":"08:00am","event_id":"28","id":"43"},
 *  				{"start":"07:00am","end":"08:00am","event_id":"29","id":"49"}
 *  			 ],
 *  		}
 *  - getData - return an object of data
 *
 * Example of 'month_change' event handler:
 *
 * 	document.addEventListener('month_change', function(event){
 *		var calendars_list = document.getElementsByClassName('bb_calendar');
 *		for(var i=0;i<calendars_list.length;i++){
 *			var current_calendar = calendars_list[i].bb_calendar;
 *			if(current_calendar.depends_of(event)){
 *				current_calendar.change_month(event.year, event.month);
 *			}
 *		}
 *	});
 *
 */

function BB_Calendar(container, controller, options){
	this.controller = controller;
	this.container = container;
	
	var dayRenderer = function(){ return '';};
	var data; //contains data to populate calendar cells
	
	if(!container){
		throw new Error('Container element has not been given to constructor');
	};
	if(container.tagName == undefined || container.tagName != "DIV"){
		throw new Error('Wrong container has been given to constructor');
	};
	if(container.classList.contains('bb_calendar')){
		throw new Error('An instance of bb_calendar already associated with given container');
	}
	if(!controller /*|| !instanceOf('CalendarController')*/){
		throw new Error('An instance of CalendarController has not been given to constructor');
	};
	container.classList.add('bb_calendar');
	container.bb_calendar = this;
	
	/* Public methods*/
	this.change_month = change_month;
	this.depends_of = depends_of;
	this.setData = function($data){
		data = $data; // global variable
	}
	this.getData = function(){return data;}

	/* Internal methods */

	function depends_of(event){
		return (event.target === this.controller.container);
	};

	function change_month(year, month){

		var date = new Date(year, month);
		var first_day = container.getAttribute('data-first_day');
		if(first_day == 'sunday'){
			var first_week_offset = date.getDay();
		}else{
			var first_week_offset = (date.getDay() || 7) - 1;
		}

		//Getting number of days in requested month
		var days_in_month = 0
		while(date.getMonth() == month){
			date.setDate(date.getDate() + 1);
			days_in_month++;
		}
		
		//----
		var number_of_weeks = Math.ceil((first_week_offset + days_in_month)/7);
		var tbody = container.querySelector('tbody');
		var rows =  container.querySelectorAll('tbody tr');

		//Correction of weeks/rows count
		if(rows.length > number_of_weeks){
			tbody.removeChild(rows[rows.length-1]);
		}
		if(rows.length < number_of_weeks){
			tbody.appendChild(rows[0].cloneNode(true));
		}

		//Populating table with data
		var date_counter = 1
		var cells = tbody.querySelectorAll('tr td');
		for(var i = 0; i < (number_of_weeks * 7); i++){
			if(first_week_offset > 0 || date_counter > days_in_month){
				cells[i].innerHTML = '';
				first_week_offset--;
				continue;
			}
			var current_cell_content = Array(
				'<span class="bb_calendar_date">' + date_counter + '</span>'
			);

			var data = this.getData();
			if(data[date_counter]){
				var length = data[date_counter].length;
				for(var len=0; len<length; len++){
					var id = data[date_counter][len].id;
					current_cell_content.push('<a class="event_time" href="" data-id="'+ id +'">');
					var str = data[date_counter][len].start + ' - ' +  data[date_counter][len].end;
					current_cell_content.push(str);
					current_cell_content.push('</a>');
				}
			}

			cells[i].innerHTML = current_cell_content.join(' \n');
			date_counter++
		}
	}
}