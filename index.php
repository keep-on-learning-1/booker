<?php
session_start();
define(BASE_PATH, __DIR__ . DIRECTORY_SEPARATOR);

require_once(BASE_PATH.'/app/Autoloader.php');
Autoloader::getInstance()->registerLoaders();

//$front = FrontController::getInstance();
//$front->route();

$booker = BoardroomBooker::getInstance();
$booker->init();

