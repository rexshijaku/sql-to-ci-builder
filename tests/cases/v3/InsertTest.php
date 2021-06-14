<?php

namespace RexShijaku\SQLToCIBuilder\Test\v3;

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

class InsertTest extends AbstractCases
{
    private $type = 'insert';

    public function test()
    {
        $sql = "INSERT INTO members (name, surname, age) VALUES ('Jökull', 'Júlíusson', 30)";
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "insert");
        $this->assertEquals($expected, $actual);
    }

    public function testBatch()
    {
        $sql = "INSERT INTO members (name,surname, age) VALUES ('Jökull', 'Júlíusson', 30),  ('David', 'Antonsson', null), ('Daniel', 'Kristjansson', null), ('Rubin', 'Pollock', null), ('Þorleifur Gaukur', 'Davíðsson', null) ";
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "batch");
        $this->assertEquals($expected, $actual);
    }
}