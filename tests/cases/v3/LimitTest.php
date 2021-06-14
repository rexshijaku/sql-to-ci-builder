<?php

namespace RexShijaku\SQLToCIBuilder\Test\v3;

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

class LimitTest extends AbstractCases
{
    private $type = 'limit';

    public function testLimitAndOffset()
    {
        $sql = "SELECT * FROM members LIMIT 10,5";
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "limitOffset");
        $this->assertEquals($expected, $actual);
    }

    public function testLimitViaFrom()
    {
        $sql = "SELECT * FROM members LIMIT 10";
        $converter = new SQLToCIBuilder(array('civ' => 3, 'use_from' => true));
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "limitViaFrom");
        $this->assertEquals($expected, $actual);
    }
}