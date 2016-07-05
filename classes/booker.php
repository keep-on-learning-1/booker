<?php
/*
 * Êëàññ ïğåäñòàâëÿåò îáúåêò ïğèëîæåíèÿ.
 * 		Ìåòîäû:
 * 			getInstance - ïîëó÷åíèå ıêçåìïëÿğà îáúåêòà
 * 			init 		- èíèöèàëèçàöèÿ îáúåêòà. Îïğåäåëÿåò êàêàÿ ñòğàíèöà áóäåò îòîáğàæåíà
 * 			invokePage 	- ïîëó÷åíèå îáúåêòà Page. Ïîëó÷åíèå html êîäà, êîòîğûé áóäåò îòîáğàæåí.
 *
 * Ìåòîä init ïğè âûçîâå ïğîâåğÿåò ñîçäí ëè ôàéë booker.conf. Åñëè ôàéëà íå ñóùåñòâóåò - ïğåäïîëàãàåòñÿ, ÷òî
 * ıòî ïåğâûé çàïóñê ïğèëîæåíèÿ è, ïî î÷åğåäè, áóäóò îòîáğàæåíû ñòğàíèöû setup_database è setup_booker.
 *
 */
class BoardroomBooker{
	private static $instance;
	private static $messages;
	private static $config;
	private static $db;
	private $page;

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
			$this->page = 'setupDatabase';
			return;
		}
		$config = parse_ini_file('booker.conf', 1);
		if(!$config){throw new Exception('Can\'t read configuration file');}
		/* --Check options*/
		if(	!$config['database']['db_name'] ||
			!$config['database']['db_host'] ||
			!$config['database']['db_user'] ||
			!$config['database']['db_prefix']
		){
			/*Did not found one or more options*/
			$this->page = 'setupDatabase';
			return;
		}
		self::$config = $config;
		/* --Attempt to connect to database*/
		$db = self::getDB();
		if(!$db){
			$this->page = 'error';
			return;
		}
		$res = $db->query('SELECT COUNT(*) as count FROM users')->fetch(PDO::FETCH_ASSOC);
		if($res['count'] == 0){
			$this->page = 'setupBooker';
			return;
		}

		parse_str($_SERVER['QUERY_STRING'], $variables);
		$method_name = ($variables['action'])?$variables['action']:'index';

		/*Authorization*/
		if(!$_SESSION['user'] && $method_name != 'login' ){
			$this->page = 'signIn';
			return;
		}

		/*Main or target page*/
		$controller = new CommandController($this);
		if(!$controller->$method_name()){$controller->main();};

		if(!$this->page){$this->page = 'mainPage';}
	}

	public function invokePage(){
		$file = './classes/' . $this->page . '.php';
		if(file_exists($file)){
			include_once './classes/page_pattern.php';
			include_once $file;
			$class_name = ucfirst($this->page);
			$page_object = new $class_name();
			$page_object->render();
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
		return self::$config;
	}
	public function setPage($page){
		$this->page=$page;
	}
}