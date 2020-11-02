<?php

namespace BjornVoesten\CipherSweet;

use ParagonIE\CipherSweet\Contract\TransformationInterface;

class Index implements Contracts\Index
{
    /**
     * @var string
     */
    public $column;

    /**
     * @var int
     */
    public $bits = 32;

    /**
     * @var bool
     */
    public $fast = true;

    /**
     * @var array
     */
    public $transformers = [];

    public function __construct(string $column)
    {
        $this->column = $column;
    }

    /**
     * Set the index bits.
     *
     * @param int $bits
     * @return $this
     */
    public function bits(int $bits)
    {
        $this->bits = $bits;

        return $this;
    }

    /**
     * Set the index speed to fast.
     *
     * @param bool $fast
     * @return $this
     */
    public function fast(bool $fast = true)
    {
        $this->fast = $fast;

        return $this;
    }

    /**
     * Add a transformer.
     *
     * @param \ParagonIE\CipherSweet\Contract\TransformationInterface $transformer
     * @return $this
     */
    public function transform(TransformationInterface $transformer)
    {
        $this->transformers[] = $transformer;

        return $this;
    }
}
