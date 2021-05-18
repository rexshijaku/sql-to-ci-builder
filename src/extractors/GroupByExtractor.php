<?php

namespace RexShijaku\SQLToCIBuilder\extractors;
/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  groupBy
 *  groupByRaw
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class GroupByExtractor extends AbstractExtractor implements Extractor
{
    public function extract(array $value, array $parsed = array())
    {
        $parts = array(); // columns
        foreach ($value as $k => $val) {
            $parts_tmp = array();
            $this->getExpressionParts(array($val), $parts_tmp); // expression parts since it can be anything! such as fn, subquery etc.
            $parts[] = $this->mergeExpressionParts($parts_tmp);
        }
        return $parts;
    }
}