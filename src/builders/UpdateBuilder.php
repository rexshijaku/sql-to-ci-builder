<?php

namespace RexShijaku\SQLToCIBuilder\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  update
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class UpdateBuilder extends AbstractBuilder implements Builder
{
    public function build(array $parts, array &$skip_bag = array())
    {
        $skip_bag[] = 'SET';

        $ci_parts = $parts['is_batch'] ? array('update', 'batch') : array('update');
        $ci_part = $this->fnMerger($ci_parts);
        $q = '';
        if ($this->options['civ'] < 4) {
            if ($this->options['group'] && $parts['inline_set']) {
                $update_q = "->" . $ci_part . "(" . $this->quote($parts['table']) . ', ' . $this->getSetAsArray($parts['records']);
                if ($parts['where'] !== false) //200a
                {
                    $skip_bag[] = 'WHERE';
                    $update_q .= ', ' . $this->arrayify($parts['where']) . ')';
                } else
                    $update_q .= ')';
            } else {
                $update_q = $this->getSetAsNormalSets($parts['records']);
                $update_q .= "->" . $ci_part . "(" . $this->quote($parts['table']) . ')';
            }
        } else {
            $q = '->table(' . $this->quote($parts['table']) . ')'; // todo can where be before this?
            if ($this->options['group'] && $parts['inline_set']) {
                $update_q = '->set(' . $this->getSetAsArray($parts['records']) . ')';
            } else {
                $update_q = $this->getSetAsNormalSets($parts['records']);
            }
            $update_q .= '->update()';
        }

        return array('finally' => $update_q, 'q' => $q);
    }

    private function getSetAsArray($records)
    {
        if (empty($records))
            return 'array()';

        $inner_array = '';
        foreach ($records as $record) {
            if (!empty($inner_array))
                $inner_array .= ', ';
            $inner_array .= ($this->quote($record['field']) . ' => ' . $this->wrapValue($record['value']));
        }
        return 'array(' . $inner_array . ')';
    }

    private function getSetAsNormalSets($records)
    {
        if (empty($records))
            return '';

        $local_qb = '';
        foreach ($records as $record) {
            $local_qb .= "->set(" . $this->quote($record['field']) . ", " . $this->wrapValue($record['value']);
            if (!$record['escape'])
                $local_qb .= ', FALSE';
            $local_qb .= ")";
        }
        return $local_qb;
    }
}