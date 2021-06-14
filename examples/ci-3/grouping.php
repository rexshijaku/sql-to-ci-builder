<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';


//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = "SELECT * FROM (members) WHERE ( age = 25 OR ( salary = 2000 AND gender = 'm' ) ) AND id > 100800";
echo $converter->convert($sql);
// prints
//          $this->db->select('*')
//            ->from('members')
//            ->group_start()
//                ->where('age',25)
//                ->or_group_start()
//                    ->where('salary',2000)
//                    ->where('gender','m')
//                ->group_end()
//            ->group_end()
//            ->where('id >',100800)
//            ->get();

//==========================================================


$sql = " SELECT * FROM members WHERE NOT ( age = 25 or age > 80) OR NOT (age < 10) ";
echo $converter->convert($sql);
//          $this->db->not_group_start()
//                  ->where('age', 25)
//                  ->or_where('age >', 80)
//               ->group_end()
//               ->or_not_group_start()
//                   ->where('age <', 10)
//               ->group_end()
//               ->get('members');