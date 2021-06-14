<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = "INSERT INTO members (name, surname, age) VALUES ('Jökull', 'Júlíusson', 30)";
echo $converter->convert($sql);
// prints
//      $this->db->insert('members', array('name' => 'Jökull', 'surname' => 'Júlíusson', 'age' => 30));

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3, 'single_command' => false));
$sql = "INSERT INTO members (name, surname, age) VALUES ('Jökull', 'Júlíusson', 30)";
echo $converter->convert($sql);
// prints
//          $this->db->set(array('name' => 'Jökull', 'surname' => 'Júlíusson', 'age' => 30))
//                    ->insert('members')

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = "INSERT INTO members (name,surname, age) VALUES ('Jökull', 'Júlíusson', 30),  ('David', 'Antonsson', null), ('Daniel', 'Kristjansson', null), ('Rubin', 'Pollock', null), ('Þorleifur Gaukur', 'Davíðsson', null) ";
echo $converter->convert($sql);

// prints
//          $this->db->insert_batch('members',
//              array(
//                  array('name' => 'Jökull', 'surname' => 'Júlíusson', 'age' => 30),
//                  array('name' => 'David', 'surname' => 'Antonsson', 'age' => null),
//                  array('name' => 'Daniel', 'surname' => 'Kristjansson', 'age' => null),
//                  array('name' => 'Rubin', 'surname' => 'Pollock', 'age' => null),
//                  array('name' => 'Þorleifur Gaukur', 'surname' => 'Davíðsson', 'age' => null)
//              ));

//==========================================================