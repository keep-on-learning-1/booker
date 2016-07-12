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
}