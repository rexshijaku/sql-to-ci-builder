<?php

namespace RexShijaku\SQLToCIBuilder\extractors;

/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 * delete
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class DeleteExtractor extends AbstractExtractor implements Extractor
{

    public function extract(array $value, array $parsed = array())
    {
        $parts = array();
        $parts['table'] = $parsed['FROM'][0]['base_expr'];
        if (isset($parsed['WHERE'])) {
            $criterion_ = new CriterionExtractor($this->options);
            if ($this->options['group'])
                $criterion_->extractAsArray(isset($parsed['WHERE']) ? $parsed['WHERE'] : array(), $where);
            else
                $where = false;
            $parts['where'] = array('single' => ($where !== false), 'value' => $where);
        }
        return $parts;
    }
}

