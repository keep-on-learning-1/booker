<?php
/*
 * Methods:
 *  - createEvent
 *  - getErrors
 *  - checkFields
 *  - validateTime
 *  - setTime
 *  - getTime
 *  - createEventTime
 *  - checkIsTimeAvailable
 *  - getStringInterval
 *  - insertEventBody
 *  - insertEventTime
 *  - getTimeIntervals
 *  - getById
 *  - updateEvent
 *  - updateEvent
 *  - get24hTime
 */
class EventManager{
    private $errors;
    private $config;
    private $start_time;
    private $end_time;

    public function __construct(){
        $this->config = BoardroomBooker::getConfig();
    }

    public function createEvent($data){
        if(!$data){
            $this->errors[] = "Event cat't be created. Wrong input data";
            return false;
        }

        $this->data = $data;

        if(!$this->checkFields($data)){
            return false; // error message has been already created
        }
        if(!$this->setTime($data)){
            return false; // error message has been already created
        }
        if(!$this->validateTime($this->getTime())){
            return false; // error message has been already created
        }

        $all_time_intervals = $this->createEventTime($this->getTime(),$data);
        foreach($all_time_intervals as $time_interval){
            $res = $this->checkIsTimeAvailable($time_interval);
            if($res > 0 || $res === false){
                $this->errors[] = 'Specified event time overlaps with another event:'.
                                  '<br>'.$this->getStringInterval($time_interval);
            }
        }
        if($this->errors){return false;}

        if(!$id=$this->insertEventBody($data)){
            return false; // error message has been already created
        }
        if(!$this->insertEventTime($id, $all_time_intervals)){
            return false; // error message has been already created
        }

        foreach($all_time_intervals as $interval){
            $str = 'The event '.$this->getStringInterval($interval). ' has been added.';
            $str.= '<br>The text for this event is: '. $data['specifics'];
            BoardroomBooker::setMessage($str);
        }
        return true;
    }

    public function getErrors(){
        return $this->errors;
    }

    /*
     * Method checks whether all of required fields were got from the form.
     */
    function checkFields($data){
        if( !$data['employee']            ||
            !isset($data['event_month'])  ||
            !isset($data['event_day'])    ||
            !isset($data['event_year'])   ||
            !isset($data['start_hours'])  ||
            !isset($data['start_minutes'])||
            !isset($data['end_hours'])    ||
            !isset($data['end_minutes'])  ||
            !isset($data['is_recurred'])
        ){
            return false;
        }
        if($this->config['booker']['time_format'] == '12h' && (!$data['start_ampm'] || !$data['end_ampm'])){
            $this->errors[] = "Required fields were not sent";
            return false;
        }

        if($this->config['booker']['time_format'] == '12h' && ($data['start_hours']>12 || $data['end_hours']>12)){
            $this->errors[] = "checkFields: Corrupted input data";
            return false;
        }

        if($data['is_recurred'] && !$data['duration']){
            $this->errors[] = 'Number of recurrences was not specefied for recurring event';
            return false;
        }
        return true;
    }

    public function validateTime($time_arr){
        if(!is_array($time_arr)){
            $this->errors[] = "validateTime: Corrupted input data";
            return false;
        }
        $start = $time_arr['start']->getTimestamp();
        $end   = $time_arr['end']  ->getTimestamp();

        if(time() > $start ){
            $this->errors[] = "The past time was specified for the event.";
            return false;
        }

        if( $end <= $start ){
            $this->errors[] = "The time of event ending cannot be less than the time of its start.";
            return false;
        }
        return true;
    }

    /*
     *  Method tryes to create DateTime objects for the start and the end of an event using array of data.abstract
     *  It will return true if objects are created, false otherwise.
     *  Puts created objects into appropriate protected properties.
     */
    public function setTime($data){

        $start_data_str =   ($data['event_month']+1).'-'.
            $data['event_day']      .'-'.
            $data['event_year']     .'-'.
            $data['start_hours']    .'-'.
            str_pad($data['start_minutes'],2,'0',STR_PAD_LEFT);

        $end_data_str =     ($data['event_month']+1).'-'.
            $data['event_day']      .'-'.
            $data['event_year']     .'-'.
            $data['end_hours']    .'-'.
            str_pad($data['end_minutes'],2,'0',STR_PAD_LEFT);

        $this->start_time = DateTime::createFromFormat('n-j-Y-G-i', $start_data_str);
        $this->end_time   = DateTime::createFromFormat('n-j-Y-G-i', $end_data_str);
        if(!$this->start_time) {
            $this->errors[] = "Wrong time was specified for event start.";
        }
        if(!$this->end_time){
            $this->errors[] = "Wrong time was specified for event end.";
        }
        if($this->errors){return false;}
        return true;
    }

