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
	
	inner_options.captionMonthClass = options.captionRightClass || "CalendarControl_month-caption"
	
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