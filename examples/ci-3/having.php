<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 3));

//==========================================================

$sql = "SELECT age,salary FROM members HAVING age > 25, salary < 3000";
echo $converter->convert($sql);
// prints
//          $this->db->select('age,salary')
//            ->having(array('age >' => 25, 'salary <' => 3000))
//            ->get('members');

//==========================================================

$sql = "SELECT age FROM members HAVING age+20 > 45";
echo $converter->convert($sql);

// prints
//         $this->db->select('age')
//            ->having('age+20 >', 45, FALSE)
//            ->get('members');

//==========================================================

$sql = "SELECT age,salary FROM members HAVING age > 25 OR salary < 3000";
echo $converter->convert($sql);
// prints
//           $this->db->select('age,salary')
//            ->having('age >', 25)
//            ->or_having('salary <', 3000)
//            ->get('members');

//==========================================================

$sql = "SELECT * FROM members HAVING age+20 > 45 OR salary-200 > 500";
echo $converter->convert($sql);
// prints
//          $this->db->select('*')
//              ->having('age+20 >', 45, FALSE)
//              ->or_having('salary-200 >', 500, FALSE)
//              ->get('members');

//==========================================================

$sql = "SELECT * FROM members HAVING age BETWEEN 25 AND 35";
echo $converter->convert($sql);
// prints
//             $this->db->select('*')
//                  ->having('age >=', 25)
//                  ->having('age <=', 35)
//                  ->get('members');

//==========================================================

$sql = "SELECT * FROM members HAVING age NOT BETWEEN 25 AND 35";
echo $converter->convert($sql);
// prints
//            $this->db->select('*')
//                  ->having("!(age BETWEEN 25 AND 35)")
//                  ->get('members');

//==========================================================

$sql = "SELECT name,age,salary,gender FROM members HAVING name like '%R' AND gender = 'm' or salary>1000 AND gender=0 AND age+2=25";
echo $converter->convert($sql);
// prints
//           $this->db->select('name,age,salary,gender')
//              ->having(array('name like \'%R\'' =>null,'gender' =>'m'))
//              ->or_having('salary >' ,1000)
//              ->having('age+2' ,25,FALSE)
//              ->having('gender' ,0)
//              ->get('members');
//==========================================================