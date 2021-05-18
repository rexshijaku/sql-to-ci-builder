<?php

namespace RexShijaku\SQLToCIBuilder;

/**
 * This class provides additional functionality for the Creator class.
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class AbstractCreator
{
    public $qb;
    public $lastly;
    public $qb_closed;


    function isSingleTable($parsed)
    {
        if (isset($parsed['FROM'])) {
            $cnt = count($parsed['FROM']);
            if ($cnt == 1)
                return $parsed['FROM'][0]['expr_type'] == 'table';
            else {
                $cross_joins_cnt = 0;
                foreach ($parsed['FROM'] as $p)
                    if ($p['expr_type'] == 'table' || $p['expr_type'] == 'subquery')
                        if (strtolower(trim($p['join_type'])) == 'cross')
                            $cross_joins_cnt++;
                if (($cnt - $cross_joins_cnt) == 1)
                    return true;
            }
        }
        return false;
    }

    function isJoinedUpdate($parsed)
    {
        return isset($parsed['UPDATE']) && count($parsed['UPDATE']) > 1;
    }

    public function resetQ()
    {
        $this->qb = '';
        $this->lastly = '';
    }

}