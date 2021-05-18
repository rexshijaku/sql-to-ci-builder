<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));

//==========================================================

$sql = "SELECT * FROM members LIMIT 10,5";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//             ->get(5,10);

//==========================================================

$sql = "SELECT * FROM members LIMIT 10";
echo $converter->convert($sql);

// prints
//          $db->table('members')
//             ->get(10);

//==========================================================