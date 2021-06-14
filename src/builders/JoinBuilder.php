<?php

namespace RexShijaku\SQLToCIBuilder\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  join
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class JoinBuilder extends AbstractBuilder implements Builder
{

    public function build(array $parts, array &$skip_bag = array())
    {
        $qb = '';
        if (!isset($parts))
            return $qb;

        foreach ($parts as $join) {
            $condition_part = (implode(' ' . implode(' ', $join['condition_separators']) . ' ', $join['condition_fields']));
            if ($this->getValue($join['type']) !== 'join')
                $qb .= "->join(" . $this->quote($join['table']) . ", " . $this->quote($condition_part) . ", " . $this->quote($join['type']) . ")";
            else
                $qb .= "->join(" . $this->quote($join['table']) . ", " . $this->quote($condition_part) . ")";
        }

        return $qb;
    }
}