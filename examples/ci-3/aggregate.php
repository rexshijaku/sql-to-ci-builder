<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 3));

//==========================================================

$sql = 'SELECT MAX(age) as age FROM members';
echo $converter->convert($sql);
// prints
//          $this->db->select_max('age', 'age')
//              ->get('members');

//==========================================================

$sql = 'SELECT MAX(age) FROM members';
echo $converter->convert($sql);
// prints
//          $this->db->select_max('age')
//              ->get('members');


//==========================================================

$sql = 'SELECT MIN(age) as age FROM members';
echo $converter->convert($sql);
// prints
//         $this->db->select_min('age', 'age')
//            ->get('members');


//==========================================================

$sql = 'SELECT AVG(age) as avg_member_age FROM members';
echo $converter->convert($sql);
// prints
//          $this->db->select_avg('age', 'avg_member_age')
//            ->get('members');

//==========================================================

$sql = 'SELECT SUM(age) as sum_age FROM members';
echo $converter->convert($sql);
// prints
//         $this->db->select_sum('age', 'sum_age')
//            ->get('members');

//==========================================================