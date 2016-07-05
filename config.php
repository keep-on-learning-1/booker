<?php
if(!$_COOKIE['setup_database_csrf'] || !$_POST['token'] || $_POST['token'] != $_COOKIE['setup_database_csrf'] ){
    header("HTTP/1.1 404 Not Found");
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
}
if( !$_POST['host']          ||
    !$_POST['db_user']       ||
    !$_POST['db_name']       ||
    //!$_POST['db_password']   ||
    !$_POST['prefix'] )
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
    'db_prefix = '.$_POST['prefix']
);
$result = file_put_contents('booker.conf', implode("\r\n", $file)."\r\n");
if(!$result){ throw new Exception('Can\'t write configuration file');}

/*Create tables in database*/
$dsn = 'mysql:dbname='.$_POST['db_name'].';host='.$_POST['host'];
try {
    $dbh = new PDO($dsn, $_POST['db_user'], $_POST['db_password']);
} catch (PDOException $e) {
    echo 'Подключение не удалось: ' . $e->getMessage();
}
$query = "CREATE TABLE IF NOT EXISTS `users` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `login` VARCHAR(50) NOT NULL,
                `password` VARCHAR(50) NOT NULL,
                PRIMARY KEY (`id`)
            )
            COLLATE='utf8_general_ci'
            ENGINE=InnoDB;
            ";
$dbh ->exec($query);
setcookie('setup_database_csrf', '', time() - 3600);
header('Location: http://'.$_SERVER['HTTP_HOST']);
