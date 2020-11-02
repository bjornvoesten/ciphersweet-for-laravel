<?php

namespace BjornVoesten\CipherSweet\Contracts;

use ParagonIE\CipherSweet\Contract\TransformationInterface;

interface Index
{
    /**
     * Set the index bits.
     *
     * @param int $bits
     * @return $this
     */
    public function bits(int $bits);

    /**
     * Set the index speed to fast.
     *
     * @param bool $fast
     * @return $this
     */
    public function fast(bool $fast = true);

    /**
     * Add a transformer.
     *
     * @param \ParagonIE\CipherSweet\Contract\TransformationInterface $transformer
     * @return $this
     */
    public function transform(TransformationInterface $transformer);
}
