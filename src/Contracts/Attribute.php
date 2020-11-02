<?php

namespace BjornVoesten\CipherSweet\Contracts;

interface Attribute
{
    /**
     * Add a new attribute index.
     *
     * @param string $column
     * @param callable|null $callback
     * @return $this
     */
    public function index(string $column, callable $callback = null);
}
