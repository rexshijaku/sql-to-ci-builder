<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = "SELECT DISTINCT * FROM members";
echo $converter->convert($sql);
// prints
//          $this->db->distinct()
//               ->get('members');

//==========================================================