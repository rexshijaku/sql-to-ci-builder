<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = "SELECT * FROM members ORDER BY name DESC, surname ASC";
echo $converter->convert($sql);
// prints
//          $this->db->select('*')
//               ->order_by('name DESC,surname ASC')
//               ->get('members');

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3, 'single_command' => false));
$sql = "SELECT * FROM members ORDER BY name DESC, surname ASC";
echo $converter->convert($sql);
// prints
//         $this->db->select('*')
//              ->order_by('name', 'DESC')
//              ->order_by('surname', 'ASC')
//              ->get('members');

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = "SELECT * FROM members ORDER BY RAND(42)";
echo $converter->convert($sql);

// prints
//          $this->db->select('*')
//               ->order_by('RANDOM (42)')
//               ->get('members');

//==========================================================