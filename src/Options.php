<?php

namespace RexShijaku\SQLToCIBuilder;

class Options
{
    private $options;

    private $min_version = 3;

    private $defaults = array(
        'civ' => 3,
        'db_instance' => '$this->db',
        'use_from' => false,
        'group' => true,
        'single_command' => true
    );

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function set()
    {
        if (!is_array($this->options))
            $this->options = array();

        foreach ($this->defaults as $k => $v)
            if (!key_exists($k, $this->options))
                $this->options[$k] = $v;
            else {
                // set type
            }

        if ($this->options['civ'] >= 4)
            array_push($this->aggregate_fn, "count"); // supported in c.v 4
        $this->options['settings']['agg'] = $this->aggregate_fn;
        return $this->options;
    }

    public function validate()
    {
        if ($this->options['civ'] < $this->min_version) {
            throw new \Exception('Invalid CI Version. Must be > ' . $this->min_version . '! (' . $this->options['civ'] . ' provided.' . ')');
        }
    }

    public function get()
    {
        return $this->options;
    }

    private $aggregate_fn = array('sum', 'min', 'max', 'avg', 'sum');

}