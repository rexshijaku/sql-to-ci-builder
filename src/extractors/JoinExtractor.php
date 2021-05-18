<?php

namespace RexShijaku\SQLToCIBuilder\extractors;

/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  join
 *  leftJoin
 *  rightJoin
 *  crossJoin
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class JoinExtractor extends AbstractExtractor implements Extractor
{

    public function extract(array $value, array $parsed = array())
    {
        $joins = array();
        foreach ($value as $k => $val) {
            if ($k == 0) // skip from table
                continue;

            if (!$this->validJoin($val['join_type'])) // skip joins such as natural
                continue;

            if ($this->handledJoinTypes($val['join_type']))
                continue;

            $join_table = $this->getTableVal($val);

            $separators = array();
            $condition_value = array();
            if ($val['ref_clause'] !== false) {
                foreach ($val['ref_clause'] as $r) {
                    if ($r['expr_type'] == 'operator')
                        $separators[] = $r['base_expr'];
                    else
                        $condition_value[] = $r['base_expr'];
                }
            }

            $join = array('table' => $join_table,
                'condition_fields' => $condition_value,
                'condition_separators' => $separators,
                'type' => $val['join_type']
            );
            $joins[] = $join;
        }
        return $joins;
    }
}