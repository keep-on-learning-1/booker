window.onload = function(){
    var controller_container = document.getElementById('calendar-controller-container');
    var calendarController_instance = new CalendarController(controller_container);

    var calendar_container = document.getElementById('calendar_container');
    var calendar_instance = new bb_calendar(calendar_container, calendarController_instance)
    calendar_instance.setDayRenderer(
        //function(){return 'sometext';}

    );
    /* Event handler */
    document.addEventListener('month_change', function(event){
        var calendars_list = document.getElementsByClassName('bb_calendar');
        for(var i=0;i<calendars_list.length;i++){
            var current_calendar = calendars_list[i].dm_calendar;
            if(current_calendar.depends_of(event)){
                current_calendar.change_month(event.year, event.month);
            }
        }
    });
};
