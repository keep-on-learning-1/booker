<?php
	$config = parse_ini_file('../booker.conf', 1);
	$dsn = 'mysql:dbname='.$config['database']['db_name'].';host='.$config['database']['db_host'];
	try {
        $db = new PDO($dsn, $config['database']['db_user'], $config['database']['db_password']);
    } catch (PDOException $e) {
        echo 'Connection failed: '.$e->getMessage(), 'msg-error';
    }
    //--------------------------------------
    $cm = date('m');
    $cy = date('Y');

    /*Единичные события*/
	$values = array(
        [
            (new DateTime("1-$cm-$cy 7:00"))->getTimestamp(),
            (new DateTime("1-$cm-$cy 8:00"))->getTimestamp()
        ],
        [
            (new DateTime("1-$cm-$cy 9:00"))->getTimestamp(),
            (new DateTime("1-$cm-$cy 10:00"))->getTimestamp()
        ],
        [
            (new DateTime("1-$cm-$cy 12:00"))->getTimestamp(),
            (new DateTime("1-$cm-$cy 13:00"))->getTimestamp()
        ],
        [
            (new DateTime("2-$cm-$cy 7:00"))->getTimestamp(),
            (new DateTime("2-$cm-$cy 9:00"))->getTimestamp()
        ],
        [
            (new DateTime("3-$cm-$cy 11:00"))->getTimestamp(),
            (new DateTime("3-$cm-$cy 12:00"))->getTimestamp()
        ],
        [
            (new DateTime("4-$cm-$cy 9:00"))->getTimestamp(),
            (new DateTime("4-$cm-$cy 10:00"))->getTimestamp()
        ],
        [
            (new DateTime("5-$cm-$cy 11:00"))->getTimestamp(),
            (new DateTime("5-$cm-$cy 12:00"))->getTimestamp()
        ],
        [
            (new DateTime("6-$cm-$cy 12:30"))->getTimestamp(),
            (new DateTime("6-$cm-$cy 13:45"))->getTimestamp()
        ],
        [
            (new DateTime("7-$cm-$cy 12:00"))->getTimestamp(),
            (new DateTime("7-$cm-$cy 12:30"))->getTimestamp()
        ],
        [
            (new DateTime("8-$cm-$cy 14:00"))->getTimestamp(),
            (new DateTime("8-$cm-$cy 14:30"))->getTimestamp()
        ],
        [
            (new DateTime("10-$cm-$cy 11:00"))->getTimestamp(),
            (new DateTime("10-$cm-$cy 12:00"))->getTimestamp()
        ],
        [
            (new DateTime("11-$cm-$cy 9:00"))->getTimestamp(),
            (new DateTime("11-$cm-$cy 10:00"))->getTimestamp()
        ],
        [
            (new DateTime("13-$cm-$cy 7:00"))->getTimestamp(),
            (new DateTime("13-$cm-$cy 8:00"))->getTimestamp()
        ],
        [
            (new DateTime("14-$cm-$cy 7:00"))->getTimestamp(),
            (new DateTime("14-$cm-$cy 8:00"))->getTimestamp()
        ],
        [
            (new DateTime("16-$cm-$cy 7:00"))->getTimestamp(),
            (new DateTime("16-$cm-$cy 9:00"))->getTimestamp()
        ],
        [
            (new DateTime("17-$cm-$cy 9:00"))->getTimestamp(),
            (new DateTime("17-$cm-$cy 10:00"))->getTimestamp()
        ],
        [
            (new DateTime("19-$cm-$cy 13:00"))->getTimestamp(),
            (new DateTime("19-$cm-$cy 15:00"))->getTimestamp()
        ],
        [
            (new DateTime("20-$cm-$cy 12:00"))->getTimestamp(),
            (new DateTime("20-$cm-$cy 13:00"))->getTimestamp()
        ],
        [
            (new DateTime("20-$cm-$cy 14:00"))->getTimestamp(),
            (new DateTime("20-$cm-$cy 14:30"))->getTimestamp()
        ],
        [
            (new DateTime("20-$cm-$cy 15:00"))->getTimestamp(),
            (new DateTime("20-$cm-$cy 16:00"))->getTimestamp()
        ],
        [
            (new DateTime("23-$cm-$cy 17:00"))->getTimestamp(),
            (new DateTime("23-$cm-$cy 18:00"))->getTimestamp()
        ],
        [
            (new DateTime("25-$cm-$cy 8:00"))->getTimestamp(),
            (new DateTime("25-$cm-$cy 9:00"))->getTimestamp()
        ],
    );

    foreach($values as $k=>$v){
        $db->exec('INSERT INTO events(recurring, employee_id, specifics)
                   VALUES (0, '.rand(1,20).', \'test\')');
        $id = $db->lastInsertId();
        $db->exec("INSERT INTO times(start_time, end_time, event_id)
                  VALUES (FROM_UNIXTIME({$v[0]}), FROM_UNIXTIME({$v[1]}), {$id})");
    }

    /*Повторяющиеся события*/
    $values = [
        new DatePeriod(new DateTime("12-$cm-$cy 8:00"), new DateInterval('P1M'), 2),
        new DatePeriod(new DateTime("18-$cm-$cy 8:00"), new DateInterval('P1M'), 2),
        new DatePeriod(new DateTime("21-$cm-$cy 8:00"), new DateInterval('P1W'), 4),
        new DatePeriod(new DateTime("26-$cm-$cy 8:00"), new DateInterval('P1W'), 4),
    ];

    foreach($values as $range){
        $db->exec('INSERT INTO events(recurring, employee_id, specifics)
                   VALUES (1, '.rand(1,20).', \'test\')');
        $id = $db->lastInsertId();
        foreach($range as $date){
            $start = $date->getTimestamp();
            $end = $date->getTimestamp() + 60*60;
            $db->exec("INSERT INTO times(start_time, end_time, event_id)
                       VALUES (FROM_UNIXTIME({$start}),FROM_UNIXTIME({$end}),{$id})");
        }

    }
?>