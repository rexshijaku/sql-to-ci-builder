<?php

namespace RexShijaku\SQLToCIBuilder;

use RexShijaku\SQLToCIBuilder\builders\CriterionBuilder;
use RexShijaku\SQLToCIBuilder\builders\DeleteBuilder;
use RexShijaku\SQLToCIBuilder\builders\FromBuilder;
use RexShijaku\SQLToCIBuilder\builders\GroupByBuilder;
use RexShijaku\SQLToCIBuilder\builders\HavingBuilder;
use RexShijaku\SQLToCIBuilder\builders\InsertBuilder;
use RexShijaku\SQLToCIBuilder\builders\JoinBuilder;
use RexShijaku\SQLToCIBuilder\builders\LimitBuilder;
use RexShijaku\SQLToCIBuilder\builders\OrderBuilder;
use RexShijaku\SQLToCIBuilder\builders\SelectBuilder;
use RexShijaku\SQLToCIBuilder\builders\UnionBuilder;
use RexShijaku\SQLToCIBuilder\builders\UpdateBuilder;
use RexShijaku\SQLToCIBuilder\extractors\CriterionExtractor;
use RexShijaku\SQLToCIBuilder\extractors\DeleteExtractor;
use RexShijaku\SQLToCIBuilder\extractors\FromExtractor;
use RexShijaku\SQLToCIBuilder\extractors\GroupByExtractor;
use RexShijaku\SQLToCIBuilder\extractors\HavingExtractor;
use RexShijaku\SQLToCIBuilder\extractors\InsertExtractor;
use RexShijaku\SQLToCIBuilder\extractors\JoinExtractor;
use RexShijaku\SQLToCIBuilder\extractors\LimitExtractor;
use RexShijaku\SQLToCIBuilder\extractors\OrderExtractor;
use RexShijaku\SQLToCIBuilder\extractors\SelectExtractor;
use RexShijaku\SQLToCIBuilder\extractors\UpdateExtractor;

