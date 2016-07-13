<?php
abstract class AbstractController{
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
}
