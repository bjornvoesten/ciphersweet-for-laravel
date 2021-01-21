<?php

namespace BjornVoesten\CipherSweet\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Encrypted implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model|\BjornVoesten\CipherSweet\Concerns\WithAttributeEncryption $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return string|null
     * @throws \Exception
     */
    public function get($model, string $key, $value, array $attributes): ?string
    {
        return $model->decrypt($key);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model|\BjornVoesten\CipherSweet\Concerns\WithAttributeEncryption $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return array
     * @throws \Exception
     */
    public function set($model, string $key, $value, array $attributes)
    {
        [$ciphertext, $indexes] = $model->encrypt($key, $value);

        return array_merge(
            [$key => $ciphertext],
            $indexes
        );
    }
}
