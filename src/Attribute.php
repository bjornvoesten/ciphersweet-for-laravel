<?php

namespace BjornVoesten\CipherSweet;

class Attribute implements Contracts\Attribute
{
    /**
     * @var string
     */
    public $column;

    /**
     * @var array
     */
    public $indexes = [];

    public function __construct(string $column)
    {
        $this->column = $column;
    }

    /**
     * Add a new attribute index.
     *
     * @param string $column
     * @param callable|null $callback
     * @return $this
     */
    public function index(string $column, callable $callback = null)
    {
        $index = new Index($column);

        if ($callback) $callback($index);

        $this->indexes[] = $index;

        return $this;
    }
}
