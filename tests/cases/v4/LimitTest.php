<?php

namespace RexShijaku\SQLToCIBuilder\Test\v4;

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

    public function testLimitOnly()
    {
        $sql = "SELECT * FROM members LIMIT 10";
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "limitOnly");
        $this->assertEquals($expected, $actual);
    }
}