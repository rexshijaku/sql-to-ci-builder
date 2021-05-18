<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));

//==========================================================

$sql = 'SELECT MAX(age) as age FROM members';
echo $converter->convert($sql);
// prints
//            $db->table('members')
//            ->selectMax('age', 'age')
//            ->get();

//==========================================================

$sql = 'SELECT MAX(age) FROM members';
echo $converter->convert($sql);
// prints
//         $db->table('members')
//            ->selectMax('age')
//            ->get();


//==========================================================

$sql = 'SELECT MIN(age) as age FROM members';
echo $converter->convert($sql);
// prints
//         $db->table('members')
//            ->selectMin('age', 'age')
//            ->get();


//==========================================================

$sql = 'SELECT AVG(age) as avg_member_age FROM members';
echo $converter->convert($sql);
// prints
//         $db->table('members')
//            ->selectAvg('age', 'avg_member_age')
//            ->get();

//==========================================================

$sql = 'SELECT SUM(age) as sum_age FROM members';
echo $converter->convert($sql);
// prints
//        $db->table('members')
//            ->selectSum('age', 'sum_age')
//            ->get();

//==========================================================