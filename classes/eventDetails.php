<?php
class EventDetails extends PagePattern {
    private $event;
    private $employees_list;

    public function __construct($id){
        require_once('./classes/event_manager.php');
        require_once('./classes/employee_manager.php');
        $this->event = EventManager::getById($id);
        $this->employees_list = EmployeeManager::getEmployeeList();
    }

    function render(){

        $this->getHeader('Event details');
        ?>

        <div class="default_form_container">
            <h2>B.B. DETAILS</h2>
            <form method="POST" id="event_details_form" >
                <div class="line">
                    <div class="details_caption">
                        When:
                    </div><span class="line_content_bg">
                        <input type="text" name = "start_time" value="<?php echo $this->event['start']?>"> -
                        <input type="text" name = "end_time" value="<?php echo $this->event['end']?>">
                    </span>

                </div>
                <div class="line">
                    <div class="details_caption">Notes:</div><span class="line_content_bg">
                        <input type="text" name = "specifics" value="<?php echo $this->event['specifics']?>">
                     </span>
                </div>
                <div class="line">
                    <div class="details_caption">
                        Who:
                    </div><span class="line_content_bg">
                        <select name="employee">
                            <option value="0">&nbsp;</option>
                            <?php foreach($this->employees_list as $employee):?>
                               <?php $check = ($employee['id'] == $this->event['employee_id'])?'selected':'';?>
                                <option value="<?php echo $employee['id'] ?>" <?php echo $check; ?> >
                                    <?php echo $employee['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                     </span>
                </div>
                <div class="details_submited line">
                    Submited: <?php echo $this->event['modified']; ?>
                </div>
                <div class="relations_checkbox_placeholder">
                    <?php if($this->event['recurring']): ?>
                        <label>
                            <input type="checkbox" name="all_occurrences" value="1">
                            <strong>Apply to all occurrences?</strong>
                        </label>
                    <?php else: ?>
                        &nbsp;
                    <?php endif;?>
                </div>
                <p class="details_form_footer">
                    <input type="submit" value="UPDATE" id="update_button">
                    <input type="submit" value="DELETE" id="delete_button">
                </p>
            </form>
            <p>*Event details are clickable and available for edition.</p>
        </div>
            <script>
                var update_button = document.getElementById('update_button');
                var form = document.forms.event_details_form;

                var initial_values;
                window.onload = function(){
                    initial_values = getFormValues();
                }

                update_button.onclick = function(event){
                    event.preventDefault();
                    current_values = getFormValues();
                    var diff = compareFormValues(initial_values, current_values);
                    if(!diff){return;} // form was not changed

                    var ansver;
                    var xhr =  new XMLHttpRequest();
                    var formData = new FormData(form);
                    xhr.open('POST', '/index.php?action=ajax&m=updateEvent');
                    xhr.onreadystatechange = function(){
                        if(this.readyState != 4){return;}
                        if (this.status != 200) {
                            //throw new Exception( xhr.status + ': ' + xhr.statusText ); // example: 404: Not Found
                        } else {
                            var ansver = JSON.parse(this.responseText);
                            if(!ansver['result']){
                                window.opener.display_message(ansver['cause']);
                            }
                            var str = 'Event '  + initial_values.start_time + '-' + initial_values.end_time
                            + ' was changed to '+ current_values.start_time + '-' + current_values.end_time
                            + '<br> Event text: '+ current_values.specifics;

                            window.opener.display_message(str);
                            window.opener.external_controller_link.refresh();
                        }
                    }
                    xhr.send(formData);
                }

                function getFormValues(){
                    return {
                        'start_time': document.getElementsByName('start_time')[0].value,
                        'end_time'  : document.getElementsByName('end_time')[0].value,
                        'specifics' : document.getElementsByName('specifics')[0].value,
                        'employee'  : document.getElementsByName('employee')[0].value
                    }
                }

                function compareFormValues(initial_values, current_values){
                    for(var k in initial_values){
                        if(initial_values[k] != current_values[k]){return true}
                    }
                    return false;

                }
            </script>
        <?php
        $this->getFooter();
    }
}