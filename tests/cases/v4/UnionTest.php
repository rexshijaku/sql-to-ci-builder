<?php

namespace RexShijaku\SQLToCIBuilder\Test\v4;

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

class UnionTest extends AbstractCases
{
    private $type = 'union';

    public function testUnion()
    {
        $sql = "SELECT * FROM table1 UNION SELECT * FROM table2";
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "union");
        $this->assertEquals($expected, $actual);
    }
}