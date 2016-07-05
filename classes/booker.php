<?php
/*
 * ����� ������������ ������ ����������.
 * 		������:
 * 			getInstance - ��������� ���������� �������
 * 			init 		- ������������� �������. ���������� ����� �������� ����� ����������
 * 			invokePage 	- ��������� ������� Page. ��������� html ����, ������� ����� ���������.
 *
 * ����� init ��� ������ ��������� ����� �� ���� booker.conf. ���� ����� �� ���������� - ��������������, ���
 * ��� ������ ������ ���������� �, �� �������, ����� ���������� �������� setup_database � setup_booker.
 *
 */
class BoardroomBooker{
	private static $instance;
	private $page;
	private $db;
	
	private function __construct(){}
	
	public static function getInstance(){
		if(!self::$instance){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function init(){
		if(!file_exists('booker.conf')){
			$this->page = 'setupDatabase';
			return;
		}
		$config = parse_ini_file('booker.conf', 1);
		if(!$config){throw new Exception('Can\'t read configuration file');}
		/*Check options*/
		if(	!$config['database']['db_name'] ||
			!$config['database']['db_host'] ||
			!$config['database']['db_user'] ||
			//!$config['database']['db_password'] ||
			!$config['database']['db_prefix']
		){
			/*Did not found one or more options*/
			$this->page = 'setupDatabase';
			return;
		}
		/*Attempt to connect to database*/
		$dsn = 'mysql:dbname='.$config['database']['db_name'].';host='.$config['database']['db_host'];
		try {
			$this->db = new PDO($dsn, $config['database']['db_user'], $config['database']['db_password']);
		} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
		$res = $this->db->query('SELECT COUNT(*) as count FROM users')->fetch(PDO::FETCH_ASSOC);
		if($res['count'] == 0){
			$this->page = 'setupBooker';
			return;
		}
		/*Authorization*/
		if(!$_SESSION['user']){
			$this->page = 'signIn';
			return;
		}
		die('+');
		/*Target page from query_string*/
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
}