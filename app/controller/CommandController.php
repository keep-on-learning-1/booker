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
class CommandController extends MainController{
    protected $defaultViewPath;

    public function __construct(){
        $this->defaultViewPath = BASE_PATH.'app/view/';
    }

    public function main(){

        $config = BoardroomBooker::getConfig();

        $data['config'] = $config;
        $data['number_of_bookers'] = $this->getBookersCount();
        $data['first_day'] = ($config['booker']['first_day'])?$config['booker']['first_day']:'monday';
        $data['month'] = new InitMonth( $data['first_day'] );
        $data['events'] = EventModel::getTimeIntervals($data['month']->this_month, $data['month']->this_year);

        $data['curr_page'] = 'Boardroom 1';

        $this->registerJS('CalendarController.js');
        $this->registerJS('bb_calendar.js');
        $this->registerJS('main.js');

        $this->render(array('topSection','mainPage'), $data);
        return true;
    }

    public function employee_list(){
        $data['number_of_bookers'] =  $this->getBookersCount();
        $data['employee_list'] = EmployeeModel::getEmployeeList();

        $data['curr_page'] = 'Employee List';

        $this->render(array('topSection','employeeList'), $data);
        return true;
    }

    public function add_employee_form(){
        $data['employee_data'] = $_POST;
        $data['number_of_bookers'] = $this->getBookersCount();

        $data['curr_page'] = 'Add an employee';

        $data['submit_caption'] = 'Add';
        $data['form_action'] = 'add_employee';

        $this->render(array('topSection','employeeForm'), $data);
        return true;
    }

    public function add_employee(){
        $data['number_of_bookers'] = $this->getBookersCount();
        $data['post_data'] = $_POST;

        $employee_manager = new EmployeeModel();
        if(!$employee_manager->validateEmployeeData($_POST)){
            $this->setErrors( $employee_manager->getErrors() );
            $data['curr_page'] = 'Add an employee';
            $data['submit_caption'] = 'Add';
            $data['form_action'] = 'add_employee';
            $this->render(array('topSection','employeeForm'), $data);
            return true;
        }
        if(!$employee_manager->addEmployee($_POST)){
            $this->setErrors( $employee_manager->getErrors() );
        }else{
            BoardroomBooker::setMessage("Employee {$_POST['name']}({$_POST['email']}) was added to database");
        }
        $data['employee_list'] = EmployeeModel::getEmployeeList();
        $data['curr_page'] = 'Employee List';
        $this->render(array('topSection','employeeList'), $data);
        return true;
    }

    public function remove_employee(){
        parse_str($_SERVER['QUERY_STRING'], $vars);

        //if(!isset($vars['id']) || !is_numeric($vars['id'])){return true;}
        $employee_manager = new EmployeeModel();

        if(!$employee = $employee_manager->deleteEmployee($vars['id'])){
            $this->setErrors( $employee_manager->getErrors() );
        }else{
            BoardroomBooker::setMessage("Employee {$employee['name']}({$employee['email']}) has been deleted from database");
        }

        $data['number_of_bookers'] = $this->getBookersCount();
        $data['employee_list'] = EmployeeModel::getEmployeeList();
        $data['curr_page'] = 'Employee List';
        $this->render(array('topSection','employeeList'), $data);
        return true;
    }

    public function edit_employee_form(){
        //if(!isset($vars['id']) || !is_numeric($vars['id'])){return true;}
        parse_str($_SERVER['QUERY_STRING'], $vars);
        $employee_manager = new EmployeeModel();
        //$data['employee_data'] = $_POST;
        $data['number_of_bookers'] = $this->getBookersCount();
        $data['id'] = $vars['id'];

        if(!$data['employee_data'] = $employee_manager->getById($vars['id'])){
            $this->setErrors( $employee_manager->getErrors() );

            $data['employee_list'] = EmployeeModel::getEmployeeList();
            $data['curr_page'] = 'Employee List';
            $this->render(array('topSection','employeeList'), $data);
            return true;
        }

        $data['curr_page'] = 'Edit an employee';
        $data['submit_caption'] = 'Edit';
        $data['form_action'] = 'edit_employee';
        $this->render(array('topSection','employeeForm'), $data);
        return true;
    }

