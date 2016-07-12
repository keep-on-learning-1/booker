<?php
session_start();

require_once('./classes/autoloader.php');
Autoloader::getInstance()->registerLoaders();

$booker = BoardroomBooker::getInstance();
$booker->init();
//$booker->invokePage();

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