<?php
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