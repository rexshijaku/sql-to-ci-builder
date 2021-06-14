<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 3));

//==========================================================

$sql = "UPDATE members SET age = 10 WHERE id = 2";
echo $converter->convert($sql);
// prints
//          $this->db->update('members',array('age'=>10),array('id' => 2));

//==========================================================

$sql = "UPDATE members SET age = age+10 WHERE id = 2";
echo $converter->convert($sql);
// prints
//          $this->db->where(array('id' => 2))
//                ->set('age','age+10',FALSE)
//                ->update('members');

//==========================================================