<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));

//==========================================================

$sql = "SELECT age,salary FROM members HAVING age > 25, salary < 3000";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//            ->select('age,salary')
//            ->having(array('age >' => 25, 'salary <' => 3000))
//            ->get();

//==========================================================

$sql = "SELECT age FROM members HAVING age+20 > 25";
echo $converter->convert($sql);

// prints
//         $db->table('members')
//            ->select('age')
//            ->having('age+20 >', 25, FALSE)
//            ->get();

//==========================================================

$sql = "SELECT age,salary FROM members HAVING age > 25 OR salary < 3000";
echo $converter->convert($sql);
// prints
//           $db->table('members')
//            ->select('age,salary')
//            ->having('age >', 25)
//            ->orHaving('salary <', 3000)
//            ->get();

//==========================================================

$sql = "SELECT * FROM members HAVING age+20 > 45 OR salary-200 > 500";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//            ->select('*')
//            ->having('age+20 >', 45, FALSE)
//            ->orHaving('salary-200 >', 500, FALSE)
//            ->get();

//==========================================================

$sql = "SELECT * FROM members HAVING age BETWEEN 25 AND 35";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//               ->select('*')
//               ->having('age >=', 25)
//               ->having('age <=', 35)
//               ->get();

//==========================================================

$sql = "SELECT * FROM members HAVING age NOT BETWEEN 25 AND 35";
echo $converter->convert($sql);
// prints
//            $db->table('members')
//            ->select('*')
//            ->having("!(age BETWEEN 25 AND 35)")
//            ->get();

//==========================================================

$sql = "SELECT name,age,salary,gender FROM members HAVING name like '%R' AND gender = 'm' or HAVING salary>1000 AND HAVING gender=0 AND age+2=25";
echo $converter->convert($sql);
// prints
//           $db->table('members')
//            ->select('name,age,salary,gender')
//            ->having(array('name like \'%R\'' => null, 'gender' => 'm'))
//            ->orHaving('salary >', 1000)
//            ->having('age+2', 25, FALSE)
//            ->having('gender', 0)
//            ->get();
//==========================================================