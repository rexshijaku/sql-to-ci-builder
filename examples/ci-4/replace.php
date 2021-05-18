<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));
$sql = "REPLACE INTO members (name, surname, age) VALUES ('Jökull', 'Júlíusson', 30)";
echo $converter->convert($sql);
//         $db->table('members')
//            ->replace(array('name' => 'Jökull', 'surname' => 'Júlíusson', 'age' => 30));

//==========================================================