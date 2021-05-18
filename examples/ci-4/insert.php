<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));
$sql = "INSERT INTO members (name, surname, age) VALUES ('Jökull', 'Júlíusson', 30)";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//              ->insert(array('name' => 'Jökull', 'surname' => 'Júlíusson', 'age' => 30));

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db', 'single_command' => false));
$sql = "INSERT INTO members (name, surname, age) VALUES ('Jökull', 'Júlíusson', 30)";
echo $converter->convert($sql);
// prints
//           $db->table('members')
//            ->set(array('name' => 'Jökull', 'surname' => 'Júlíusson', 'age' => 30))
//            ->insert();

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));
$sql = "INSERT INTO members (name,surname, age) VALUES ('Jökull', 'Júlíusson', 30),  ('David', 'Antonsson', null), ('Daniel', 'Kristjansson', null), ('Rubin', 'Pollock', null), ('Þorleifur Gaukur', 'Davíðsson', null) ";
echo $converter->convert($sql);

// prints
//          $db->table('members')
//            ->insertBatch(array(
//                array('name' => 'Jökull', 'surname' => 'Júlíusson', 'age' => 30),
//                array('name' => 'David', 'surname' => 'Antonsson', 'age' => null),
//                array('name' => 'Daniel', 'surname' => 'Kristjansson', 'age' => null),
//                array('name' => 'Rubin', 'surname' => 'Pollock', 'age' => null),
//                array('name' => 'Þorleifur Gaukur', 'surname' => 'Davíðsson', 'age' => null)));

//==========================================================