<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));
$sql = "SELECT * FROM members WHERE name LIKE '%j%' AND  surname LIKE 'j%' OR title NOT LIKE '%gui'";
echo $converter->convert($sql);
// prints
//        $db->table('members')
//            ->like('name', 'j', 'both')
//            ->like('surname', 'j', 'after')
//            ->orNotLike('title', 'gui', 'before')
//            ->get();

//==========================================================