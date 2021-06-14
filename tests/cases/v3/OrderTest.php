<?php

namespace RexShijaku\SQLToCIBuilder\Test\v3;

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

class OrderTest extends AbstractCases
{
    private $type = 'order';

    public function testAscDesc()
    {
        $sql = "SELECT * FROM members ORDER BY name DESC, surname ASC";
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "ascDesc");
        $this->assertEquals($expected, $actual);
    }

    public function testByFunction()
    {
        $sql = "SELECT * FROM members ORDER BY RAND(42)";
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "byFunction");
        $this->assertEquals($expected, $actual);
    }
}