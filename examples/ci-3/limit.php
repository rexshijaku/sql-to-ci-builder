<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';


//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = "SELECT * FROM members LIMIT 10,5";
echo $converter->convert($sql);
// prints
//          $this->db->get('members',5,10);

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3, 'use_from' => true));
$sql = "SELECT * FROM members LIMIT 10";
echo $converter->convert($sql);

// prints
//          $this->db->select('*')
//              ->from('members')
//              ->limit(10)
//              ->get();

//==========================================================