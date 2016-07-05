<?php
class CommandController{
    private $booker;
    public function __construct($booker){
        $this->booker = $booker;
    }

    public function index(){
        $this->booker->setPage('mainPage');
        return true;
    }
    public function main(){
        $this->booker->setPage('mainPage');
        return true;
    }
    public function employee_list(){
        $this->booker->setPage('employeeList');
        return true;
    }

    public function add_employee_form(){
        $this->booker->setPage('addEmployee');
        return true;
    }

    public function add_employee(){
        require_once('./classes/employee_manager.php');
        $employee = new EmployeeManager();
        if(!$employee->validateEmployeeData($_POST)){
            $errors = $employee->getErrors();
            if(is_array($errors)){
                foreach($errors as $e){
                    BoardroomBooker::setMessage($e, 'msg-error');
                }
            }
            $this->booker->setPage('addEmployee');
            return true;
        }

        if(!$employee->addEmployee($_POST)){
            $errors = $employee->getErrors();
            if(is_array($errors)){
                foreach($errors as $e){
                    BoardroomBooker::setMessage($e, 'msg-error');
                }
            }
        }
        $this->booker->setPage('employeeList');
        return true;
    }

    public function remove_employee(){
        parse_str($_SERVER['QUERY_STRING'], $vars);
        if(!isset($vars['id']) || !is_numeric($vars['id'])){
            $this->booker->setPage('employeeList');
            return true;
        }
        require_once('./classes/employee_manager.php');
        $employee = new EmployeeManager();
        $employee->deleteEmployee($vars['id']);

        $this->booker->setPage('employeeList');
        return true;
    }

    public function login(){
        User::login($_POST);
        return true;
    }
    public function logout(){
        return true;
    }
    public function __call($name, $args){
        return false;
    }
}