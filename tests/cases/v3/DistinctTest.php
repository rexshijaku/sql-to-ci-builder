<?php

namespace RexShijaku\SQLToCIBuilder\Test\v3;

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

class DistinctTest extends AbstractCases
{
    private $type = 'distinct';

    public function testAll()
    {
        $sql = "SELECT DISTINCT * FROM members";
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "all");
        $this->assertEquals($expected, $actual);
    }
}