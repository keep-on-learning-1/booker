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
class Configurer{

    public function setupDatabase($data){
        if(!$_COOKIE['setup_database_csrf'] || !$data['token'] || $data['token'] != $_COOKIE['setup_database_csrf'] ){
            header("HTTP/1.1 404 Not Found");
            header("HTTP/1.0 404 Not Found");
            header("Status: 404 Not Found");
            die;
        }
        if( !$data['host']          ||
            !$data['db_user']       ||
            !$data['db_name'])
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
            'db_name = '.$data['db_name'],
            'db_host = '.$data['host'],
            'db_user = '.$data['db_user'],
            'db_password = '.$data['db_password'],
        );
        $result = file_put_contents('booker.conf', implode("\r\n", $file)."\r\n");
        if(!$result){ throw new Exception('Can\'t write configuration file');}

        /*Create tables in database*/
        $dsn = 'mysql:dbname='.$data['db_name'].';host='.$data['host'];
        try {
            $dbh = new PDO($dsn, $data['db_user'], $data['db_password']);
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

    public static function setupBooker($data){
        if(!$_COOKIE['setup_booker_csrf'] || !$data['token'] || $data['token'] != $_COOKIE['setup_booker_csrf'] ){
            header("HTTP/1.1 404 Not Found");
            header("HTTP/1.0 404 Not Found");
            header("Status: 404 Not Found");
            die;
        }
        if(!is_numeric($data['number']) || $data['number']>10){
            die('Wrong number of the Boardrooms');
        }
        if(!file_exists('booker.conf')){die('Configuration file does not exist');}
        $file = array(
            '[booker]',
            'time_format = '.$data['time_format'] .' ; 12h or 24h',
            'first_day = '.$data['first_day']. ' ; sunday or monday',
            'number_of_bookers = '.$data['number']
        );
        file_put_contents('booker.conf', join("\r\n",$file)."\r\n",FILE_APPEND);

        setcookie('setup_booker_csrf', '', time() - 3600);
        header('Location: http://'.$_SERVER['HTTP_HOST']);
    }
}