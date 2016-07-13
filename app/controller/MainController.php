<?php
abstract class MainController{
    private $js_stack;
    private $css_stack;
    private $pageTemplate = 'mainTemplate.php';
    protected $defaultViewPath;

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

    public function registerCSS($file){
        $this->css_stack[] = $file;
    }
    public function getCSS(){
        return ($this->css_stack)?$this->css_stack:array();
    }

    public function registerJS($file){
        $this->js_stack[] = $file;
    }
    public function getJS(){
        return ($this->js_stack)?$this->js_stack:array();
    }

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

    protected function getBookersCount(){
        $config = BoardroomBooker::getConfig();
        $bookers_number = (int)$config['booker']['number_of_bookers'];
        return  ($bookers_number)?$bookers_number:3;
    }

    public function logout(){
        unset($_SESSION['user']);
        header("Location: http://".$_SERVER['HTTP_HOST'].'/index.php');
        die;
    }

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
}
