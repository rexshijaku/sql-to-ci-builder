<?php

namespace RexShijaku\SQLToCIBuilder\Test\v4;

use PHPUnit\Framework\TestCase;

class AbstractCases extends TestCase
{
    protected $options = array('civ' => 4, 'db_instance' => '$db');

    public function getExpectedValue($type, $fn)
    {
        $file_path = dirname(__FILE__);
        $file_path .= "\\..\\..\\expected\\v4\\";
        $file_path .= $type;
        $file_path .= "\\";
        $file_path .= $fn . ".txt";
        return file_get_contents($file_path);
    }
}