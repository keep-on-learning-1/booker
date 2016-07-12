<?php
if(!$_COOKIE['setup_database_csrf'] || !$_POST['token'] || $_POST['token'] != $_COOKIE['setup_database_csrf'] ){
    header("HTTP/1.1 404 Not Found");
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
}
if( !$_POST['host']          ||
    !$_POST['db_user']       ||
    //!$_POST['db_password'] ||
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

$query =    "CREATE TABLE `times` (
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

$query =    "CREATE TABLE `events` (
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
