<?php

namespace RexShijaku\SQLToCIBuilder\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 * delete
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class DeleteBuilder extends AbstractBuilder implements Builder
{
    public function build(array $parts, array &$skip_bag = array())
    {
        $skip_bag[] = 'FROM';
        if (isset($parts['where'])) {
            if ($parts['where']['single']) {
                $skip_bag[] = 'WHERE';
                $delete_q = $this->buildSingleLine($parts['table'], $parts['where']['value']);
            } else {
                $delete_q = $this->delete($parts['table']);
            }
        } else {

            $ci_part = $this->fnMerger(array('empty', 'table'));
            $delete_q = '->' . $ci_part . '(' . $this->quote($parts['table']) . ')'; // 100b
        }

        return $delete_q;
    }

    private function delete($table)
    {
        if ($this->options['civ'] < 4)
            $qb = '->delete(' . $this->quote($table) . ')';
        else {
            $this->query_start = '->table(' . $this->quote($table) . ')';
            $qb = '->delete()';
        }
        return $qb;
    }

    private function buildSingleLine($table, $where)
    {
        if ($this->options['civ'] < 4)
            $qb = '->delete(' . $this->quote($table) . ', ' . $this->arrayify($where) . ')';
        else {
            $this->query_start = '->table(' . $this->quote($table) . ')';
            $qb = '->delete(' . $this->arrayify($where) . ')';
        }
        return $qb;
    }

}