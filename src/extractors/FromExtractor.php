<?php

namespace RexShijaku\SQLToCIBuilder\extractors;

use RexShijaku\SQLToCIBuilder\utils\FromQueryTypes;

/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 * table
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class FromExtractor extends AbstractExtractor implements Extractor
{
    public function extract(array $value, array $parsed = array())
    {
        $parts = array();

        foreach ($value as $val) {
            if (!isset($parts['from_table'])) {
                $parts['from_table'] = $this->getTables($value);
            } else {
                if (!$this->validJoin($val['join_type'])) { // such as natural join
                    $join = array(
                        'type' => $val['join_type'],
                        'table_expr' => $val['base_expr']
                    );
                    $parts['joins'][] = $join;
                }
            }
        }

        return $parts;
    }

    function extractSingle($value, $parsed)
    {
        if (!$this->options['use_from']) {
            $limit = isset($parsed['LIMIT']) ? $parsed['LIMIT'] : false;
            return array('table' => $this->getTables($value), 'limit' => $limit, 'f_type' => FromQueryTypes::Get);
        } else {
            return array('table' => $this->getTables($value), 'f_type' => FromQueryTypes::FromQuery);
        }
    }

    private function getTables($value)
    {
        $tables[] = $this->getTableVal($value[0]);
        foreach ($value as $p) // extract cross joins if any
            if ($p['expr_type'] == 'table' || $p['expr_type'] == 'subquery')
                if ($this->getValue($p['join_type']) == 'cross')
                    $tables[] = $this->getTableVal($p);

        return implode(', ', $tables);
    }
}