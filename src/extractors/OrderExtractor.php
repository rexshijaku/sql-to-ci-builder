<?php

namespace RexShijaku\SQLToCIBuilder\extractors;
/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  orderBy
 *  orderByRaw
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class OrderExtractor extends AbstractExtractor implements Extractor
{
    public function extract(array $value, array $parsed = array())
    {
        $parts = array();
        foreach ($value as $k => $val) {
            if ($this->getValue($val['base_expr']) == 'rand' && $this->getValue($val['expr_type']) == 'function')
                $parts[] = array('field' => $this->getFnParams($val, $n), 'dir' => 'RANDOM', 'type' => 'fn');
            else
                $parts[] = array('field' => $val['base_expr'], 'dir' => $val['direction'], 'type' => 'normal');
        }
        return $parts;
    }
}