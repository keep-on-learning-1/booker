<?php
session_start();
require_once('/classes/booker.php');
require_once('/classes/user.php');
require_once('/classes/commandController.php');
$booker = BoardroomBooker::getInstance();
$booker->init();
$booker->invokePage();

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