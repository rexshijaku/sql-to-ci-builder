<?php

namespace RexShijaku\SQLToCIBuilder\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  query which contains the union statement
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class UnionBuilder extends AbstractBuilder implements Builder
{

    public function build(array $parts, array &$skip_bag = array())
    {
        $q = '';
        foreach ($parts as $k => $part) {
            if ($k > 0) {
                $key_word = $part['is_all'] == 1 ? ' UNION ALL ' : ' UNION ';
                $q .= '.' . $this->quote($key_word) . '.';
            }

            $part['str'] .= '->' . $this->fnMerger(array('get', 'compiled', 'select')) . '()';
            $q .= $part['str'];
        }
        if ($this->options['civ'] >= 4)
            return '->query(' . $q . ')';
        else
            return '->query(' . $q . ')->get()';
    }
}