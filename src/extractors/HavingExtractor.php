<?php

namespace RexShijaku\SQLToCIBuilder\extractors;

use RexShijaku\SQLToCIBuilder\utils\CriterionContext;

/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  having
 *  orHaving
 *  havingRaw
 *  orHavingRaw
 *  havingBetween
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class HavingExtractor extends AbstractExtractor implements Extractor
{
    public function extract(array $value, array $parsed = array())
    {
        $criterion = new CriterionExtractor($this->options);
        $criterion->getCriteriaParts($value, $parts, CriterionContext::Having);
        $this->havingPreGroup($parts);
        return $parts;
    }

    // group by logical operators [and,or]
    function havingPreGroup(&$parts)
    {
        $index = 0;
        $groups = array();
        $parts_len = count($parts);
        foreach ($parts as $k => $part) {

            if ($k < $index) // skip already grouped
                continue;

            if ($part['type'] == 'group') {
                $groups[] = $part;
                $index++;
                continue;
            }

            $items = array();
            $duplicate_fields = array();
            while (isset($parts[$index]['sep']) && $part['sep'] == $parts[$index]['sep']) {

                $key = $parts[$index]['field'];
                if (!key_exists($key, $items))
                    $items[$key] = $parts[$index];
                else
                    $duplicate_fields[$key] = $parts[$index]; // collect duplicates (to append at the end) since php array overrides them

                $index++;
                if ($parts_len <= $index)
                    break;
            }
            $groups[] = array('type' => 'comparison', 'operator' => $part['sep'], 'items' => $items);
            foreach ($duplicate_fields as $duplicate_field)
                $groups[] = array('type' => 'comparison', 'operator' => $part['sep'], 'items' => array($duplicate_field));
        }
        $parts = $groups;
    }

}
