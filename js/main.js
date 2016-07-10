var external_controller_link;
function refresh_table(){
    external_controller_link.refresh();
}
function display_message(message, msg_class){
    var containner = document.getElementById('app_messages_container');
    var str = [
        '<div id="app_messages_container">',
        '<div class="app_message '+ msg_class +'">' + message + "</div><br>",
        '</div>'
    ];
    containner.innerHTML = containner.innerHTML + str.join("\r\n");
}

window.onload = function(){
    var controller_container = document.getElementById('calendar-controller-container');
    var calendarController_instance = new CalendarController(controller_container);
    external_controller_link = calendarController_instance;

    var calendar_container = document.getElementById('calendar_container');
    var calendar_instance = new bb_calendar(calendar_container, calendarController_instance)

    /* Event handler */
    document.addEventListener('month_change', function(event){
        var calendars_list = document.getElementsByClassName('bb_calendar');
        for(var i=0;i<calendars_list.length;i++){
            var current_calendar = calendars_list[i].dm_calendar;
            if(current_calendar.depends_of(event)){
                //console.log(event.year, event.month);
                requestMonth(event.year, event.month, current_calendar);
            }
        }
    });

    function requestMonth(year, month, calendar){
        var xhr =  new XMLHttpRequest();
        var ansver;
        var formData = new FormData
        formData.append('month', month+1);  // server script expects value of 1..12
        formData.append('year', year);
        xhr.open('POST', '/index.php?action=ajax&m=getCalendarData');
        xhr.onreadystatechange = function(){
            if(this.readyState != 4){return;}
            if (this.status != 200) {
                //throw new Exception( xhr.status + ': ' + xhr.statusText ); // example: 404: Not Found
            } else {
                calendar.setData( JSON.parse(this.responseText) );
                //calendar.setData( this.responseText );
                calendar.change_month(year, month);
            }
        }
        xhr.send(formData);
        return ansver;
    }

    calendar_container.addEventListener('click', function(event){
        var target = event.target;
        while(target != this){
            if(target.classList.contains('event_time')){
                event.preventDefault();
                var newWin = window.open("index.php?action=event_details&id="+target.getAttribute('data-id'), "details", "width=400,height=350");
                return;
            }
            target = target.parentNode;
        }

    });
};
