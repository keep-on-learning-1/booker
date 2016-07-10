/**
* ����� ���������. (IE 9+)
* � �������� ���������� ����������� ��������� ������ �� ���������(������� DIV) � 
* ��������� ������� CaltndarController.
* ��� �������� ���������� ������� ���������� ����������� css ����� "DM_calendar"
* ������ �� ��������� ������ ������� � �������� DM_calendar ��������-����������
*
* ��� ����� ������ CaltndarController ���������� ������� month_change.
* ��� ������� ����� 2 ��������: month � year.
* ������� ���������� �� ��������-����������. ������ �� ����� � event.target
*
* ��������� ������:
* 	- depends_of( event ) - ���������, ���� �� ������� month_change ������� �� ��� �� ��������, 
*	  ������� ��� ������� � �������� controllert �������� ���������� ������.
*	- change_month( year, months ) - ��������� ����������� container � ������������ � ���������
*	  ����� � �������.
*	  ��� ���������� ������� ��� ������ ������ ���������� ��������� ����� dayRenderer. ���� ����� 
* 	  ����� �������������� ��� ���������� ����� HTML-���������.
*   - setDayRenderer - �������������� ����� dayRenderer, ������� ���������� ��� ����������
*	  ������ ���������.
*
* ��� ������� ������������ ���������� month_change ������� �� document.
* ��� ������������� month_change ���������� ��� ������� DM_calendar � ������������ � �����.
* ��� ��� �� ���, � ������� ������� controller ������ � event.target, ���������� ����� 
* change_month(year, month).
*
* 	methods:
 * 		-
 *
 * 	document.addEventListener('month_change', function(event){
*		var calendars_list = document.getElementsByClassName('DM_calendar');
*		for(var i=0;i<calendars_list.length;i++){
*			var current_calendar = calendars_list[i].dm_calendar;
*			if(current_calendar.depends_of(event)){
*				current_calendar.change_month(event.year, event.month);
*			}
*		}
*	});
*
*/

function bb_calendar(container, controller, options){
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
	container.dm_calendar = this;
	
	/* Public methods*/
	this.change_month = change_month;
	this.depends_of = depends_of;
	this.setDayRenderer = function(func){
		if(typeof(func) != 'function'){return;}
		dayRenderer = func; // global variable
	};
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