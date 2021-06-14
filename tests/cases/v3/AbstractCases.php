<?php

namespace RexShijaku\SQLToCIBuilder\Test\v3;

use PHPUnit\Framework\TestCase;

class AbstractCases extends TestCase
{
    protected $options = array('db_instance' => '$this->db');

    public function getExpectedValue($type, $fn)
    {
        $file_path = dirname(__FILE__);
        $file_path .= "\\..\\..\\expected\\v3\\";
        $file_path .= $type;
        $file_path .= "\\";
        $file_path .= $fn . ".txt";
        return file_get_contents($file_path);
    }
}