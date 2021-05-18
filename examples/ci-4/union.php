<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));

//==========================================================

$sql = "SELECT * FROM table1 UNION SELECT * FROM table2";
echo $converter->convert($sql);
// prints
//      $db->query(
//            $db->table('table1')
//                ->select('*')
//                ->getCompiledSelect() . ' UNION ' .
//            $db->table('table2')
//                ->select('*')
//                ->getCompiledSelect());

//==========================================================
$sql = "SELECT * FROM table1 UNION ALL SELECT * FROM table2";
echo $converter->convert($sql);
// prints
//      $db->query(
//            $db->table('table1')
//                ->select('*')
//                ->getCompiledSelect() . ' UNION ALL ' .
//            $db->table('table2')
//                ->select('*')
//                ->getCompiledSelect());