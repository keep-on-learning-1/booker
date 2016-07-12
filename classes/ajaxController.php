<?php

class AjaxController{
    public function __construct(){
        require_once('./classes/autoloader.php');
    }

    public function getCalendarData(){
        $month = $_POST['month'];
        $year = $_POST['year'];

        $data = EventManager::getTimeIntervals($month, $year);
        die(json_encode($data));
    }

    public function updateEvent(){
        $event_manager = new EventManager();

        if(!$event_manager->updateEvent($_POST)){
            $arr = array('result' => false, 'cause' => $event_manager->getErrors());
            die(json_encode($arr));
        }
        die(json_encode(array('result' => true, 'cause' => '')));
    }

    public function deleteEvent(){
        $event_manager = new EventManager();

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
