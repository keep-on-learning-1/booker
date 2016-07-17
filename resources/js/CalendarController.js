/*
 * Controller for BB_Calendar
 *
 *  Constructor accepts a link to container element (DIV element) and an object of options
 *  	options = {
 *  				captionLeft: '<',
 *  				captionLeftClass: 'CalendarControl_button-left',
 *  				captionRight: '>',
 *  				captionRightClass: 'CalendarControl_button-right',
 *  				captionMonthClass: 'CalendarControl_month-caption'
 *  			  }
 *  Inside the container element generated 2 control elements and a field to display current month(and year).
 *  CSS classes that were specified in options object of constructor will be added to created elements.
 *
 *  When mouse click occurred on control element an 'month_change' event is generated.
 *  An event object has 2 additional properties:
 *  	- event.month - number of a new month (0..11)
 *  	- event.year - a year, 4 digits
 *
 *  Has refresh() method to invoke once again the current displaying month to refresh calendar table.
 */

function CalendarController(container, options){
	var monthes = [
		"January",
		"February",
		"March",
		"April",
		"May",
		"June",
		"July",
		"August",
		"September",
		"October",
		"November",
		"December"
	];
	//Checking input data
	if(!container){
		throw new Error('No arguments have been given to CalendarController constructor');
	}
	if(container.tagName == undefined || container.tagName != "DIV"){
		throw new Error('Wrong container has been given to CalendarController constructor');
	};
	
	this.container = container;
	
	//Initial values
	if(!options){ options = {}; }
	var inner_options = {};
	
	inner_options.captionLeft = options.captionLeft || '<'
	inner_options.captionLeftClass = options.captionLeftClass || "CalendarControl_button-left"
	
	inner_options.captionRight = options.captionLeft || '>'
	inner_options.captionRightClass = options.captionRightClass || "CalendarControl_button-right"
	
	inner_options.captionMonthClass = options.captionMonthClass || "CalendarControl_month-caption"
	
	this.date = new Date();
	
	//Creating controls
	var left 	= document.createElement("SPAN");
	var caption = document.createElement("SPAN");
	var right 	= document.createElement("SPAN");
	
	left.className = inner_options.captionLeftClass;
	right.className = inner_options.captionRightClass;
	caption.className = inner_options.captionMonthClass;
	
	left.innerHTML = inner_options.captionLeft;
	right.innerHTML = inner_options.captionRight;
	caption.innerHTML = monthes[this.date.getMonth()] + " " + this.date.getFullYear()
	//caption.innerHTML = this.date.toLocaleString('en', {month: 'long', year: 'numeric'});

	//Events on controls
	var self = this;

	left.addEventListener('click', function(){
		self.date.setMonth(self.date.getMonth() - 1); // 0..11
		var new_month = self.date.getMonth(); 
		var new_year = self.date.getFullYear();
		
		caption.innerHTML = monthes[new_month] + " " + new_year;

		var custom_change_event = document.createEvent("Event");
		custom_change_event.month = new_month;
		custom_change_event.year = new_year;
		//alert(new_month);
		
		custom_change_event.initEvent('month_change',true,true);
		this.parentElement.dispatchEvent(custom_change_event);
	});
	right.addEventListener('click', function(){
		self.date.setMonth(self.date.getMonth() + 1); // 0..11
		var new_month = self.date.getMonth(); 
		var new_year = self.date.getFullYear();
		
		caption.innerHTML = monthes[new_month] + " " + new_year;

		var custom_change_event = document.createEvent("Event");
		custom_change_event.month = new_month;
		custom_change_event.year = new_year;

		custom_change_event.initEvent('month_change',true,true);
		this.parentElement.dispatchEvent(custom_change_event);
	});
	
	//Prevent selection
	left	.onselectstart = function(){return false;}
	right   .onselectstart = function(){return false;}
	caption .onselectstart = function(){return false;}
	
	left	.onmousedown = function(){return false;}
	right   .onmousedown = function(){return false;}
	caption .onmousedown = function(){return false;}
	
	//Append controls to container
	container.innerHTML = '';
	container.appendChild(left);
	container.appendChild(caption);
	container.appendChild(right);
	
	this.refresh = function(){
		var custom_change_event = document.createEvent("Event");
		custom_change_event.month = self.date.getMonth();
		custom_change_event.year = self.date.getFullYear();

		custom_change_event.initEvent('month_change',true,true);
		this.container.dispatchEvent(custom_change_event);
	}
}