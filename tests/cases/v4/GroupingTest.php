<?php

namespace RexShijaku\SQLToCIBuilder\Test\v4;

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

class GroupingTest extends AbstractCases
{
    private $type = 'grouping';

    public function testGrouping()
    {
        $sql = "SELECT * FROM (members) WHERE ( age = 25 OR ( salary = 2000 AND gender = 'm' ) ) AND id > 100800";
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "grouping");
        $this->assertEquals($expected, $actual);
    }


}