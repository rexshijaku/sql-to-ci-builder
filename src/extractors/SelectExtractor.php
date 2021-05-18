<?php

namespace RexShijaku\SQLToCIBuilder\extractors;

use RexShijaku\SQLToCIBuilder\utils\SelectQueryTypes;

/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  table
 *  distinct
 *  select
 *  sum
 *  avg
 *  min
 *  max
 *  count
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class SelectExtractor extends AbstractExtractor implements Extractor
{
    public function extract(array $value, array $parsed = array())
    {
        $distinct = $this->is_distinct($value);
        if ($distinct)
            array_shift($value);

        if (count(array_diff(array_keys($parsed), array('SELECT', 'FROM'))) == 0 && $this->isSingleTable($parsed) &&
            $this->is_count_table($value)) {

            return array('s_type' => SelectQueryTypes::CountATable, 'parts' => array('table' => $parsed['FROM'][0]['base_expr'], 'distinct' => $distinct, 'selected' => 'COUNT(*)'));

        } else if ($this->is_aggregate($value))
            return array('s_type' => SelectQueryTypes::Aggregate,
                'parts' => $this->extractAggregateParts($value, $distinct));
        else if ($this->isSingleTable($parsed)) { // todo in joins as well

            if (!$this->options['use_from']) {
                $max_clauses = 2; // from + select clauses

                if ($this->is_get_table($value[0])) {

                    $from = $parsed['FROM'][0]['base_expr'];
                    $limit = isset($parsed['LIMIT']) ? $parsed['LIMIT'] : false;
                    $where = isset($parsed['WHERE']) ? $parsed['WHERE'] : false;

                    if ($limit !== false) {
                        $max_clauses++;
                    }
                    if ($where !== false)
                        $max_clauses++;

                    if (count($parsed) == $max_clauses) {
                        $criterion_ = new CriterionExtractor($this->options);
                        $where !== false ? $criterion_->extractAsArray($where, $where_as_array) : $where_as_array = false;
                        if ($where_as_array !== false) { // can get_where be returned?
                            return array('s_type' => SelectQueryTypes::GetWhere, 'parts' => array('from' => $from, 'where' => $where_as_array, 'limit' => $limit, 'distinct' => $distinct, 'selected' => '*'));
                        } else {   //55a  $this->db->get(table,1,2); ?

                            return array('s_type' => SelectQueryTypes::Get, 'parts' => array('from' => $from, 'limit' => $limit, 'distinct' => $distinct, 'selected' => '*'));
                        }
                    }

                }
            }

        }

        $this->getExpressionParts($value, $parts, $raw);
        return array('s_type' => SelectQueryTypes::Other, 'parts' => array('selected' => $parts, 'distinct' => $distinct));
    }

    private function is_aggregate($value)
    {
        return count($value) == 1 && $value[0]['expr_type'] == 'aggregate_function'
            && in_array($this->getValue($value[0]['base_expr']), $this->options['settings']['agg']);
    }

    private function is_distinct($value)
    {
        return count($value) > 0 && $value[0]['expr_type'] == 'reserved' && $this->getValue($value[0]['base_expr']) == 'distinct';
    }

    private function extractAggregateParts($value, $distinct)
    {
        $fn_suffix = $this->getValue($value[0]['base_expr']);
        $this->getExpressionParts($value[0]['sub_tree'], $parts);
        $column = implode('', $parts);

        $alias = $this->isAlias($value[0]);
        if ($alias)
            $alias = $value[0]['alias']['name'];
        return array('suffix' => $fn_suffix, 'column' => $column, 'alias' => $alias, 'distinct' => $distinct);
    }

    private function is_count_table($value)
    {
        return count($value) == 1 && $value[0]['expr_type'] == 'aggregate_function' && $this->getValue($value[0]['base_expr']) == 'count' && $this->getFnParams($value[0], $d) === "*";
    }

    private function is_get_table($value)
    {
        return $value['expr_type'] == 'colref' && $value['base_expr'] == '*';
    }
}