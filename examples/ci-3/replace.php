<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = "REPLACE INTO members (name, surname, age) VALUES ('Jökull', 'Júlíusson', 30)";
echo $converter->convert($sql);
//         $this->db->replace('members',
//            array('name' => 'Jökull', 'surname' => 'Júlíusson', 'age' => 30));

//==========================================================