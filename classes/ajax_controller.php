<?php

class AjaxController{
    public function __construct(){
        require_once('./classes/booker.php');
    }

    public function getCalendarData(){
        //var_dump($_POST); die;
        $month = $_POST['month'];
        $year = $_POST['year'];

        /*if(!$_POST['month'] || !$_POST['year'] ){
            $date = new DateTime();
            $month = $date->format('m');
            $year = $date->format('Y');
        }else{
            $month = $_POST['month'];
            $year = $_POST['year'];
        }*/
        require_once("./classes/event_manager.php");
        $data = EventManager::getTimeIntervals($month, $year);
        die(json_encode($data));
    }

    public function updateEvent(){
        die(json_encode(array('result' => true, 'cause' => 'aaaaaa')));
        var_dump($_POST);
    }

    public function __call($name, $args){
        return false;
    }
}