    public function edit_employee(){
        parse_str($_SERVER['QUERY_STRING'], $vars);

        $data['number_of_bookers'] = $this->getBookersCount();
        $data['curr_page'] = 'Edit an employee';
        $data['submit_caption'] = 'Edit';
        $data['form_action'] = 'edit_employee';
        $data['id'] = $vars['id'];

        $employee_manager = new EmployeeModel();
        if(!$old_employee = $employee_manager->getById($vars['id'])){
            $this->setErrors( $employee_manager->getErrors() );
            $this->render(array('topSection','employeeForm'), $data);
            return true;
        }elseif(!$employee_manager->validateEmployeeData($_POST)){
            $this->setErrors( $employee_manager->getErrors() );
            $this->render(array('topSection','employeeForm'), $data);
            return true;
        }elseif(!$employee_manager->editEmployee($vars['id'], $_POST)){
            $this->setErrors( $employee_manager->getErrors() );
            $this->render(array('topSection','employeeForm'), $data);
            return true;
        }else{
            BoardroomBooker::setMessage("Employee {$old_employee['name']}({$old_employee['email']}) was changed to {$_POST['name']}({$_POST['email']})");
            $data['employee_list'] = EmployeeModel::getEmployeeList();
            $data['curr_page'] = 'Employee List';
            $this->render(array('topSection','employeeList'), $data);
        }
        return true;
    }

    public function book_it(){
        $data['config'] = BoardroomBooker::getConfig();
        $data['number_of_bookers'] = $this->getBookersCount();

        $data['curr_page'] = 'Book It!';
        $data['months'] = EventModel::getMonthNames();
        $data['year'] = date('Y');

        $data['employees_list'] = EmployeeModel::getEmployeeList();
        if(!is_array( $data['employees_list'])){ $this->employees_list = array();}

        $this->render(array('topSection','bookIt'), $data);
        return true;
    }

    public function create_event(){
        $event_manager = new EventModel();
        if(!$event_manager->createEvent($_POST)){
            $this->setErrors( $event_manager->getErrors() );
            $data['config'] = BoardroomBooker::getConfig();
            $data['number_of_bookers'] = $this->getBookersCount();

            $data['curr_page'] = 'Book It!';
            $data['months'] = EventModel::getMonthNames();
            $data['year'] = date('Y');

            $data['employees_list'] = EmployeeModel::getEmployeeList();
            if(!is_array( $data['employees_list'])){ $this->employees_list = array();}

            $this->render(array('topSection','bookIt'), $data);
            return true;
        }

        $config = BoardroomBooker::getConfig();
        $data['config'] = $config;
        $data['number_of_bookers'] = $this->getBookersCount();
        $data['first_day'] = ($config['booker']['first_day'])?$config['booker']['first_day']:'monday';
        $data['month'] = new InitMonth( $data['first_day'] );
        $data['events'] = EventModel::getTimeIntervals($data['month']->this_month, $data['month']->this_year);

        $data['curr_page'] = 'Boardroom 1';

        $this->registerJS('CalendarController.js');
        $this->registerJS('bb_calendar.js');
        $this->registerJS('main.js');

        // all informative messages was created inside the $event_manager->createEvent() method
        $this->render(array('topSection','mainPage'), $data);
        return true;

    }

    public function event_details(){
        parse_str($_SERVER['QUERY_STRING'], $vars);
        //if(!$vars['id']){die;}
        $data['id'] = $vars['id'];
        $data['event'] = EventModel::getById($vars['id']);
        $data['employees_list'] = EmployeeModel::getEmployeeList();

        $this->render(array('eventDetails'), $data);
        return true;
    }

    public function login(){
        if(!UserModel::login($_POST)){
            $this->render('signIn', array());
            die;
        }
        header("Location: http://".$_SERVER['HTTP_HOST'].'/index.php');
        die;
    }

    public function error(){
        $this->render(array('error'), array());
    }

    public function __call($name, $args){
        header("Location: http://".$_SERVER['HTTP_HOST'].'/index.php?action=main');
        die;
    }
}