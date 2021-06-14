<?php

namespace RexShijaku\SQLToCIBuilder\extractors;

use RexShijaku\SQLToCIBuilder\utils\CriterionContext;
use RexShijaku\SQLToCIBuilder\utils\CriterionTypes;

/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  where
 *  orWhere
 *  whereRaw
 *  orWhereRaw
 *
 *  whereBetween
 *  orWhereNotBetween
 *  whereIn
 *  whereNotIn
 *
 *  whereNull
 *  whereNotNull
 *
 *  Logical Grouping methods
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class CriterionExtractor extends AbstractExtractor implements Extractor
{
    private $field_unescaped = array('colref', 'subquery', 'aggregate_function');
    private $negation_on = false;
    private $handle_outer_negation = false;

    public function extract(array $value, array $parsed = array())
    {
        $this->getCriteriaParts($value, $parts);
        return $parts;
    }

    function extractAsArray($value, &$part)
    {
        if (!$this->options['group']) {
            $part = false;
            return false;
        }

        $part = $this->getArrayParts($value);
        if ($part !== false)
            return true;
        return false;
    }

    function getCriteriaParts($value, &$parts = array(), $context = CriterionContext::Where) // for where + having
    {
        $curr_index = 0;
        $logical_operator = 'and';
        $this->negation_on = false;

        foreach ($value as $index => $val) {

            if ($index < $curr_index)
                continue; // skip those collected in inner loop

            if (in_array($val['expr_type'], array('operator', 'reserved'))) { // reserved since k+10 in (in is considered reserved)
                $sep = $this->getValue($val['base_expr']);
                if ($this->isLogicalOperator($sep)) {
                    $logical_operator = $this->getValue($val['base_expr']);
                    continue;
                }

                switch ($this->getValue($sep)) {
                    case $this->isComparisonOperator($sep):
                        $this->handle_outer_negation = true;
                        $res_field = $this->getLeft($index, $value, $context);
                        $res_value = $this->getRight($index, $value, $curr_index, $context);

                        $parts[] = array(
                            'type' => CriterionTypes::Comparison,
                            'operators' => array($sep),
                            'field' => $res_field['value'],
                            'value' => $res_value['value'],
                            'escape' => !($res_field['escape'] == false || $res_value['escape'] == false),
                            'sep' => $logical_operator
                        ); // now combine fields + operators

                        break;
                    case 'is':
                        $this->handle_outer_negation = true;

                        $res_field = $this->getLeft($index, $value);
                        $res_value = $this->getRight($index, $value, $curr_index);

                        $operators_ = array('is');
                        if ($res_value['has_negation'])
                            $operators_[] = 'not';

                        $parts[] = array(
                            'type' => CriterionTypes::Is,
                            'operators' => $operators_,
                            'field' => $res_field['value'],
                            'value' => $res_value['value'],
                            'escape' => $res_field['escape'],
                            'sep' => $logical_operator
                        ); // now combine fields + operators
                        break;
                    case "between":
                        $btw_operators = array();
                        if ($this->negation_on)
                            $btw_operators[] = 'not';
                        $btw_operators[] = 'between';

                        $res_field = $this->getLeft($index, $value);
                        $res_val = $this->getBetweenValue($index, $value, $curr_index);

                        $parts[] = array(
                            'type' => CriterionTypes::Between,
                            'operators' => $btw_operators,
                            'field' => $res_field['value'],
                            'values' => $res_val['value'],
                            'field_escape' => $res_field['escape'],
                            'value_escape' => $res_val['escapes'],
                            'sep' => $logical_operator); // now combine fields + operators
                        break;
                    case "like":
                        $like_operators = array();
                        if ($this->negation_on)
                            $like_operators[] = 'not';
                        $like_operators[] = 'like';

                        $res_field = $this->getLeft($index, $value);
                        $res_val = $this->getRight($index, $value, $curr_index);

                        $parts[] = array(
                            'type' => CriterionTypes::Like,
                            'operators' => $like_operators,
                            'field' => $res_field['value'],
                            'value' => $res_val['value'],
                            'escape' => !($res_field['escape'] == false || $res_val['escape'] == false),
                            'sep' => $logical_operator);

                        break;
                    case "in":
                        $in_operators = array();
                        if ($this->negation_on) // is not in ?
                            $in_operators[] = 'not';
                        $in_operators[] = 'in';

                        $res_field = $this->getLeft($index, $value);
                        $res_val = $this->getRight($index, $value, $curr_index, $context);


                        $parts[] = array(
                            'type' => CriterionTypes::InFieldValue,
                            'operators' => $in_operators,
                            'field' => $res_field['value'],
                            'value' => $res_val['value'],
                            'escape' => !($res_field['escape'] == false || $res_val['escape'] == false),
                            'sep' => $logical_operator,
                            'as_php_arr' => $res_val['value_type'] == 'in-list'
                        );
                        break;
                    case "not":
                        // if is group preceded by not, then it is handled as group in next interation
                        $this->negation_on = !$this->negation_on;
                        break;

                    default:
                        break;
                }
            } else if ($val['expr_type'] == 'bracket_expression') {
                $local = array();

                if ($val['sub_tree'] !== false) { // in case of () without anything inside
                    $negation_on = $this->negation_on;
                    $this->getCriteriaParts($val['sub_tree'], $local, $context);

                    if (!empty($local)) {
                        $parts[] = array('type' => CriterionTypes::Group, 'se' => 'start', 'subtype' => $logical_operator, 'negation' => $negation_on, 'context' => $context);
                        $parts = array_merge($parts, $local);
                        $parts[] = array('type' => CriterionTypes::Group, 'se' => 'end', 'negation' => $negation_on, 'context' => $context);
                    }
                }


            } else if ($val['expr_type'] == 'function') {
                if ($this->getValue($val['base_expr']) == 'against') {
                    $res_field = $this->getLeft($index, $value);
                    $res_val = $this->getRight($index, $value, $curr_index, $context);

                    $parts[] = array(
                        'type' => CriterionTypes::Against,
                        'field' => $res_field['value'],
                        'value' => $res_val['value'],
                        'sep' => $logical_operator
                    );

                }

            }
        }
    }

    private function getArrayParts($val)
    {
        $fields = array();
        $values = array();
        $operators = array();

        $next = 'field';
        $local_operators = array();

        foreach ($val as $v) {
            if ($v['expr_type'] == 'operator') {

                if ($this->getValue($v['base_expr']) == 'not' && (count($fields) - count($values)) == 0)
                    return false; // where not a is null
                if ($this->isComparisonOperator($v['base_expr'], array("is", "not"))) { // in this case [is, not] are are valid comparison operators
                    $local_operators[] = $v['base_expr'];
                    continue;
                }
                if ($this->getValue($v['base_expr']) != 'and') // prevent group on [or] operator, also in any operation too such as field1+field2 > number (this needs not to be escaped, therefore it will be as separate row)
                    return false;
            } else {

                if ($next == 'field') {

                    if (!in_array($v['expr_type'], $this->field_unescaped)) // not escaped by codeigniter, so allow
                        return false;

                    $field = $this->getExpression($v);
                    if (in_array($field, $fields)) // dont allow grouping in duplicate keys (because of php arrays)
                        return false;

                    $fields[] = $field;
                    $next = 'value';
                } else if ($next == 'value') {

                    if ($v['expr_type'] !== 'const')
                        return false;

                    $operators[] = implode(' ', $local_operators);
                    $local_operators = array();
                    $values[] = $this->getExpression($v);
                    $next = 'field';
                }
            }
        }

        if (count($fields) != count($values) || count($fields) < 1)
            return false;

        return array('fields' => $fields, 'operators' => $operators, 'values' => $values);
    }

    function getLeft($index, $value, $context = CriterionContext::Where)
    {
        $has_negation = false;
        $field_value = '';
        $left_ind = $index;
        $left_operator = '';
        $escape = true;

        while (!$this->isLogicalOperator($left_operator)) {
            if ($left_ind > 0) {
                $left_ind--;
                $op_type = $this->getValue($value[$left_ind]['expr_type']);
                if ($op_type == 'operator') {
                    if (!$this->isArithmeticOperator($this->getValue($value[$left_ind]['base_expr']))) {
                        $left_operator = $this->getValue($value[$left_ind]['base_expr']);
                    } else {
                        $escape = false;
                        $field_value = $value[$left_ind]['base_expr'] . $field_value;
                    }
                } else if ($context == CriterionContext::Having && $op_type == 'colref' && trim($value[$left_ind]['base_expr']) == ',') {
                    break;
                } else {
                    if ($op_type == 'reserved') // where x like '%abc'; stop at where, todo needs a better solution
                        break;

                    $field_value = $this->getExpression($value[$left_ind]) . $field_value;
                }
            } else
                break;
        }


        if ($this->handle_outer_negation && $this->negation_on) {
            $field_value = ' NOT ' . $field_value;
            $escape = false;
        }

        $this->negation_on = false;
        $this->handle_outer_negation = false;

        return array('value' => $field_value, 'has_negation' => $has_negation, 'escape' => $escape);
    }

    function getRight($index, $value, &$curr_index, $context = CriterionContext::Where)
    {
        $has_negation = false;
        $value_ = '';
        $value_type = '';
        $right_ind = $index;
        $right_operator = '';
        $escape = true;
        while (!$this->isLogicalOperator($right_operator)) { // x > 2 and (until you find first logical operator keep looping)
            $right_ind++;
            if ($right_ind < count($value)) {
                if ($this->getValue($value[$right_ind]['expr_type']) == 'operator') {


                    $right_operator = $this->getValue($value[$right_ind]['base_expr']);
                    if ($right_operator == 'not') {
                        $has_negation = true;
                        continue;
                    }

                    if ($this->isLogicalOperator($right_operator))
                        continue;

                    $value_ .= $value[$right_ind]['base_expr'];

                    if ($value[$right_ind]['expr_type'] != 'const')
                        $escape = false;


                } else if ($context == CriterionContext::Having
                    && $this->getValue($value[$right_ind]['expr_type']) == 'colref'
                    && trim($value[$right_ind]['base_expr']) == ','
                ) {
                    break;
                } else {

                    $val = $this->getExpression($value[$right_ind]);
                    $value_type = $value[$right_ind]['expr_type']; // used only in where in queries

                    if ($value[$right_ind]['expr_type'] != 'const') {
                        if (!($context == CriterionContext::Where && $value[$right_ind]['expr_type'] == 'in-list'))
                            $escape = false;
                    }
                    $value_ .= $val;
                }
            } else
                break;
        }
        $curr_index = $right_ind;
        return array('value' => $value_, 'has_negation' => $has_negation, 'escape' => $escape, 'value_type' => $value_type);
    }

    private function getBetweenValue($index, $value, &$curr_index)
    {
        $has_negation = false;
        $final = $escapes = $values_ = array();
        $right_ind = $index;
        $right_operator = '';

        $log_operator_count = 0;
        $escape = true;
        while ($log_operator_count != 2) { // between x and y and (until you find second logical operator keep looping)
            $right_ind++;
            if ($right_ind < count($value)) {
                if ($this->getValue($value[$right_ind]['expr_type']) == 'operator') {
                    if (!$this->isArithmeticOperator($value[$right_ind]['base_expr']))
                        $right_operator = $this->getValue($value[$right_ind]['base_expr']);

                    if ($right_operator == 'not') {
                        $has_negation = true;
                        $right_operator = '';
                        continue;
                    }

                    if ($this->isLogicalOperator($right_operator)) {
                        $log_operator_count++;
                        $right_operator = '';
                        $final[] = $values_;
                        $escapes[] = $escape;
                        $escape = true;
                        $values_ = array();
                        continue;
                    }

                }

                if ($value[$right_ind]['expr_type'] != 'const')
                    $escape = false;

                $values_ [] = $this->getAllValue($value[$right_ind]);
            } else
                break;
        }

        if (!empty($values_)) {
            $final[] = $values_;
            $escapes[] = $escape;
        }
        $curr_index = $right_ind;
        return array('value' => $final, 'has_negation' => $has_negation, 'escapes' => $escapes);
    }

    private function getAllValue($val)
    {
        $this->getExpressionParts(array($val), $parts);
        return $this->mergeExpressionParts($parts);
    }
}