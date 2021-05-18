<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 3));

//==========================================================

$sql = "DELETE FROM members";
echo $converter->convert($sql);
// prints
//         $this->db->empty_table('members');

//==========================================================

$sql = "DELETE FROM members WHERE age>10";
echo $converter->convert($sql);
// prints
//         $this->db->delete('members', array('age >' => 10));

//==========================================================

$sql = "DELETE FROM members WHERE age>10 OR salary > 2000";
echo $converter->convert($sql);
// prints
//         $this->db->where('age >', 10)
//                  ->or_where('salary >', 2000)
//                  ->delete('members');

//==========================================================