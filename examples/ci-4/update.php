<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));

//==========================================================

$sql = "UPDATE members SET age = 10 WHERE id = 2";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//             ->where(array('id' => 2))
//             ->set(array('age' => 10))
//             ->update();

//==========================================================

$sql = "UPDATE members SET age = age+10 WHERE id = 2";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//             ->where(array('id' => 2))
//             ->set('age', 'age+10', FALSE)
//             ->update();

//==========================================================