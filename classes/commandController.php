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
        $employee_manager = new EmployeeManager();
        if(!$employee_manager->validateEmployeeData($_POST)){
            $errors = $employee_manager->getErrors();
            if(is_array($errors)){
                foreach($errors as $e){
                    BoardroomBooker::setMessage($e, 'msg-error');
                }
            }
            $this->booker->setPage('addEmployee');
            return true;
        }

        $this->booker->setPage('employeeList');

        if(!$employee_manager->addEmployee($_POST)){
            $errors = $employee_manager->getErrors();
            if(is_array($errors)){
                foreach($errors as $e){
                    BoardroomBooker::setMessage($e, 'msg-error');
                }
            }
            return true;
        }
        BoardroomBooker::setMessage("Employee {$_POST['name']}({$_POST['email']}) was added to database");
        return true;
    }

    public function remove_employee(){
        $this->booker->setPage('employeeList');
        parse_str($_SERVER['QUERY_STRING'], $vars);

        if(!isset($vars['id']) || !is_numeric($vars['id'])){
            return true;
        }

        require_once('./classes/employee_manager.php');
        $employee_manager = new EmployeeManager();

        if(!$employee = $employee_manager->deleteEmployee($vars['id'])){
            $errors = $employee_manager->getErrors();
            if(is_array($errors)){
                foreach($errors as $e){
                    BoardroomBooker::setMessage($e, 'msg-error');
                }
            }
            return true;
        }
        BoardroomBooker::setMessage("Employee {$employee['name']}({$employee['email']}) has been deleted from database");
        return true;
    }
    public function edit_employee_form(){

        $this->booker->setPage('employeeList');

        parse_str($_SERVER['QUERY_STRING'], $vars);
        if(!isset($vars['id']) || !is_numeric($vars['id'])){
            return true;
        }

        require_once('./classes/employee_manager.php');
        $employee_manager = new EmployeeManager();
        if(!$employee = $employee_manager->getById($vars['id'])){
            $errors = $employee_manager->getErrors();
            if(is_array($errors)){
                foreach($errors as $e){
                    BoardroomBooker::setMessage($e, 'msg-error');
                }
            }
            return true;
        }

        $this->booker->setPage('editEmployee');
        $this->booker->setPageData($employee);
        return true;
    }
    public function edit_employee(){

        $this->booker->setPage('editEmployee');
        $this->booker->setPageData($_POST);

        parse_str($_SERVER['QUERY_STRING'], $vars);
        if(!isset($vars['id']) || !is_numeric($vars['id'])){
            return true;
        }

        require_once('./classes/employee_manager.php');
        $employee_manager = new EmployeeManager();

        if(!$old_employee = $employee_manager->getById($vars['id'])){
            $errors = $employee_manager->getErrors();
            if(is_array($errors)){
                foreach($errors as $e){
                    BoardroomBooker::setMessage($e, 'msg-error');
                }
            }
            return true;
        }
        if(!$employee_manager->validateEmployeeData($_POST)){
            $errors = $employee_manager->getErrors();
            if(is_array($errors)){
                foreach($errors as $e){
                    BoardroomBooker::setMessage($e, 'msg-error');
                }
            }
            return true;
        }

        if(!$employee_manager->editEmployee($vars['id'], $_POST)){
            $errors = $employee_manager->getErrors();
            if(is_array($errors)){
                foreach($errors as $e){
                    BoardroomBooker::setMessage($e, 'msg-error');
                }
            }
            return true;
        }
        $this->booker->setPage('employeeList');
        //$this->booker->setPageData(null);
        BoardroomBooker::setMessage("Employee {$old_employee['name']}( {$old_employee['email']}) was changed to {$_POST['name']}({$_POST['email']})");
        return true;
    }

    public function login(){
        User::login($_POST);
        return true;
    }
    public function logout(){
        User:logout();
        return true;
    }
    public function __call($name, $args){
        return false;
    }
}