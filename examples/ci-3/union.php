<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 3));

//==========================================================

$sql = "SELECT * FROM table1 UNION SELECT * FROM table2";
echo $converter->convert($sql);
// prints
//      $this->db->query($this->db->select('*')
//              ->from('table1')
//              ->get_compiled_select().' UNION '.$this->db->select('*')
//              ->from('table2')
//              ->get_compiled_select())
//              ->get();

//==========================================================

$sql = "SELECT * FROM table1 UNION ALL SELECT * FROM table2";
echo $converter->convert($sql);
// prints
//      $this->db->query($this->db->select('*')
//              ->from('table1')
//              ->get_compiled_select().' UNION ALL '.$this->db->select('*')
//              ->from('table2')
//              ->get_compiled_select())
//              ->get();