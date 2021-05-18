<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));
$sql = 'SELECT * FROM members';
echo $converter->convert($sql);
// prints
//          $db->table('members')
//             ->get();

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));
$sql = 'SELECT * FROM members LIMIT 20, 10';
echo $converter->convert($sql);
// prints
//          $db->table('members')
//             ->get(10,20);

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));
$sql = 'SELECT name, surname, age FROM members LIMIT 20, 10';
echo $converter->convert($sql);
// prints
//          $db->table('members')
//             ->select('name,surname,age')
//             ->get(10, 20);

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db', 'use_from' => true));
$sql = 'SELECT name, surname, age FROM members LIMIT 20, 10';
echo $converter->convert($sql);;
// prints
//          $db->table('members')
//            ->select('name,surname,age')
//            ->limit(10, 20)
//            ->get();


//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));
$sql = 'SELECT * FROM members WHERE id=1 LIMIT 20, 10';
echo $converter->convert($sql);
// prints
//        $db->table('members')->getWhere(array('id' => 1),10,20);

//==========================================================

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));
$sql = 'SELECT name, surname, surname  FROM members';
echo $converter->convert($sql);
// prints
//          $db->table('members')
//             ->select('name,surname,surname')
//             ->get();

//==========================================================