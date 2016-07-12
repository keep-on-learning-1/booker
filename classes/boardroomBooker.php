<?php
/*
 * 	Methods:
 * 		getInstance - get an instanec
 * 		init 		- initialization
 * 		invokePage 	- get HTML to display
 *
 * 		setPage
 * 		setPageData
 *
 * 		setMessage
 * 		getMessage
 * 		getDB
 * 		getConfig
 *
 */
class BoardroomBooker{
	private static $instance;
	private static $messages;
	private static $config;
	private static $db;
	private $page;
	private $pageData;

	private function __construct(){}
	
	public static function getInstance(){
		if(!self::$instance){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function init(){
		/*Check configuration*/
		if(!file_exists('booker.conf')){
			$controller = new CommandController($this);
			$controller->setupDatabase();
			die;
		}
		$config = parse_ini_file('booker.conf', 1);
		if(!$config){throw new Exception('Can\'t read configuration file');}
		/* --Check options*/
		if(	!$config['database']['db_name'] ||
			!$config['database']['db_host'] ||
			!$config['database']['db_user']
		){
			/*Did not found one or more options*/
			$controller = new CommandController($this);
			$controller->setupDatabase();
			die;
		}
		self::$config = $config;

		/* --Attempt to connect to database*/
		$db = self::getDB();
/*		if(!$db){
			$this->page = 'error';
			return;
		}*/

		$res = $db->query('SELECT COUNT(*) as count FROM users')->fetch(PDO::FETCH_ASSOC);
		if($res['count'] == 0){
			$controller = new CommandController($this);
			$controller->setupBooker();
			die;
		}

		parse_str($_SERVER['QUERY_STRING'], $variables);
		$method_name = ($variables['action'])?$variables['action']:'index';
		/*Authorization*/
		if(!$_SESSION['user'] && $method_name != 'login' ){
			$controller = new CommandController($this);
			$controller->login();
			die;
		}
		/*Handle AJAX request*/
		if($method_name=='ajax'){
			$ajax = new AjaxController();
			$ajax->$variables['m']();
			die;
		}

		/*Main or target page*/
		$controller = new CommandController($this);
		$controller->$method_name();
		//if(!$controller->$method_name()){$controller->main();}

		if(!$this->page){$this->page = 'mainPage';}
	}

	public function invokePage(){
			//$class_name = ucfirst($this->page);
			//$page_object = new $class_name($this->pageData);
			//$page_object->render();
	}

	public static function setMessage($msg, $class=''){
		self::$messages[] = array('text'=>$msg, 'class'=>$class);
	}

	public static function getMessages(){
		if(!isset(self::$messages)){
			return array();
		}
		return self::$messages;
	}

	public static function getDB(){
		if(!self::$db){
			$config =self::getConfig();
			$dsn = 'mysql:dbname='.$config['database']['db_name'].';host='.$config['database']['db_host'];
			try {
				self::$db = new PDO($dsn, $config['database']['db_user'], $config['database']['db_password']);
			} catch (PDOException $e) {
				self::setMessage('Connection failed: '.$e->getMessage(), 'msg-error');
				return false;
			}
		}
		return self::$db;
	}
	public static function getConfig(){
		if(!self::$config){
			if(!file_exists('booker.conf')){
				die('Config file was not found');
			}
			self::$config = parse_ini_file('booker.conf',1);
			if(!self::$config){die('Can\'t read config file');}
		}
		return self::$config;
	}
	public function setPage($page){
		$this->page=$page;
	}
	public function setPageData($data){
		$this->pageData = $data;
	}

	public static function getRenderedMessages(){
		$messages = self::$messages;
		$msg_html = array();
		if(isset($messages) && is_array($messages)){
			$msg_html[] = '<div id="app_messages_container">';
			foreach($messages as $msg){
				$msg_html[] = "<div class=\"app_message {$msg['class']}\">{$msg['text']}</div><br>";
			}
			$msg_html[] = '</div>';
		}
		return implode("\r\n", $msg_html);
	}
}