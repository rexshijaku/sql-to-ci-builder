<?php

namespace RexShijaku\SQLToCIBuilder\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  v3
 *  group_by
 *
 *  v4
 *
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class GroupByBuilder extends AbstractBuilder implements Builder
{
    public function build(array $parts, array &$skip_bag = array())
    {
        $qb = '';
        $parts_len = count($parts);

        if ($parts_len == 0)
            return $qb;

        $inner = '';
        if ($parts_len == 1)
            $inner .= $this->quote($parts[0]);
        else if ($parts_len > 1)
            $inner .= "array(" . implode(", ", array_map(array($this, 'quote'), $parts)) . ")";

        $qb .= "->" . $this->fnMerger(array('group', 'by'));
        $qb .= '(' . $inner . ')';
        return $qb;
    }
}