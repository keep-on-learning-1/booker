<?php
session_start();
require_once('/classes/booker.php');
$booker = BoardroomBooker::getInstance();
$booker->init();
$booker->invokePage();
//var_dump($booker);

//TODO: remove helpers
function dd($i){
    var_dump($i); die;
}
function ed($i){
    echo "<pre>";
    var_export($i);
    echo "</pre>";
    die;
}