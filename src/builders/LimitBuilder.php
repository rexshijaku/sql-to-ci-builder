<?php

namespace RexShijaku\SQLToCIBuilder\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  offset
 *  limit
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class LimitBuilder extends AbstractBuilder implements Builder
{
    public function build(array $parts, array &$skip_bag = array())
    {
        $query_val = '';

        $parts_len = count($parts);

        if ($parts_len == 0)
            return $query_val;

        if ($parts_len == 2) { // 2 means it consists both of rowcount and offset
            $inner = $this->buildInner($parts);
            $query_val .= "->limit(" . $inner . ')';
        } else if ($parts_len == 1) {

            if (isset($parts['rowcount']))
                $query_val .= "->limit(" . $parts['rowcount'] . ')';
            else if (isset($parts['offset']))
                $query_val .= "->offset(" . $parts['offset'] . ')';
        }

        return $query_val;
    }

    public function buildInner($parts)
    {
        $inner = '';

        $to_merge = array();
        if ($this->is_valid($parts, 'rowcount'))
            $to_merge[] = $parts['rowcount']; // adding in order important
        if ($this->is_valid($parts, 'offset'))
            $to_merge[] = $parts['offset']; //  adding in order important

        $inner .= implode(", ", $to_merge);
        return $inner;
    }

    private function is_valid($parts, $field)
    {
        return isset($parts[$field]) && !empty($parts[$field]);
    }

}