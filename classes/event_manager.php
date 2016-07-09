<?php
/*
 * Methods:
 *  - createEvent
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
            if($res > 0){
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
     * Method creates time ranges for an event using start time objects and informtion
     * about event recurrence.
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
     * Checks if time of the event overlaps with time intervals of already scheduled events
     * Returns 0 if no overlaps found.
     */
    public function checkIsTimeAvailable($time_arr){
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
	              WHERE LEAST(UNIX_TIMESTAMP(end_time), :end_time) -
	                    GREATEST(UNIX_TIMESTAMP(start_time), :start_time) > 0";
        $stmt = $db->prepare($query);
        $start =  $time_arr['start']->getTimestamp();
        $end = $time_arr['end']->getTimestamp();
        $res = $stmt->execute( array('start_time' => $start,'end_time' => $end));

        if(!$res){
            echo $this->errors[] = $stmt->errorInfo()[2];
            return false;
        }
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

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
    public function insertEventBody($data){
        $db = BoardroomBooker::getDB();
        $query = "INSERT INTO events (recurring, employee_id, specifics)
                  VALUES (:recurring, :employee_id, :specifics)";
        $stmt = $db->prepare($query);
        $res = $stmt->execute(array('recurring'=>(int)$data['is_recurred'],
                                    'employee_id'=>$data['employee'],
                                    'specifics'=>$data['specifics']) );
        if(!$res){
            echo $this->errors[] = $stmt->errorInfo()[2];
            return false;
        }
        return $db->lastInsertId();
    }

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
        if(!$res){
            echo $this->errors[] = $stmt->errorInfo()[2];
            return false;
        }
        return true;
    }

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
}