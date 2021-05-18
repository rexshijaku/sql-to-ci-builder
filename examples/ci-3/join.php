<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 3));

//==========================================================

$sql = 'SELECT * FROM members JOIN details ON members.id = details.members_id';
echo $converter->convert($sql);
// prints
//          $this->db->select('*')
//            ->from('members')
//            ->join('details', 'members.id = details.members_id')
//            ->get();

//==========================================================

$sql = 'SELECT * FROM members LEFT JOIN details ON members.id = details.members_id';
echo $converter->convert($sql);
// prints
//          $this->db->select('*')
//            ->from('members')
//            ->join('details', 'members.id = details.members_id', 'LEFT')
//            ->get();

//==========================================================

$sql = 'SELECT * FROM members RIGHT JOIN details ON members.id = details.members_id';
echo $converter->convert($sql);
// prints
//            $this->db->select('*')
//              ->from('members')
//              ->join('details', 'members.id = details.members_id', 'RIGHT')
//              ->get();

//==========================================================