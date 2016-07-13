<?php
/*
 * Application launcher and registry.
 *
 * Check existence of required configuration values to launch application.
 * Launch installer if it is the first launch or if configuration data were lost.
 *
 * Provides methods to set and get informative messages.
 * Stores link to database object and array of application settings.
 *
 * 	Methods:
 * 		getInstance - get an instance
 * 		init 		- initialization
 *
 * 		setMessage
 * 		getMessage
 * 		getDB
 * 		getConfig
 */
class BoardroomBooker{
	private static $instance;
	private static $messages;
	private static $config;
	private static $db;

	private function __construct(){}
	
	public static function getInstance(){
		if(!self::$instance){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function init(){
		try{
			FrontController::getInstance()->route();
		}catch(Exception $e){
			//BoardroomBooker::setMessage( $e->getMessage() );
			$controller = new CommandController();
			$controller->main();
			die;
		}
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
				return false;
			}
			self::$config = parse_ini_file('booker.conf',1);
			if(!self::$config){
				return false;
			}
		}
		return self::$config;
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

	public static function setDefferedMessage($message){
		$index = count($_SESSION['deffered_messages']);
		$_SESSION['deffered_messages'][$index]['countdown'] = 1;
		$_SESSION['deffered_messages'][$index]['message'] = $message;
	}

	public static function getBaseURL(){
		if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'){
			$scheme = 'https://';
		}else{
			$scheme = 'http://';
		}
		return $scheme.$_SERVER['HTTP_HOST'];
	}
}