`<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));

//==========================================================

$sql = "SELECT * FROM members WHERE age = 25";
echo $converter->convert($sql);
// prints
//       $db->table('members')
//            ->getWhere(array('age' => 25));

//==========================================================

$sql = "SELECT * FROM members WHERE age +1  = 25";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//              ->where('age+1', 25, FALSE)
//              ->get();

//==========================================================


$sql = "SELECT * FROM members WHERE age+0 = age";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//             ->where('age+0', 'age', FALSE)
//             ->get();

//==========================================================


$sql = "SELECT * FROM members WHERE age+0  = age or name like '%j%' or id + 2 > id +5";
echo $converter->convert($sql);
// prints
//           $db->table('members')
//              ->where('age+0', 'age', FALSE)
//              ->orLike('name', 'j', 'both')
//              ->orWhere('id+2 >', 'id+5', FALSE)
//              ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age BETWEEN 20 AND 30";
echo $converter->convert($sql);
// prints
//            $db->table('members')
//            ->where('age >=', 20)
//            ->where('age <=', 30)
//            ->get();


//==========================================================

$sql = "SELECT * FROM members WHERE age BETWEEN age+20 AND 50";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//             ->where('age>=', 'age+20', FALSE)
//             ->where('age<=', 50)
//             ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age NOT BETWEEN 20 AND 20";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//             ->where("!(age BETWEEN 20 AND 20)")->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age BETWEEN 20 AND 40 OR age NOT BETWEEN 40 AND 45";
echo $converter->convert($sql);
// prints
//         $db->table('members')
//            ->where('age >=', 20)
//            ->where('age <=', 40)
//                    ->orWhere("!(age BETWEEN 40 AND 45)")
//            ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age IN(20,30)";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//             ->whereIn('age', array(20, 30))
//             ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age NOT IN(20,30)";
echo $converter->convert($sql);
// prints
//           $db->table('members')
//            ->whereNotIn('age', array(20, 30))
//            ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age IS NULL";
echo $converter->convert($sql);
// prints
//         $db->table('members')
//            ->getWhere(array('age' => null));

$sql = "SELECT * FROM members WHERE age IS NOT NULL";
echo $converter->convert($sql);
// prints
//        $db->table('members')
//            ->getWhere(array('age !=' => null));


//==========================================================

$sql = "SELECT * FROM members WHERE age > 30 OR (name LIKE 'J%' OR (surname='P' AND name IS NOT NULL)) AND AGE !=30";
echo $converter->convert($sql);
// prints
//           $db->table('members')
//            ->where('age >',30)
//            ->orGroupStart()
//                ->like('name','J','after')
//                ->orGroupStart()
//                    ->where('surname','P')
//                    ->where('name !=',null)
//                ->groupEnd()
//            ->groupEnd()
//            ->where('AGE !=',30)
//            ->get();

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db', 'group' => false));
$sql = "SELECT * FROM members WHERE age = 25 AND name = 'David'";
echo $converter->convert($sql);
// prints
//      $db->table('members')
//            ->where('age', 25)
//            ->where('name', 'David')
//            ->get();`