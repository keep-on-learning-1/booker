<?php
class Autoloader{
    private static $instance;
    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct(){}

    public function registerLoaders(){
        spl_autoload_register(array($this, 'loadClassesPages'));
        spl_autoload_register(array($this, 'loadClasses'));
        spl_autoload_register(array($this, 'loadModels'));
        spl_autoload_register(array($this, 'loadControllers'));
    }

    public function loadClasses($className){
        if(file_exists('./classes/'.lcfirst($className).'.php')){
            include_once('./classes/'.lcfirst($className).'.php');
        }
    }
    public function loadClassesPages($className){
        if(file_exists('./classes/pages/'.lcfirst($className).'.php')){
            include_once('./classes/pages/'.lcfirst($className).'.php');
        }
    }
    public function loadModels($className){
        if(file_exists('./classes/models/'.lcfirst($className).'.php')){
            include_once('./classes/models/'.lcfirst($className).'.php');
        }
    }
    public function loadControllers($className){
        if(file_exists('./classes/controllers/'.lcfirst($className).'.php')){
            include_once('./classes/controllers/'.lcfirst($className).'.php');
        }
    }
}