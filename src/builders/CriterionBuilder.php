<?php

namespace RexShijaku\SQLToCIBuilder\builders;


use RexShijaku\SQLToCIBuilder\utils\CriterionContext;
use RexShijaku\SQLToCIBuilder\utils\CriterionTypes;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  where
 *  or_where
 *
 *  where_in
 *  or_where_in
 *  where_not_in
 *
 *
 *  like
 *  or_like
 *  not_like
 *  or_not_like
 *
 *
 *  Query grouping
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class CriterionBuilder extends AbstractBuilder implements Builder
{

    public function build(array $parts, array &$skip_bag = array())
    {
        // todo very advanced : combine group and,or for each condition below!

        $query_val = '';

        foreach ($parts as $part) {

            if ($part['type'] == CriterionTypes::Comparison || $part['type'] == CriterionTypes::Is) {

                $disposable_operators = array('=', 'is');
                $op = implode(' ', $part['operators']);
                $op = str_replace("is not", "!=", $this->getValue($op));
                $op = in_array($op, $disposable_operators) ? '' : $op; // ignore '=' operator //todo is e kom shtu ma von (me kqyr)

                $part['value'] = $this->getValue($part['value']) == 'null' ? 'null' : $part['value'];
                $operator_tokens = $this->getValue($part['sep']) == 'and' ? array('where') : array('or', 'where');
                $ci_part = $this->fnMerger($operator_tokens);

                $inner = $this->quote($part['field'] . rtrim(' ' . $op)) . ', ' . $this->wrapValue($part['value']);

                if (!$part['escape'])
                    $inner .= ', FALSE';

                $query_val .= '->' . $ci_part . '(' . $inner . ')';

            } else if ($part['type'] == CriterionTypes::InFieldValue) {
                // if not simple such as where in, make it where_in, or_where_in, not in =>
                // not in, in
                $operator_tokens = $part['sep'] == 'or' ? array('or', 'where') : array('where');
                $operator_tokens = array_merge($operator_tokens, $part['operators']); // not + in part (depending on what is sent)
                $ci_part = $this->fnMerger($operator_tokens);

                $query_val .= '->' . $ci_part . '(' . $this->quote($part['field']) . ', ';
                if (isset($part['as_php_arr']) && $part['as_php_arr'] == true)
                    $query_val .= 'array' . $part['value'];
                else {
                    $query_val .= $this->wrapValue($this->unBracket($part['value']));
                }
                if (!$part['escape'])
                    $query_val .= ', FALSE';
                $query_val .= ')';
            } else if ($part['type'] == CriterionTypes::Like) {

                $without_quotation = trim($part['value'], '"');
                $without_quotation = trim($without_quotation, "'");

                // todo : percentage in value field will not work
                $starts_like = $this->startsWith($without_quotation, "%");
                $ends_like = $this->endsWith($without_quotation, "%");

                if ($starts_like && $ends_like) {
                    $side = 'both';
                    $without_quotation = ltrim($without_quotation, '%');
                    $part['value'] = rtrim($without_quotation, '%');
                } else if ($starts_like && !$ends_like) {
                    $side = 'before';
                    $part['value'] = ltrim($without_quotation, '%');
                } else if ($ends_like && !$starts_like) {
                    $side = 'after';
                    $part['value'] = rtrim($without_quotation, '%');
                } else
                    $side = 'none';

                $operator_tokens = array();
                if ($this->getValue($part['sep']) == 'or')
                    $operator_tokens[] = 'or';
                $operator_tokens = array_merge($operator_tokens, $part['operators']);
                $ci_part = $this->fnMerger($operator_tokens);

                $query_val .= '->' . $ci_part . '(' . $this->quote($part['field']) . ', ' . $this->wrapValue($part['value']) . ', ' . $this->quote($side);

                if (!$part['escape'])
                    $query_val .= ', FALSE';

                $query_val .= ')';

            } else if ($part['type'] == CriterionTypes::Between) {
                $query_val .= $this->buildBetween($part);
            } else if ($part['type'] == CriterionTypes::Group) {
                $this->buildGroup($part, $query_val);
            } else if ($part['type'] == CriterionTypes::Against) {
                $operator_tokens = $this->getValue($part['sep']) == 'and' ? array('where') : array('or', 'where');
                $ci_part = $this->fnMerger($operator_tokens);
                $query_val .= '->' . $ci_part . '(' . $this->quote($part['field'] . ' AGAINST ' . $part['value']) . ')';
            }
        }
        return $query_val;
    }


    public function buildAsArray(array $parts)
    {
        $query_val = $this->arrayify($parts);
        if ($query_val !== false)
            return '->where(' . $query_val . ')';
        return false;
    }

    function buildGroup($part, &$query_val)
    {
        if (in_array($part['se'], array('start', 'end'))) {

            $operator_tokens = array();
            if ($part['se'] == 'start') {

                $operator_tokens = array('group', 'start');
                if ($this->options['civ'] >= 4 && $part['context'] == CriterionContext::Having)
                    array_unshift($operator_tokens, 'having');
                if (isset($part['negation']) && $part['negation'])
                    array_unshift($operator_tokens, 'not');
                if ($part['subtype'] == 'or')
                    array_unshift($operator_tokens, 'or');


            } else if ($part['se'] == 'end') {
                $operator_tokens = array('group', 'end');
                if ($this->options['civ'] >= 4 && $part['context'] == CriterionContext::Having)
                    array_unshift($operator_tokens, 'having');
            }

            $query_val .= "->";
            $query_val .= $this->fnMerger($operator_tokens);
            $query_val .= "()";
        }

    }

    function buildBetween($part, $command = 'where')
    {
        $query = '';
        if ($part['sep'] == 'or')
            $command = $this->fnMerger(array('or', $command));
        if (in_array('not', $part['operators'])) { // not between

            $query .= '->' . $command . '(';
            $query .= '"' . "!(" . $part['field'] . " BETWEEN " . $this->wrapValue(implode('', $part['values'][0])) . " AND " . $this->wrapValue(implode('', $part['values'][1])) . ")" . '"';
            $query .= ")";
        } else {
            // between
            $pt_1 = '';
            $pt_2 = '';

            $pt_1 .= '->' . $command . '(';
            $pt_2 .= '->' . $command . '(';

            $pt_1 .= $this->quote($part['field'] . ' >=') . ', ' . $this->wrapValue(implode('', $part['values'][0]));
            $pt_2 .= $this->quote($part['field'] . ' <=') . ', ' . $this->wrapValue(implode('', $part['values'][1]));

            if (!$part['field_escape'] || !$part['value_escape'][0]) {
                $pt_1 .= ',FALSE';
            }
            if (!$part['field_escape'] || !$part['value_escape'][1]) {
                $pt_2 .= ',FALSE';
            }

            $pt_1 .= ')';
            $pt_2 .= ')';

            $query .= $pt_1;
            $query .= $pt_2;
        }
        return $query;
    }
}