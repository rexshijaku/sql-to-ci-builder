<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));

//==========================================================

$sql = "SELECT age,salary,count(*) FROM members GROUP BY age, salary";
echo $converter->convert($sql);
// prints
//          $this->db->select('age,salary,count(*)')
//                ->group_by(array('age', 'salary'))
//                ->get('members');

//==========================================================

$sql = "SELECT age, some_function(),count(*) FROM members GROUP BY age, some_function()";
echo $converter->convert($sql);
// prints
//          $this->db->select('age,some_function(),count(*)')
//               ->group_by(array('age', 'some_function()'))
//               ->get('members');