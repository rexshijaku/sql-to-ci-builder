<?php

namespace RexShijaku\SQLToCIBuilder;

use PHPSQLParser\PHPSQLParser;


class SQLToCIBuilder
{
    private $sql;
    private $creator;
    private $options;
    private $temp;

    public function __construct($options = null)
    {
        $this->options = new Options($options);
    }

    /**
     * Converts a SQL Query into Query Builder
     * @param $sql
     * @return string
     */
    public function convert($sql)
    {
        $this->sql = $sql;
        try {

            $this->options->set();
            $this->unionHandler();
            $this->options->validate();

            $parser = new PHPSQLParser($this->sql);
            $parsed = is_array($parser->parsed) ? $parser->parsed : array();
            $this->creator = new Creator($this, $this->options->get());
            return $this->parse($parsed);
        } catch (\Exception $exception) {

            if (isset($this->creator)) {
                $this->creator->resetQ();
                return $exception->getMessage() .
                    ' Alternative result : ' . $this->creator->getQuery($this->sql, true);
            } else {
                return $exception->getMessage();
            }
        }

    }

    private function parse($parsed)
    {


        foreach ($parsed as $k => $value) {

            if (in_array($k, $this->creator->skip_bag))
                continue;

            switch ($k) {
                case 'SELECT':
                    $this->creator->select($value, $parsed);
                    break;
                case 'FROM':
                    $this->creator->from($value, $parsed);
                    break;
                case 'WHERE':
                    $this->creator->where($value);
                    break;
                case 'GROUP':
                    $this->creator->group_by($value);
                    break;
                case 'LIMIT':
                    $this->creator->limit($value);
                    break;
                case 'HAVING':
                    $this->creator->having($value);
                    break;
                case 'ORDER':
                    $this->creator->order($value);
                    break;
                case 'INSERT':
                    $skip_bag[] = 'VALUES';
                    $this->creator->insert($value, $parsed);
                    $this->creator->qb_closed = true;
                    break;
                case 'REPLACE':
                    $skip_bag[] = 'VALUES';
                    $this->creator->replace($value, $parsed);
                    $this->creator->qb_closed = true;
                    break;
                case 'UPDATE':
                    $this->creator->update($value, $parsed);
                    $this->creator->qb_closed = true;
                    break;
                case "DELETE":
                    $this->creator->qb_closed = true;
                    $this->creator->delete($parsed);
                    break;
                case "UNION":
                    $parts = array();
                    $this->creator->options['is_union'] = true;
                    foreach ($value as $key => $q) {
                        $this->creator->resetQ();
                        $single_parts = $this->parse($q);

                        $part = array('str' => $single_parts);
                        if($key > 0)
                            $part['is_all'] = $this->temp[$key];
                        $parts[] = $part;
                    }
                    unset($this->creator->options['is_union']);
                    $this->creator->union($parts);
                    $this->creator->qb_closed = true;
                    break;
                default:
                    break;
            }
        }

        return $this->creator->options['db_instance'] . $this->creator->getQuery($this->sql);
    }


    /**
     * union order is not handled as desired in PHPSQLParser, turn all to union and keep track of which was union all
     */
    private function unionHandler()
    {
        $sql = str_replace("\n", "", $this->sql);
        $sql = str_replace("\r", "", $sql);

        $sql_parts = explode(' ', $sql);
        $union_counter = 1;

        $this->temp = array();
        for ($i = 0; $i < count($sql_parts); $i++) {

            if (strtolower($sql_parts[$i]) == 'union') {
                $this->temp[$union_counter] = 0;
                if (isset($sql_parts[$i + 1])) {
                    if (strtolower($sql_parts[$i + 1]) == 'all') {
                        $this->temp[$union_counter] = 1;
                    }
                }
                $union_counter++;
            }

        }

        $this->sql = str_ireplace('union all', 'union', $this->sql);
    }
}