    public function getTime(){
        if(!$this->start_time || !$this->end_time){
            return false;
        }
        return array('start' => $this->start_time, 'end' => $this->end_time);
    }

    /*
     *  Method creates time ranges for an event using start and end time objects and informtion
     *  about event recurrence.
     *
     *  return array(
     *      0 => [
     *          "start" => DateTime start
     *          "end" => DateTime end
     *      ],
     *      ...
     *      1 => [
     *          "start" => DateTime start
     *          "end" => DateTime end
     *      ]
     *  )
     */
    public function createEventTime($time_arr, $data){

        if($data['is_recurred']){
            switch($data['recurrence']){
                case 'weekly':   $interval = new DateInterval('P1W');break;
                case 'biweekly': $interval = new DateInterval('P2W');break;
                case 'monthly':  $interval = new DateInterval('P1M');break;
                default:         $interval = new DateInterval('P0D');break; // zero interval
            }
        }

        $iterations = ($data['is_recurred'])?$data['duration']:1;

        $time[0]['start'] =  $time_arr['start'];
        $time[0]['end']   =  $time_arr['end'];

        /*Link to the same object was returning for every iteration so I use cloning to get particular objects.*/
        $curr_time['start'] = clone($time[0]['start']);
        $curr_time['end']   = clone($time[0]['end']);
        for($i=1;$i<$iterations;$i++){
            $time[$i]['start'] = clone($curr_time['start']->add($interval));
            $time[$i]['end'] =   clone($curr_time['end']  ->add($interval));
        }
        return $time;
    }

    /*
     * Checks if time of the event overlaps with time intervals of already scheduled events.
     * Parameter $id is an identifier of checking event time slot. It allows to create a new time slot for event
     * that overlaps with its old one.
     * Returns 0 if no overlaps found, false if error occurred
     *
     * input: array(
     *      'start' => DateTime start,
     *      'end' => DateTime end
     *  )
     */
    public function checkIsTimeAvailable($time_arr, $self_id = 0){
        if(!is_array($time_arr) || !$time_arr['start'] || !$time_arr['end']){
            $this->errors[] = 'checkIsTimeAvailable: Corrupted input data';
            return false;
        }
        if(!is_a($time_arr['start'], 'DateTime') || !is_a($time_arr['end'], 'DateTime')){
            $this->errors[] = 'checkIsTimeAvailable: Corrupted input data';
            return false;
        }

        $db = BoardroomBooker::getDB();
        $query = "SELECT COUNT(*) FROM times
	              WHERE LEAST(UNIX_TIMESTAMP(end_time), UNIX_TIMESTAMP(:end_time)) - GREATEST(UNIX_TIMESTAMP(start_time), UNIX_TIMESTAMP(:start_time)) > 0
	              AND id <> :self_id";
        $stmt = $db->prepare($query);

        /*Time string used instead of timestamp to avoid problem with time zone*/
        $start =  $time_arr['start']->format('Y-m-d H:i:s');
        $end = $time_arr['end']->format('Y-m-d H:i:s');

        $res = $stmt->execute( array('start_time' => $start,'end_time' => $end, 'self_id' => $self_id));
        if(!$this->checkRes($res, $stmt)){return false;}

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /*
     * Returns time interval as a string. Time is formatted according to program configuration.
     * input: array(
     *      'start' => DateTime start,
     *      'end' => DateTime end
     *  )
     */
    function getStringInterval($time_arr){
        if(!is_array($time_arr) || !$time_arr['start'] || !$time_arr['end']){
            $this->errors[] = 'getStringInterval: Corrupted input data';
            return false;
        }
        if(!is_a($time_arr['start'], 'DateTime') || !is_a($time_arr['end'], 'DateTime')){
            $this->errors[] = 'getStringInterval: Corrupted input data';
            return false;
        }

        $config = BoardroomBooker::getConfig();

        if($config['booker']['time_format'] == '12h'){
            $time_string = $time_arr['start']->format('g:ia');
            $time_string.= $time_arr['end']  ->format(' - g:ia (F d, Y)');
        }
        else{
            $time_string = $time_arr['start']->format('H:i');
            $time_string.= $time_arr['end']  ->format(' - H:i (F d, Y)');
        }
        return $time_string;
    }

    /*
     * Create a row in a table of events in database.
     * Uses data from the event creation form.
     * Returns last insert id or false if error occurred.
     */
    public function insertEventBody($data){
        $db = BoardroomBooker::getDB();
        $query = "INSERT INTO events (recurring, employee_id, specifics)
                  VALUES (:recurring, :employee_id, :specifics)";
        $stmt = $db->prepare($query);

        $res = $stmt->execute(array('recurring'=>(int)$data['is_recurred'],
                                    'employee_id'=>$data['employee'],
                                    'specifics'=>$data['specifics']) );
        if(!$this->checkRes($res, $stmt)){return false;}

        return $db->lastInsertId();
    }

    /*
     * Create a row in a table of event time in database.
     * Uses database id of last inserted event and array of DateTime objects that represents
     * the start and the end of an event.
     * Returns true if row created or false if error occurred.
     */
    public function insertEventTime($id, $intervals){
        $db = BoardroomBooker::getDB();

        foreach($intervals as $key => $interval){
            $query_parts[] = "(  FROM_UNIXTIME(:start_time_{$key}),
                                 FROM_UNIXTIME(:end_time_{$key}),
                                 :event_id_{$key}
                               )";
            $values['start_time_'.$key] = $interval['start']->getTimestamp();
            $values['end_time_'.$key] = $interval['end']->getTimestamp();
            $values['event_id_'.$key] = $id;
        }
        $query = "INSERT INTO times (start_time, end_time, event_id) VALUES ".implode(', ',$query_parts);
        $stmt=$db->prepare($query);

        $res = $stmt->execute($values);
        if(!$this->checkRes($res, $stmt)){return false;}

        return true;
    }

