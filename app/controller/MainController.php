<?php

/*
 * Methods
 *  Public
 *   - render           - Send generated html to output.
 *   - registerCSS      - Add CSS file name into array $js_stack
 *   - getCSS           - Get array of CSS files names or empty array
 *   - registerJS       - Add JS file name into array $js_stack
 *   - getJS            - Get array of JS files names or empty array
 *   - includeView      - Get parsed HTML of specified view file.
 *   - setErrors        - Use loop to invoke BoardroomBooker::setError() for each cell of input array.
 *   - setTemplate      - Set template that should be used during page rendering
 *   - getBookersCount  - Get number of bookers
 *   - setupBooker      - Render a form to set application options. Write obtained values into configuration file.
 *   - setupDatabase    - Render a form to set database access data. Write obtained values into configuration file.
 *                        Generate required tables in specified database if they are not exist
 *   - createUser       - Render a form to set login and password for new user.
 *                        Create a new record for the application user in database.
 *   - login
 *   - logout
 */
class MainController{
    private $js_stack;
    private $css_stack;
    private $pageTemplate = 'mainTemplate.php';
    protected $defaultViewPath='app/view/';

    /*
     * Generates page html using view and, if specified, template files.
     * Send generated html to output.
     *
     * Default template is 'mainTemplate' and it can be changed.
     *
     * input: array|string $files, array $data
     *
     *  $files - name or array of names of view files.
     *  $data - array of variables used in views and template.
     *
     */
    public function render($files, $data){
        if(isset($data) && is_array($data)){
            extract($data);
        }
        if(!is_array($files)){$files = array($files);}
        foreach($files as $file){
            $content[] = $this->includeView($file, $data); //will be used in 'pageTemplate'
        }

        $template_path = $this->defaultViewPath.$this->pageTemplate;
        if(file_exists($template_path) && is_file($template_path)){
            ob_start();
            include_once($template_path);
            ob_end_flush();
            die;
        }else{
            ob_start();
            echo join("\r\n", $content);
            ob_end_flush();
            die;
        }
    }

    public function registerCSS($file){$this->css_stack[] = $file;}
    public function getCSS(){return ($this->css_stack)?$this->css_stack:array();}

    public function registerJS($file){$this->js_stack[] = $file;}
    public function getJS(){return ($this->js_stack)?$this->js_stack:array();}

    /*
     * Get parsed HTML of specified view file.
     * input:
     *  string $file - view file name
     *  array $data - array of variables that are used in code of view file
     *
     * return:
     *  string $content
     */
    public function includeView($file, $data){
        $path = $this->defaultViewPath.$file.'.php';
        $content = '';
        if(file_exists($path)){
            ob_start();
            extract($data);
            include_once($path);
            $content = ob_get_clean();
        }
        return $content;
    }

    /*
     * Set template that should be used during page rendering
     * input: string $template_filename - name of template file
     */
    public function setTemplate($template_filename){
        $this->pageTemplate = $template_filename;
    }


    protected function setErrors($errors){
        if(!is_array($errors)){
            throw new Exception('setErrors: Incorrect input data');
        }
        foreach($errors as $e){
            BoardroomBooker::setMessage($e, 'msg-error');
        }
    }

    /*
     * Use configuration file to get number of boardrooms
     * Use 3 by default if no value was set
     *
     * reutn int $bookers_number;
     */
    protected function getBookersCount(){
        $config = BoardroomBooker::getConfig();
        $bookers_number = (int)$config['booker']['number_of_bookers'];
        return  ($bookers_number)?$bookers_number:3;
    }


    /*
     * Render form to set BoardroomBooker options.
     * Generate CSRF protection random string that will be added to form data and checked by form data handler.
     * If there were data sent by POST, validate them and try to write into configuration file.
     *
     */
    public function setupBooker(){
        if(empty($_POST)){
            $data['csrf'] = sha1(rand(0, PHP_INT_MAX));
            setcookie('setup_booker_csrf', $data['csrf'], time()+300);
            $this->render(array('setupBooker'), $data);
            die;
        }
        else{
            $configurer = new Configurer();
            if(!$configurer->setupBooker($_POST)){
                $this->setErrors($configurer->getErrors());

                $data['csrf'] = sha1(rand(0, PHP_INT_MAX));
                setcookie('setup_booker_csrf', $data['csrf'], time()+300);
                $this->render(array('setupBooker'), $data);
            }
            setcookie('setup_booker_csrf', '', time()-300);
            header('Location: http://'.$_SERVER['HTTP_HOST']);
            die;
        }
    }

    /*
     * Render form to set database connection credentials.
     * Generate CSRF protection random string that will be added to form data and checked by form data handler.
     * If there were data sent by POST, validate them and try to write into configuration file.
     */
    public function setupDatabase(){
        if(empty($_POST)) {
            if (file_exists('booker.conf') && !UserModel::getUser()) {
                header('Location: http://' . $_SERVER['HTTP_HOST']);
            }
            $data['csrf'] = sha1(rand(0, PHP_INT_MAX));
            setcookie('setup_database_csrf', $data['csrf'], time() + 300);
            $this->render(array('setupDatabase'), $data);
            die;
        }else{
            $configurer = new Configurer();
            if(!$configurer->setupDatabase($_POST)){
                $this->setErrors($configurer->getErrors());

                $data['csrf'] = sha1(rand(0, PHP_INT_MAX));
                setcookie('setup_database_csrf', $data['csrf'], time()+300);
                $this->render(array('setupDatabase'), $data);
            }
            setcookie('setup_database_csrf', '', time()-300);
            header('Location: '.BoardroomBooker::getBaseURL());
            die;
        }
    }

    /*
     * Generate a form to create a new application user
     * Handle form data to create a record in database.
     */
    public function createUser(){
        if(empty($_POST)){
            $data['csrf'] = sha1(rand(0, PHP_INT_MAX));
            setcookie('create_user_csrf', $data['csrf'], time()+300);
            $this->render(array('createUser'), $data);
        }else{
            if(!UserModel::checkToken($_POST)){
                setcookie('create_user_csrf', '', time()-300);
                header('Location: http://'.$_SERVER['HTTP_HOST']);
                die;
            }
            $user_manager = new UserModel();
            if(!$user_manager->validateFormInput($_POST)){
                $this->setErrors($user_manager->getErrors());

                $data['csrf'] = sha1(rand(0, PHP_INT_MAX));
                setcookie('create_user_csrf', $data['csrf'], time()+300);
                $this->render(array('createUser'), array());
                die;
            }
            if(!$user_manager->create($_POST)){
                $this->setErrors( $user_manager->getErrors() );

                $data['csrf'] = sha1(rand(0, PHP_INT_MAX));
                setcookie('create_user_csrf', $data['csrf'], time()+300);
                $this->render(array('createUser'), array());
                die;
            }
            setcookie('create_user_csrf', '', time()-300);
            header('Location: http://'.$_SERVER['HTTP_HOST']);
            die;
        }
    }
    public function login(){
        if(!UserModel::login($_POST)){
            $this->render('signIn', array());
            die;
        }
        header("Location: http://".$_SERVER['HTTP_HOST'].'/index.php');
        die;
    }

    public function logout(){
        UserModel::logout();
        header("Location: http://".$_SERVER['HTTP_HOST'].'/index.php');
        die;
    }
}