/**
 * This class orchestrates the process between Extractors and Builders in order to produce parts of Query Builder and arranges them
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class Creator extends AbstractCreator
{
    private $main;
    public $options;
    public $skip_bag;

    function __construct($main, $options)
    {
        $this->main = $main;
        $this->options = $options;
        $this->skip_bag = array();

    }

    public function select($value, $parsed)
    {
        $extractor = new SelectExtractor($this->options);
        $builder = new SelectBuilder($this->options);

        $parts = $extractor->extract($value, $parsed);
        $build_res = $builder->build($parts, $this->skip_bag);

        $this->qb_closed = $build_res['close_qb'];
        if ($build_res['type'] == 'eq')
            $this->qb = $build_res['query_part'];
        else if ($build_res['type'] == 'lastly')
            $this->lastly = $build_res['query_part'];

        $this->qb = $builder->query_start . $this->qb;
    }

    public function from($value, $parsed)
    {
        $from_extractor = new FromExtractor($this->options);
        $from_builder = new FromBuilder($this->options);

        if ($this->isSingleTable($parsed)) { // when a single table involved // todo why this is here?!
            $from_parts = $from_extractor->extractSingle($value, $parsed); //59b //todo wtf njeri
            $build_res = $from_builder->buildSingle($from_parts, $this->skip_bag);

            $this->qb_closed = $build_res['close_qb'];
            if ($build_res['type'] == 'eq')
                $this->qb .= $build_res['query_part'];
            else if ($build_res['type'] == 'lastly')
                $this->lastly = $build_res['query_part'];


        } else {     // when more than one table involved

            $from_parts = $from_extractor->extract($value);
            if (isset($from_parts['joins'])) { // invalid joins found ?
                throw new \Exception('Invalid join type found! ');
            } else {
                $this->qb .= $from_builder->build($from_parts);

                $join_extractor = new JoinExtractor($this->options);
                $join_builder = new JoinBuilder($this->options);

                $joins = $join_extractor->extract($value);
                $this->qb .= $join_builder->build($joins);
            }
        }

        $this->qb = $from_builder->query_start . $this->qb;

    }

    public function where($value)
    {
        $extractor = new CriterionExtractor($this->options);
        $builder = new CriterionBuilder($this->options);

        if ($extractor->extractAsArray($value, $part))
            $q = $builder->buildAsArray($part);
        else {
            $parts = $extractor->extract($value);
            $q = $builder->build($parts);
        }
        $this->qb .= $q;
    }

    public function group_by($value)
    {
        $extractor = new GroupByExtractor($this->options);
        $builder = new GroupByBuilder($this->options);

        $parts = $extractor->extract($value);
        $q = $builder->build($parts);
        $this->qb .= $q;
    }

    public function limit($value)
    {
        $extractor = new LimitExtractor($this->options);
        $builder = new LimitBuilder($this->options);

        $parts = $extractor->extract($value);
        $q = $builder->build($parts);
        $this->qb .= $q;
    }

    public function having($value)
    {
        $extractor = new HavingExtractor($this->options);
        $builder = new HavingBuilder($this->options);

        $parts = $extractor->extract($value);
        $q = $builder->build($parts);
        $this->qb .= $q;
    }

    public function order($value)
    {
        $extractor = new OrderExtractor($this->options);
        $builder = new OrderBuilder($this->options);

        $parts = $extractor->extract($value);
        $q = $builder->build($parts);
        $this->qb .= $q;
    }

    public function insert($value, $parsed)
    {
        $this->options['command'] = 'insert'; // since the same fn s are used  to replace too

        $extractor = new InsertExtractor($this->options);
        $builder = new InsertBuilder($this->options);


        $parts = $extractor->extract($value, $parsed);
        $q = $builder->build($parts);
        $this->qb .= $q;
    }

    public function replace($value, $values)
    {
        $this->options['command'] = 'replace'; // since the same fn s are used to insert too

        $extractor = new InsertExtractor($this->options);
        $builder = new InsertBuilder($this->options);

        $parts = $extractor->extract($value, $values);
        $q = $builder->build($parts);
        $this->qb .= $q;
    }

    public function update($value, $parsed)
    {
        $extractor = new UpdateExtractor($this->options);
        $builder = new UpdateBuilder($this->options);

        if ($this->isJoinedUpdate($parsed)) {
            throw new \Exception('Multiple tables cannot be involved in UPDATE! Not supported yet!'); //todo 1001
        } else {
            $parts = $extractor->extract($value, $parsed);
            $q = $builder->build($parts, $this->skip_bag);

            $this->qb .= $q['q'];
            $this->lastly = $q['finally']; // 80a,b
        }
    }

    public function delete($parsed)
    {
        $extractor = new DeleteExtractor($this->options);
        $builder = new DeleteBuilder($this->options);

        if ($this->isSingleTable($parsed)) {
            $parts = $extractor->extract(array(), $parsed);
            $this->lastly = $builder->build($parts, $this->skip_bag);
        } else {
            throw new \Exception('Multiple tables cannot be involved in DELETE! Not supported yet!'); //todo 1001
        }
        $this->qb = $builder->query_start . $this->qb;
    }

    public function union($parts)
    {
        $builder = new UnionBuilder($this->options);
        $this->qb = $builder->build($parts);
    }

    function getQuery($sql, $add_db_instance = false)
    {
        $this->qb .= $this->lastly;
        if (empty($this->qb)) {
            if ($add_db_instance)
                $this->qb .= $this->options['db_instance'];
            $this->qb .= "->query('" . $sql . "')";
        } else {
            if (!$this->qb_closed)
                $this->qb .= isset($this->options['is_union']) ? '' : '->get()';
        }

        if (!isset($this->options['is_union']))
            $this->qb .= ';';
        return $this->qb;
    }
}