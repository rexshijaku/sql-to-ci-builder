<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));

//==========================================================

$sql = "SELECT age,salary,count(*) FROM members GROUP BY age, salary";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//            ->select('age,salary,count(*)')
//            ->groupBy(array('age', 'salary'))
//            ->get();

//==========================================================

$sql = "SELECT age, some_function(),count(*) FROM members GROUP BY age, some_function()";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//             ->select('age,some_function(),count(*)')
//             ->groupBy(array('age', 'some_function()'))
//             ->get();