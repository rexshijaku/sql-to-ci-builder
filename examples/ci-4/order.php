<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));
$sql = "SELECT * FROM members having k>1,p>3,l+2=3 ORDER BY name DESC, surname ASC";
echo $converter->convert($sql);
// prints
//          $db->table('members')
//             ->select('*')
//             ->orderBy('name DESC,surname ASC')
//             ->get();

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db', 'single_command' => false));
$sql = "SELECT * FROM members ORDER BY name DESC, surname ASC";
echo $converter->convert($sql);
// prints
//         $db->table('members')
//            ->select('*')
//            ->orderBy('name', 'DESC')
//            ->orderBy('surname', 'ASC')
//            ->get();

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));
$sql = "SELECT * FROM members ORDER BY RAND(42)";
echo $converter->convert($sql);

// prints
//          $db->table('members')
//            ->select('*')
//            ->orderBy('RANDOM (42)')
//            ->get();

//==========================================================