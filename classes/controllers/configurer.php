<?php
/*
 * Class for initial configuration of application.
 * Creates required tables in database and a first application user in 'users' table.
 * Creates configuration file 'booker.conf'
 *
 * Methods:
 *  - setupDatabase
 *  - setupBooker
 */
class Configurer extends AbstractController{

    public function setupDatabase(){
        if(!$_COOKIE['setup_database_csrf'] || !$_POST['token'] || $_POST['token'] != $_COOKIE['setup_database_csrf'] ){
            header("HTTP/1.1 404 Not Found");
            header("HTTP/1.0 404 Not Found");
            header("Status: 404 Not Found");
        }
        if( !$_POST['host']          ||
            !$_POST['db_user']       ||
            !$_POST['db_name'])
        {
            die('Required data are missed');
        }
        /*Check input data*/
        if(!preg_match('/^[a-zA-Z0-9]*$/', $_POST['db_user'])){
            die('wrong login');
        }
        if(!preg_match('/^[a-zA-Z0-9_]*$/', $_POST['prefix'])){
            die('wrong database prefix');
        }

        /*Creating configuration file*/
        $file = array(
            '[database]',
            'db_name = '.$_POST['db_name'],
            'db_host = '.$_POST['host'],
            'db_user = '.$_POST['db_user'],
            'db_password = '.$_POST['db_password'],
        );
        $result = file_put_contents('booker.conf', implode("\r\n", $file)."\r\n");
        if(!$result){ throw new Exception('Can\'t write configuration file');}

        /*Create tables in database*/
        $dsn = 'mysql:dbname='.$_POST['db_name'].';host='.$_POST['host'];
        try {
            $dbh = new PDO($dsn, $_POST['db_user'], $_POST['db_password']);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        /* --users table*/
        $query =    "CREATE TABLE IF NOT EXISTS `users` (
                        `id` INT NOT NULL AUTO_INCREMENT,
                        `login` VARCHAR(50) NOT NULL,
                        `password` VARCHAR(50) NOT NULL,
                        PRIMARY KEY (`id`)
                    )
                    COLLATE='utf8_general_ci'
                    ENGINE=InnoDB;
                    ";
        $dbh ->exec($query);

        /* --employees table*/
        $query =    "CREATE TABLE IF NOT EXISTS `employees` (
                        `id` INT NOT NULL AUTO_INCREMENT,
                        `name` VARCHAR(50) NOT NULL,
                        `email` VARCHAR(50) NOT NULL,
                        PRIMARY KEY (`id`)
                    )
                    COLLATE='utf8_general_ci'
                    ENGINE=InnoDB;
                    ";
        $dbh ->exec($query);

        $query =    "CREATE TABLE IF NOT EXISTS  `times` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                        `start_time` TIMESTAMP NULL DEFAULT NULL,
                        `end_time` TIMESTAMP NULL DEFAULT NULL,
                        `event_id` INT(11) NOT NULL DEFAULT '0',
                        PRIMARY KEY (`id`),
                        INDEX `event_id` (`event_id`)
                    )
                    COLLATE='utf8_general_ci'
                    ENGINE=InnoDB
                    ";
        $dbh ->exec($query);

        $query =    "CREATE TABLE IF NOT EXISTS `events` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                        `recurring` TINYINT(4) NOT NULL DEFAULT '0',
                        `employee_id` INT(11) NOT NULL DEFAULT '0',
                        `specifics` VARCHAR(50) NULL DEFAULT '0',
                        `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`),
                        INDEX `employee_id` (`employee_id`)
                    )
                    COLLATE='utf8_general_ci'
                    ENGINE=InnoDB";
        $dbh ->exec($query);

        setcookie('setup_database_csrf', '', time() - 3600);
        header('Location: http://'.$_SERVER['HTTP_HOST']);
    }

    public function setupBooker(){
        if(!$_COOKIE['setup_user_csrf'] || !$_POST['token'] || $_POST['token'] != $_COOKIE['setup_user_csrf'] ){
            header("HTTP/1.1 404 Not Found");
            header("HTTP/1.0 404 Not Found");
            header("Status: 404 Not Found");
        }
        if( !$_POST['login']            ||
            !$_POST['password']         ||
            !$_POST['confirm_password'] ||
            !$_POST['first_day']        ||
            !$_POST['number']           ||
            !$_POST['time_format'] )
        {
            die('Required data are missed');
        }
        if($_POST['password'] != $_POST['confirm_password']){
            die('Password doesn\'t match confirmation');
        }
        if(!is_numeric($_POST['number']) || $_POST['number']>10){
            die('Wrong number of the Boardrooms');
        }

        if(!preg_match('/^[a-zA-Z\d\-_@]*$/', $_POST['login'])){die('Login contains unallowed characters.');}
        if(strlen($_POST['login'])<4){die('Login must contain at least 4 symbols.');}
        if(strlen($_POST['login'])>50){die('Login can\'t be longer than 50 symbols.');}

        if(!file_exists('booker.conf')){die('Configuration file does not exist');}
        $config = parse_ini_file('booker.conf',1);
        $dsn = 'mysql:dbname='.$config['database']['db_name'].';host='.$config['database']['db_host'];
        try {
            $dbh = new PDO($dsn, $config['database']['db_user'], $config['database']['db_password']);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        $query = "INSERT INTO `users` (`login`, `password`) VALUES(:login, :password)";
        $sth = $dbh->prepare($query);
        $sth->execute(array(':login'=>$_POST['login'], ':password'=>md5($_POST['password'])));
        $file = array(
            '[booker]',
            'time_format = '.$_POST['time_format'],
            'first_day = '.$_POST['first_day'],
            'number_of_bookers = '.$_POST['number']
        );
        file_put_contents('booker.conf', join("\r\n",$file)."\r\n",FILE_APPEND);
        setcookie('setup_user_csrf', '', time() - 3600);
        header('Location: http://'.$_SERVER['HTTP_HOST']);
    }
}