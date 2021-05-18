<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

$converter = new SQLToCIBuilder(array('civ' => 4, 'db_instance' => '$db'));

//==========================================================

$sql = "DELETE FROM members";
echo $converter->convert($sql);
// prints
//          $db->emptyTable('members');

//==========================================================

$sql = "DELETE FROM members WHERE age>10";
echo $converter->convert($sql);
//         $db->table('members')
//            ->delete(array('age >' => 10));

//==========================================================

$sql = "DELETE FROM members WHERE age>10 OR salary > 2000";
echo $converter->convert($sql);

//         $db->table('members')
//            ->where('age >', 10)
//            ->orWhere('salary >', 2000)
//            ->delete();

//==========================================================