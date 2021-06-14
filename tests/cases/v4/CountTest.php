<?php

namespace RexShijaku\SQLToCIBuilder\Test\v4;

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

class CountTest extends AbstractCases
{
    private $type = 'count';

    public function testAvg()
    {
        $sql = "SELECT COUNT(*) FROM members";
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "count");
        $this->assertEquals($expected, $actual);
    }

    // todo add more, with alias, column and column alias
}