    /*
     * Returns time for all events in the specified month(and year)
     * Used to populate calendar with time intervals.
     */
    public static function getTimeIntervals($month, $year){
        $db = BoardroomBooker::getDB();
        $config =BoardroomBooker::getConfig();

        $start = new DateTime("first day of $year-$month");
        $end = new DateTime("last day of $year-$month, 23:59");
        if(!$start || !$end){
            BoardroomBooker::setMessage( "Wrong input data", 'msg-errors');
            return false;
        }

        $format = ($config['booker']['time_format'] == '12h')?'%h:%i%p':'%H:%i';
        $query = "SELECT DAY(start_time) as day,
                         LCASE(DATE_FORMAT(start_time,'{$format}')) as start,
                         LCASE(DATE_FORMAT(end_time,'{$format}')) as end,
                         event_id,
                         id
                  FROM times
                  WHERE start_time >= FROM_UNIXTIME(".$start->getTimestamp().")
                  AND end_time <= FROM_UNIXTIME(".$end->getTimestamp().")";
        $res=$db->query($query);
        if(!$res){
            BoardroomBooker::setMessage($db->errorInfo()[2]);
        }
        $time = $res->fetchAll( PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
        return $time;
    }

    /*
     * Return event data for specified time interval
     * Returns true if row created or false if error occurred.
     */
    public static function getById($id){

        $db = BoardroomBooker::getDB();
        $config =BoardroomBooker::getConfig();
        $format = ($config['booker']['time_format'] == '12h')?'%h:%i%p':'%H:%i';
        $query = "SELECT DAY(start_time) as day,
                         LCASE(DATE_FORMAT(start_time,'{$format}')) as start,
                         LCASE(DATE_FORMAT(end_time,'{$format}')) as end,
                         event_id,
                         times.id as times_id,
                         recurring,
                         specifics,
                         modified,
                         employee_id,
                         name
                  FROM times
                  JOIN events ON times.event_id = events.id
                  LEFT JOIN employees ON events.employee_id = employees.id
                  WHERE times.id = $id";

        $res=$db->query($query);
        if(!$res){
            die($db->errorInfo()[2]);
        }
        $time = $res->fetch(PDO::FETCH_ASSOC);
        return $time;
    }

    /*
     * Update single or recurring events using data from event details form.
     */
    public function updateEvent($data){
        if(!$data['employee_id']){$data['employee_id'] = 0;}

        $db = BoardroomBooker::getDB();

        if($data['all_occurrences'] || !$data['was_recurring']){
            /* Get a day and a year for events to create DataTime objects.
             * It is necessary for checking new time slots of events.
             * Events should not be changed if their new time slots overlaps with the time of any other event.
             */
            $query = "SELECT id, start_time, end_time FROM times WHERE event_id=:event_id";
            $stmt = $db->prepare($query);

            $res = $stmt->execute(array('event_id' => $data['event_id']));
            if(!$this->checkRes($res, $stmt)){return false;}

            $intervals_str = $stmt->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP );
            $intervals_obj = array();
            foreach($intervals_str as $k => $interval){
                $intervals_obj[$k]['start'] = new DateTime($interval[0]['start_time']);
                $intervals_obj[$k]['end'] = new DateTime($interval[0]['end_time']);

            }

            /* Set a new time for each time object and check it*/
            $new_start_time = $this->get24hTime($data['start_time']);
            $new_end_time = $this->get24hTime($data['end_time']);
            if(!$new_start_time || !$new_end_time){
                $this->errors[] = "Incorrect event time";
                return false;
            }
            foreach($intervals_obj as $id => $time_arr){
                $time_arr['start']->setTime($new_start_time['h'], $new_start_time['m']);
                $time_arr['end']->setTime($new_end_time['h'], $new_end_time['m']);
                $res = $this->checkIsTimeAvailable($time_arr, $id);
                if($res>0 || $res === false){
                    $this->errors[] = 'Specified event time overlaps with another event'.'<br>['.$this->getStringInterval($time_arr).']';
                }

            }
            if($this->errors){return false;}

            /* An 'execute' method didn't recognize parameter markers ':start_time' and ':end_time'
             * so I insert the values directly.
             */
            $st = $new_start_time['h'].':'.$new_start_time['m'];
            $et = $new_end_time['h'].':'.$new_end_time['m'];
            $query = "UPDATE times
                           SET start_time=DATE_FORMAT(start_time, '%Y-%m-%d {$st}'),
                               end_time=DATE_FORMAT(end_time, '%Y-%m-%d {$et}')
                           WHERE event_id=:event_id";
            $stmp = $db->prepare($query);

            $res = $stmt->execute(array('event_id' => $data['event_id']));
            if(!$this->checkRes($res, $stmt)){return false;}
            //------------------
            $query = "UPDATE events SET employee_id=:employee_id, specifics=:specifics WHERE id=:event_id";
            $stmt = $db->prepare($query);
            $res = $stmt->execute(array(
                    'employee_id' => $data['employee_id'],
                    'specifics' => $data['specifics'],
                    'event_id'=> $data['event_id']
            ));
            if(!$this->checkRes($res, $stmt)){return false;}
        }else{
            /*Check if the new time is available*/
            $query = "SELECT id, start_time, end_time FROM times WHERE id=:time_id";
            $stmt = $db->prepare($query);

            $res = $stmt->execute(array('time_id' => $data['times_id']));
            if(!$this->checkRes($res, $stmt)){return false;}

            $intervals_str = $stmt->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP );

            $intervals_obj = array();
            foreach($intervals_str as $k => $interval){
                $intervals_obj[$k]['start'] = new DateTime($interval[0]['start_time']);
                $intervals_obj[$k]['end'] = new DateTime($interval[0]['end_time']);

            }
            /* Set a new time for each time object and check it*/
            $new_start_time = $this->get24hTime($data['start_time']);
            $new_end_time = $this->get24hTime($data['end_time']);
            if(!$new_start_time || !$new_end_time){
                $this->errors[] = "Incorrect event time";
                return false;
            }
            foreach($intervals_obj as $id => $time_arr){
                $time_arr['start']->setTime($new_start_time['h'], $new_start_time['m']);
                $time_arr['end']->setTime($new_end_time['h'], $new_end_time['m']);
                $res = $this->checkIsTimeAvailable($time_arr, $id);
                if($res>0 || $res === false){
                    $this->errors[] = 'Specified event time overlaps with another event'.'<br>['.$this->getStringInterval($time_arr).']';
                }

            }
            if($this->errors){return false;}
            //-------------------
            $query = "INSERT INTO events (recurring, employee_id, specifics) VALUES ( 0, :employee_id, :specifics)";
            $stmp = $db->prepare($query);

            $res = $stmt->execute(array('employee_id' => $data['employee_id'], 'specifics' => $data['specifics']));
            if(!$this->checkRes($res, $stmt)){return false;}

            $id = $db->lastInsertId();
            //------------------
            $query = "UPDATE times
                           SET start_time=DATE_FORMAT(start_time, '%Y-%m-%d {$data['start_time']}'),
                               end_time=DATE_FORMAT(end_time, '%Y-%m-%d {$data['end_time']}'),
                               event_id=$id
                           WHERE id=:times_id";
            $stmt = $db->prepare($query);
            $res = $stmt->execute(array('times_id' => $data['times_id']));
            if(!$this->checkRes($res, $stmt)){return false;}
        }
        return true;
    }

    /*
     * Check correctness of input time string.
     * Convert time to 24 hours format.
     *
     * Returns array to use in DateTime->setTime().
     */
    function get24hTime($time_str){
        $stamp = strtotime($time_str);
        if(!$stamp){
            return false;
        }
        return array('h'=>date("H", $stamp), 'm'=>date("i", $stamp));
    }

    function deleteEvent($data){
        if($data['all_occurrences'] || !$data['was_recurring']){
            $query = "DELETE FROM events WHERE id=:event_id";
            $query = "DELETE FROM times WHERE event_id=:event_id";
        }else{
            $query = "SELECT COUNT(*) FROM times WHERE id=:event_id";
            $query = "DELETE FROM times WHERE id=:event_id";
            if($count < 2){
                $query = "DELETE FROM events WHERE id=:event_id";
            }
        }
    }
    function checkRes($res, $stmt){
        if(!$res){
            $this->errors[] = $stmt->errorInfo()[2];
            return false;
        }
        return true;
    }
}