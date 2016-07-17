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
 * 	 - getInstance
 * 	 - init					- Invoke FrontController->route and handle error if occurred.
 * 	 - setMessage			- Put message in stack
 * 	 - getMessages			- Get array of messages
 * 	 - getDB				- Get instance of PDO class
 * 	 - getConfig			- Get values from configuration file as an array
 *   - getRenderedMessages  - Get rendered HTML for all messages
 *   - getBaseURL			- Get absolute URL
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

	/*
	 * Put a message into array of messages
	 * input
	 * 	 $msg - text ov the message
	 * 	 $class - CSS class htat will be used during rendering
	 */
	public static function setMessage($msg, $class=''){
		self::$messages[] = array('text'=>$msg, 'class'=>$class);
	}

	public static function getMessages(){
		if(!isset(self::$messages)){
			return array();
		}
		return self::$messages;
	}

	/*
	 * Get an instance of PDO
	 *
	 * return PDO $db|false
	 */
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

	/*
	 * Get an array of application options from configuration file
	 *
	 * return array $config|false
	 */
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

	/*
	 * Get html for informative messages that can be used in view files.
	 *
	 * return string
	 */
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

	/*
	public static function setDefferedMessage($message){
		$index = count($_SESSION['deffered_messages']);
		$_SESSION['deffered_messages'][$index]['countdown'] = 1;
		$_SESSION['deffered_messages'][$index]['message'] = $message;
	}
	*/

	public static function getBaseURL(){
		if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'){
			$scheme = 'https://';
		}else{
			$scheme = 'http://';
		}
		return $scheme.$_SERVER['HTTP_HOST'];
	}
}