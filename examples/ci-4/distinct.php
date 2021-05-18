<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));
$sql = "SELECT DISTINCT * FROM members";
echo $converter->convert($sql);
// prints
//        $db->table('members')
//           ->distinct()
//           ->get();

//==========================================================