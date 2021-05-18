<?php

namespace RexShijaku\SQLToCIBuilder\builders;


use RexShijaku\SQLToCIBuilder\utils\FromQueryTypes;

/**
 * This class constructs and produces following Query Builder methods :
 *
 * v 3
 * from
 * get
 *
 * v 4
 * table
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class FromBuilder extends AbstractBuilder implements Builder
{

    public function build(array $parts, array &$skip_bag = array())
    {
        $table = $parts['from_table'];
        $q = '';
        if ($this->options['civ'] < 4)
            $q .= '->from(' . $this->quote($table) . ')';
        else
            $this->query_start = '->table(' . $this->quote($table) . ')';
        return $q;
    }

    public function buildSingle(array $parts, array &$skip_bag)
    {
        $type = $parts['f_type'];
        $this->handleClosuresOnUnion($type, $parts);

        switch ($type) {
            case FromQueryTypes::FromQuery:
                $qb = $this->build(array('from_table' => $parts['table']), $skip_bag);
                return array('query_part' => $qb, 'type' => 'eq', 'close_qb' => false);
            case FromQueryTypes::Get:
                if ($parts['limit'] !== false)
                    $skip_bag[] = 'LIMIT';

                $builder = new SelectBuilder($this->options);
                $qb = $builder->createGetQ($parts['table'], $parts['limit']);
                $this->query_start = $builder->query_start;
                return array('query_part' => $qb, 'type' => 'lastly', 'close_qb' => true);
            default:
                break;
        }
    }

    private function handleClosuresOnUnion(&$type, &$parts)
    {
        if (isset($this->options['is_union']))
            if (in_array($type, FromQueryTypes::CLOSURES))
                $type = FromQueryTypes::FromQuery;
    }
}