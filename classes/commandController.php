<?php
/*
 * Methods:
 *  - main
 *  - employee_list
 *  - add_employee_form
 *  - add_employee
 *  - remove_employee
 *  - edit_employee_form
 *  - edit_employee
 *  - book_it
 *  -
 *  - login
 *  - logout
 *  - __call
 */
class CommandController{
    private $booker;
    public function __construct($booker){
        $this->booker = $booker;
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
        $this->booker->setPageData(null);
        BoardroomBooker::setMessage("Employee {$old_employee['name']}( {$old_employee['email']}) was changed to {$_POST['name']}({$_POST['email']})");
        return true;
    }

    public function book_it(){
        $this->booker->setPage('bookIt');
        return true;
    }

    public function create_event(){
        $this->booker->setPage('bookIt');
        $event_manager = new EventManager();
        if(!$event_manager->createEvent($_POST)){
            $errors = $event_manager->getErrors();
            if(is_array($errors)){
                foreach($errors as $e){
                    BoardroomBooker::setMessage($e, 'msg-error');
                }
            }
            return true;
        }
        $this->booker->setPage('mainPage');
        // all informative messages was created inside the $event_manager->createEvent() method
        return true;
    }

    public function login(){
        if(!User::login($_POST)){
            $this->booker->setPage('signIn');
            return true;
        }
        $this->booker->setPage('mainPage');
        return true;
    }
    public function logout(){
        unset($_SESSION['user']);
        header("Location: http://".$_SERVER['HTTP_HOST'].'/index.php');
        return true;
    }
    public function __call($name, $args){
        return false;
    }

    public function event_details(){

        parse_str($_SERVER['QUERY_STRING'], $vars);
        if(!$vars['id']){die;}

        $this->booker->setPage('eventDetails');
        $this->booker->setPageData($vars['id']);

        return true;
    }
}