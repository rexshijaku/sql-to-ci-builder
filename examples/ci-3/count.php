<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = "SELECT COUNT(*) FROM members";
echo $converter->convert($sql);
//        prints
//        $this->db->count_all('members');

//==========================================================

$sql = "SELECT COUNT(*) FROM members WHERE age > 15";
echo $converter->convert($sql);
//        prints
//          $this->db->select('COUNT(*)')
//                   ->where(array('age >' => 15))
//                   ->get('members');;

//==========================================================