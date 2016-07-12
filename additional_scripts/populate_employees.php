<?php
	$config = parse_ini_file('../booker.conf', 1);
	$dsn = 'mysql:dbname='.$config['database']['db_name'].';host='.$config['database']['db_host'];
	try {
		$db = new PDO($dsn, $config['database']['db_user'], $config['database']['db_password']);
	} catch (PDOException $e) {
		echo 'Connection failed: '.$e->getMessage(), 'msg-error';
	}
	
	$values = array();
	for($i=0; $i<20; $i++){
		$values[] = "('employee $i', 'mail-$i@domain.any')";
	}
	echo $query = "INSERT INTO employees (name, email) VALUES ".implode(', ',$values), '<br>';
	$res = $db->exec($query);
	if(!$res){
		echo $db->errorInfo()[2];
	}else{
		echo "<hr>Values inserted";
	}

?>