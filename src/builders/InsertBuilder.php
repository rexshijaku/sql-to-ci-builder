<?php

namespace RexShijaku\SQLToCIBuilder\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  insert
 *  insert_batch
 *  replace
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class InsertBuilder extends AbstractBuilder implements Builder
{

    public function build(array $parts, array &$skip_bag = array())
    {
        $qb = '';
        $command = $this->options['command'];
        $record_len = count($parts['records']);
        $column_len = count($parts['columns']);

        if ($record_len == 0)
            return false;

        $is_batch = $record_len > 1;

        $inner_arrays = '';
        foreach ($parts['records'] as $record_key => $record) {

            if (count($record) != $column_len)
                exit('Invalid value-column!');

            if ($is_batch && $record_key > 0)
                $inner_arrays .= ", ";

            $single_array = $is_batch ? 'array(' : '';
            foreach ($record as $k => $col_val) {
                if ($k > 0)
                    $single_array .= ', ';
                $single_array .= $this->quote($parts['columns'][$k]) . ' => ' . ($this->wrapValue($col_val));
                //todo datum subtree? +escape removal
            }
            $inner_arrays .= $single_array . ($is_batch ? ')' : '');
        }

        if (!empty($inner_arrays)) {
            $outer_array = 'array(' . $inner_arrays . ')';
            $qb .= $this->buildLines($is_batch ? array($command, 'batch') : array($command), $parts['table'], $outer_array);
        } else
            exit('some problem');

        return $qb;
    }

    private function buildLines($ci_parts, $table, $outer_array)
    {
        $ci_part = $this->fnMerger($ci_parts);

        if ($this->options['civ'] < 4) {
            if (!$this->options['single_command']) {
                $qb = '->set(' . $outer_array . ')'; // todo set line-by-line single?!
                $qb .= "->" . $ci_part . "(" . $this->quote($table) . ')';
            } else
                $qb = "->" . $ci_part . "(" . $this->quote($table) . ', ' . $outer_array . ')';
        } else {
            $qb = "->table(" . $this->quote($table) . ')';
            if (!$this->options['single_command']) {
                $qb .= '->set(' . $outer_array . ')'; // todo set line-by-line single?!
                $qb .= "->" . $ci_part . '()';
            } else {
                $qb .= "->" . $ci_part . "(" . $outer_array . ')';
            }
        }
        return $qb;
    }

}