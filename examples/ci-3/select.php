<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = 'SELECT * FROM members';
echo $converter->convert($sql);
// prints
//          $this->db->get('members');

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = 'SELECT * FROM members LIMIT 20, 10';
echo $converter->convert($sql);
// prints
//      $this->db->get('members',10,20);

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = 'SELECT name, surname, age FROM members LIMIT 20, 10';
echo $converter->convert($sql);
// prints
//     $this->db->select('name,surname,age')
//            ->get('members',10,20);

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3, 'use_from' => true));
$sql = 'SELECT name, surname, age FROM members LIMIT 20, 10';
echo $converter->convert($sql);;

// prints
//          $this->db->select('name,surname,age')
//                ->from('members')
//                ->limit(10, 20)
//                ->get();


//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = 'SELECT * FROM members WHERE id=1 LIMIT 20, 10';
echo $converter->convert($sql);

// prints
//        $this->db->get_where('members',array('id' => 1),10,20);

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 3));
$sql = 'SELECT name, surname, surname  FROM members';
echo $converter->convert($sql);
// prints
//          $this->db->select('name,surname,surname')
//               ->get('members');

//==========================================================