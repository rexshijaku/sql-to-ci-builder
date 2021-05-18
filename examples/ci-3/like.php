<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = "SELECT * FROM members WHERE name LIKE '%j%' AND  surname LIKE 'j%' OR title NOT LIKE '%gui'";
echo $converter->convert($sql);
// prints
//        $this->db->like('name', 'j', 'both')
//            ->like('surname', 'j', 'after')
//            ->or_not_like('title', 'gui', 'before')
//            ->get('members');

//==========================================================