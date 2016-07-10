<?php

class AjaxController{
    public function __construct(){
        require_once('./classes/booker.php');
    }

    public function getCalendarData(){
        $month = $_POST['month'];
        $year = $_POST['year'];

        require_once("./classes/event_manager.php");
        $data = EventManager::getTimeIntervals($month, $year);
        die(json_encode($data));
    }

    public function updateEvent(){
        require_once("./classes/event_manager.php");
        $event_manager = new EventManager();

        if(!$event_manager->updateEvent($_POST)){
            $arr = array('result' => false, 'cause' => $event_manager->getErrors());
            die(json_encode($arr));
        }
        die(json_encode(array('result' => true, 'cause' => '')));
    }

    public function __call($name, $args){
        return false;
    }
}
