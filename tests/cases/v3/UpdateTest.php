<?php

namespace RexShijaku\SQLToCIBuilder\Test\v3;

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

class UpdateTest extends AbstractCases
{
    private $type = 'update';

    public function testAtomic()
    {
        $sql = "UPDATE members SET age = 10 WHERE id = 2";
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "atomic");
        $this->assertEquals($expected, $actual);
    }

    public function testColumnOperation()
    {
        $sql = "UPDATE members SET age = age+10 WHERE id = 2";
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "columnOperation");
        $this->assertEquals($expected, $actual);
    }
}