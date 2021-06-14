<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';


//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));
$sql = " SELECT * FROM members WHERE ( age = 25 OR ( salary = 2000 AND gender = 'm' ) ) AND id > 100800";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//            ->groupStart()
//            ->where('age', 25)
//                ->orGroupStart()
//                    ->where('salary', 2000)
//                    ->where('gender', 'm')
//                ->groupEnd()
//            ->groupEnd()
//            ->where('id >', 100800)
//            ->get();

//==========================================================


$sql = " SELECT * FROM members WHERE NOT ( age = 25 or age > 80) OR NOT (age < 10) ";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//            ->notGroupStart()
//                ->where('age', 25)
//                ->orWhere('age >', 80)
//            ->groupEnd()
//            ->orNotGroupStart()
//                ->where('age <', 10)
//            ->groupEnd()
//            ->get();

//==========================================================

$sql = " SELECT * FROM members WHERE NOT ( age = 25 or age > 80) HAVING age > 10 OR NOT (age < 10) ";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//              ->select('*')
//              ->notGroupStart()
//                  ->where('age',25)
//                  ->orWhere('age >',80)
//              ->groupEnd()
//              ->having('age >' ,10)
//              ->orNotHavingGroupStart()
//                  ->having('age <' ,10)
//              ->havingGroupEnd()
//              ->get();