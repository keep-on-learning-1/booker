<?php

class AjaxController{
    public function __construct(){
        require_once('./app/Autoloader.php');
    }

    public function getCalendarData(){
        $month = $_POST['month'];
        $year = $_POST['year'];

        $data = EventModel::getTimeIntervals($month, $year);
        die(json_encode($data));
    }

    public function updateEvent(){
        $event_manager = new EventModel();

        if(!$event_manager->updateEvent($_POST)){
            $arr = array('result' => false, 'cause' => $event_manager->getErrors());
            die(json_encode($arr));
        }
        die(json_encode(array('result' => true, 'cause' => '')));
    }

    public function deleteEvent(){
        $event_manager = new EventModel();

        if(!$event_manager->deleteEvent($_POST)){
            $arr = array('result' => false, 'cause' => $event_manager->getErrors());
            die(json_encode($arr));
        }
        die(json_encode(array('result' => true, 'cause' => '')));
    }

    public function __call($name, $args){
        return false;
    }
}
