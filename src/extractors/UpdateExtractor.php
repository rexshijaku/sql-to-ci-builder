<?php

namespace RexShijaku\SQLToCIBuilder\extractors;

/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  update
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class UpdateExtractor extends AbstractExtractor implements Extractor
{


    public function extract(array $value, array $parsed = array())
    {
        $criterion_ = new CriterionExtractor($this->options);
        if ($this->options['group']) // todo ?!
            $criterion_->extractAsArray(isset($parsed['WHERE']) ? $parsed['WHERE'] : array(), $where);
        else
            $where = false;

        $table = $value[0]['base_expr'];
        $records = array(); // collect data so you gave only records and know about if is it batch or not
        $inline_set = true;
        foreach ($parsed['SET'] as $key => $item) {
            if ($item['expr_type'] == 'expression') {
                $curr_index = 0;
                foreach ($item['sub_tree'] as $index => $inner) {

                    if ($index < $curr_index)
                        continue; // skip those collected in inner loop

                    if (in_array($inner['expr_type'], array('operator', 'reserved'))) {

                        $left = $criterion_->getLeft($index, $item['sub_tree']);
                        $right = $criterion_->getRight($index, $item['sub_tree'], $curr_index);
                        $prevent_escape = ($left['escape'] == false || $right['escape'] == false);
                        $records[] = array('field' => $left['value'], 'value' => $right['value'], 'escape' => !$prevent_escape);
                        if ($prevent_escape)
                            $inline_set = false;
                    }
                }

            }

        }
        return array('table' => $table, 'records' => $records, 'where' => $where, 'is_batch' => false, 'inline_set' => $inline_set);
    }

    function validateUpdateQ($parser)
    {
        if (!isset($parser->parsed['SET']))
            return array('status' => false, 'message' => 'Not a valid Update Query (1)');

        if (!isset($value[0]['base_expr']))
            return array('status' => false, 'message' => 'Not a valid Update Query (2)');
    }

}