<?php

namespace RexShijaku\SQLToCIBuilder\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  order_by
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class OrderBuilder extends AbstractBuilder implements Builder
{

    function build(array $parts, array &$skip_bag = array())
    {
        $q = '';
        if ($this->options['single_command']) { // all in one line
            $inner = '';
            foreach ($parts as $k => $f_v) {
                if (!empty($inner))
                    $inner .= ', ';

                if ($f_v['type'] == 'fn')
                    $inner .= ($f_v['dir']) . ' (' . ($f_v['field']) . ')';
                else
                    $inner .= ($f_v['field']) . ' ' . ($f_v['dir']);
            }

            $q .= "->";
            $q .= $this->fnMerger(array('order', 'by'));
            $q .= "(" . $this->quote($inner) . ')';
        } else {
            foreach ($parts as $k => $f_v) {
                $q .= "->";
                $q .= $this->fnMerger(array('order', 'by'));
                $q .= "(" . $this->quote($f_v['field']) . ', ' . $this->quote($f_v['dir']) . ')';
            }
        }

        return $q;
    }

}