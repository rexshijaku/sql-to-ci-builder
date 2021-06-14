<?php

namespace RexShijaku\SQLToCIBuilder\Test\v3;

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

class JoinTest extends AbstractCases
{
    private $type = 'join';

    public function testJoin()
    {
        $sql = 'SELECT * FROM members JOIN details ON members.id = details.members_id';
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "join");
        $this->assertEquals($expected, $actual);
    }

    public function testLeft()
    {
        $sql = 'SELECT * FROM members LEFT JOIN details ON members.id = details.members_id';
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "left");
        $this->assertEquals($expected, $actual);
    }

    public function testRight()
    {
        $sql = 'SELECT * FROM members RIGHT JOIN details ON members.id = details.members_id';
        $converter = new SQLToCIBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "right");
        $this->assertEquals($expected, $actual);
    }

    //todo advanced?
}