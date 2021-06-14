<?php

namespace RexShijaku\SQLToCIBuilder\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  having
 *  or_having
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class HavingBuilder extends AbstractBuilder implements Builder
{
    public function build(array $parts, array &$skip_bag = array())
    {
        $criterion_builder = new CriterionBuilder($this->options);

        $groups = $parts;
        $query_val = '';

        foreach ($groups as $group) {

            if ($group['type'] == 'group') {

                $criterion_builder->buildGroup($group, $query_val);
                continue;
            }

            // todo : when between and escape take place then order is not as it would be better to be
            $remaining = array();
            // create special cases of that group first
            foreach ($group['items'] as $k => $item) {

                if ($item['type'] == 'between') {
                    $query_val .= $criterion_builder->buildBetween($item, 'having');
                } else if (!$item['escape']) {
                    $key_value = $this->havingInner($item, ',');
                    $this->having($item['sep'], $query_val, $key_value, false);
                } else {
                    $remaining[] = $item; // collect remained raw cases
                }
            }

            // create whatever left
            $inner_part = '';
            $remaining_items = count($remaining);
            if ($remaining_items == 1) { // single item?
                $key_value = $this->havingInner($remaining[0], ',');
                $inner_part .= $key_value;
            } else {
                $array_inner = ''; //
                foreach ($remaining as $item) {
                    // form inner array in multiple values
                    $key_value = $this->havingInner($item, '=>');
                    $array_inner .= $key_value;
                    $array_inner .= ',';
                }
                if (!empty($array_inner))
                    $inner_part .= 'array(' . trim($array_inner, ',') . ')';
            }

            if (!empty($inner_part))
                $this->having($group['operator'], $query_val, $inner_part);
        }

        return $query_val;
    }

    private function having($operator, &$query_val, $inner_part, $esc = true)
    {
        $query_val .= '->';
        if ($operator == 'and')
            $query_val .= 'having';
        else
            $query_val .= $this->fnMerger(array('or', 'having'));

        $query_val .= '(';
        $query_val .= $inner_part;
        if (!$esc)
            $query_val .= ', FALSE';
        $query_val .= ")";
    }

    private function havingInner($group, $delimiter)
    {
        $operator = implode(' ', $group['operators']);
        $inner = '';
        if ($group['type'] !== 'comparison') {
            $inner .= $this->quote($group['field'] . ' ' . $operator . ' ' . $group['value'],false);
            $inner .= '  ' . $delimiter;
            $inner .= 'null'; // all expression added to field, leave this empty
        } else {

            if ($operator !== '=') // ignore =
                $inner .= $this->quote($group['field'] . ' ' . $operator);
            else
                $inner .= $this->quote($group['field']);
            $inner .= $delimiter.' ';
            $inner .= $this->wrapValue($group['value']);
        }
        return $inner;
    }
}