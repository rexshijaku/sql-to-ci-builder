<?php

namespace RexShijaku\SQLToCIBuilder\builders;

use RexShijaku\SQLToCIBuilder\utils\SelectQueryTypes;


/**
 * This class constructs and produces following Query Builder methods :
 *
 *  table
 *  distinct
 *  get
 *  get_where
 *  select
 *  select_sum
 *  select_avg
 *  select_min
 *  select_max
 *  count_all
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class SelectBuilder extends AbstractBuilder implements Builder
{
    public function build(array $parts, array &$skip_bag = array())
    {
        $type = $parts['s_type'];
        $parts = $parts['parts'];

        $this->handleClosuresOnUnion($type, $parts);

        $qb = '';
        if ($parts['distinct'])
            $qb = $this->distinctQ();

        switch ($type) {
            case SelectQueryTypes::Aggregate:
                $qb .= $this->aggregateQ($parts['suffix'], $parts['column'], $parts['alias']);
                return array('query_part' => $qb, 'type' => 'eq', 'close_qb' => false);
            case SelectQueryTypes::Get:
                $skip_bag[] = 'FROM';
                if ($parts['limit'] !== false)
                    $skip_bag[] = 'LIMIT';
                $qb .= $this->createGetQ($parts['from'], $parts['limit']);
                return array('query_part' => $qb, 'type' => 'lastly', 'close_qb' => true);
            case SelectQueryTypes::GetWhere:
                $skip_bag[] = 'FROM';
                $skip_bag[] = 'WHERE';
                if ($parts['limit'] !== false)
                    $skip_bag[] = 'LIMIT';
                $qb .= $this->getWhereQ($parts['from'], $parts['where'], $parts['limit']);
                return array('query_part' => $qb, 'type' => 'lastly', 'close_qb' => true);
            case SelectQueryTypes::CountATable:
                $skip_bag[] = 'FROM';
                $qb .= $this->countAllQ($parts['table']);
                return array('query_part' => $qb, 'type' => 'lastly', 'close_qb' => true);
            case SelectQueryTypes::Other:
                $qb .= $this->selectOnlyQ($parts['selected']);
                return array('query_part' => $qb, 'type' => 'eq', 'close_qb' => false);
            default:
                break;
        }
    }

    private function handleClosuresOnUnion(&$type, &$parts)
    {
        if (isset($this->options['is_union'])) {
            if (in_array($type, SelectQueryTypes::CLOSURES)) {
                $type = SelectQueryTypes::Other;
            }
        }
    }

    private function aggregateQ($suffix, $column, $alias)
    {
        $qb = "->";
        $qb .= $this->fnMerger(array("select", $this->getValue($suffix)));
        $qb .= "(";
        $qb .= $this->quote($column);
        if ($alias !== false)
            $qb .= ', ' . $this->quote($alias);
        $qb .= ")";
        return $qb;
    }

    function createGetQ($table, $limit)
    {
        $limit_set = $limit !== false;

        if ($this->options['civ'] < 4) // in previous version get(table..limit)
        {
            $qb = '->';
            $qb .= $this->getPart();
            $qb .= '(' . $this->quote($table);
            if ($limit_set)
                $qb .= ', ';
        } else {
            $this->query_start = '->table(' . $this->quote($table) . ')';
            $qb = '->get(';
        }

        if ($limit_set) {
            $builder = new LimitBuilder($this->options);
            $qb .= $builder->buildInner($limit);
        }
        $qb .= ')';
        return $qb;
    }

    private function getWhereQ($table, $where, $limit)
    {
        $from_q = '';
        $qb = '->';
        $qb .= $this->fnMerger(array('get', 'where'));
        $qb .= '(';

        if ($this->options['civ'] < 4) // in previous version get_where(table,where)
            $qb .= $this->quote($table) . ', ' . $this->arrayify($where);
        else // now getWhere(where);
        {
            $this->query_start = '->table(' . $this->quote($table) . ')';
            $qb .= $this->arrayify($where);
        }

        if ($limit !== false) {
            $qb .= ', ';
            $builder = new LimitBuilder($this->options);
            $qb .= $builder->buildInner($limit);
        }
        $qb .= ')';
        return $from_q . $qb;
    }

    private function selectOnlyQ($parts)
    {
        $inner = is_array($parts) ? $this->quote(implode(',', $parts)) : $this->quote($parts);
        $query = '->';
        $ci_part = false ? 'select_raw' : 'select';
        $query .= $ci_part . "(";
        $query .= $inner;
        $query .= ")";
        return $query;
    }

    private function countAllQ($table)
    {
        $ci_part = $this->fnMerger(array('count', 'all'));
        return '->' . $ci_part . '(' . $this->quote($table) . ')';
    }

    private function distinctQ()
    {
        return '->distinct()';
    }
}