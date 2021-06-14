<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 3));

//==========================================================

$sql = "SELECT * FROM members WHERE age = 25";
echo $converter->convert($sql);
// prints
//       $this->db->get_where('members', array('age' => 25));

//==========================================================

$sql = "SELECT * FROM members WHERE age +1  = 25";
echo $converter->convert($sql);
// prints
//      $this->db->where('age+1', 25, FALSE)
//          ->get('members');

//==========================================================


$sql = "SELECT * FROM members WHERE age+0 = age";
echo $converter->convert($sql);
// prints
//          $this->db->where('age+0', 'age', FALSE)
//            ->get('members');

//==========================================================


$sql = "SELECT * FROM members WHERE age+0  = age or name like '%j%' or id + 2 > id +5";
echo $converter->convert($sql);
// prints
//          $this->db->where('age+0', 'age', FALSE)
//               ->or_like('name', 'j', 'both')
//               ->or_where('id+2 >', 'id+5', FALSE)
//               ->get('members');

//==========================================================

$sql = "SELECT * FROM members WHERE age BETWEEN 20 AND 20";
echo $converter->convert($sql);
// prints
//            $this->db->where('age>=', 20)
//                 ->where('age<=', 20)
//                 ->get('members');


//==========================================================

$sql = "SELECT * FROM members WHERE age BETWEEN age+20 AND 20";
echo $converter->convert($sql);
// prints
//          $this->db->where('age>=', 'age+20', FALSE)
//                   ->where('age<=', 20)
//                   ->get('members');

//==========================================================

$sql = "SELECT * FROM members WHERE age NOT BETWEEN 20 AND 20";
echo $converter->convert($sql);
// prints
//          $this->db->where("!(age BETWEEN 20 AND 20)")->get('members');

//==========================================================

$sql = "SELECT * FROM members WHERE age BETWEEN 20 AND 40 OR age NOT BETWEEN 40 AND 45";
echo $converter->convert($sql);
// prints
//          $this->db->where('age >=', 20)
//                   ->where('age <=', 40)
//                   ->or_where("!(age BETWEEN 40 AND 45)")
//                   ->get('members');

//==========================================================

$sql = "SELECT * FROM members WHERE age IN(20,30)";
echo $converter->convert($sql);
// prints
//          $this->db->where_in('age', array(20, 30))
//                   ->get('members');

//==========================================================

$sql = "SELECT * FROM members WHERE age NOT IN(20,30)";
echo $converter->convert($sql);
// prints
//         $this->db->where_not_in('age',array(20,30))->get('members');

//==========================================================

$sql = "SELECT * FROM members WHERE age IS NULL";
echo $converter->convert($sql);
// prints
//          $this->db->get_where('members', array('age' => null));

$sql = "SELECT * FROM members WHERE age IS NOT NULL";
echo $converter->convert($sql);
// prints
//          $this->db->get_where('members', array('age !=' => null));


//==========================================================

$sql = "SELECT * FROM members WHERE age > 30 OR (name LIKE 'J%' OR (surname='P' AND name IS NOT NULL)) AND AGE !=30";
echo $converter->convert($sql);

//          $this->db->where('age >',30)
//              ->or_group_start()
//                  ->like('name','J','after')
//                  ->or_group_start()
//                       ->where('surname','P')
//                       ->where('name !=',null)
//                  ->group_end()
//              ->group_end()
//              ->where('AGE !=',30)
//              ->get('members');

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3, 'group' => false));
$sql = "SELECT * FROM members WHERE age = 25 AND name = 'David'";
echo $converter->convert($sql);
// prints
//        $this->db->where('age', 25)
//            ->where('name', 'David')
//            ->get('members');