<?php

namespace BjornVoesten\CipherSweet\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait WithAttributeEncryption
{
    /**
     * Encrypt the value for an attribute.
     *
     * @param string $attribute
     * @param string|int|boolean $value
     * @return array
     * @throws \Exception
     */
    public function encrypt(string $attribute, $value)
    {
        [$ciphertext, $indexes] = $result = app('ciphersweet')->encrypt(
            $this, $attribute, $value
        );

        $this->attributes[$attribute] = $ciphertext;

        foreach ($indexes as $value => $index) {
            $this->attributes[$index] = $value;
        }

        return $result;
    }

    /**
     * Encrypt the attribute.
     *
     * @param string $attribute
     * @return $this
     */
    public function decrypt(string $attribute)
    {
        return app('ciphersweet')->decrypt(
            $this, $attribute, $this->attributes[$attribute]
        );
    }

    /**
     * Add a where clause to the query for an encrypted column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @param $operator
     * @param $value
     * @param array $indexes
     * @return void
     * @throws \Exception
     */
    public function scopeWhereEncrypted(Builder $query, string $column, $operator, $value, array $indexes = []): void
    {
        $available = Arr::last(
            $this->encrypt($column, $value)
        );

        $indexes = empty($indexes)
            ? array_keys($available)
            : $indexes;

        $first = true;
        foreach ($indexes as $index) {
            $first
                ? $query->where($index, $operator, $available[$index])
                : $query->orWhere($index, $operator, $available[$index]);

            $first = false;
        }
    }

    /**
     * Add an or where clause to the query for an encrypted column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @param string $operator
     * @param $value
     * @param array $indexes
     * @return void
     * @throws \Exception
     */
    public function scopeOrWhereEncrypted(Builder $query, string $column, string $operator, $value, array $indexes = []): void
    {
        $available = Arr::last(
            $this->encrypt($column, $value)
        );

        $indexes = empty($indexes)
            ? array_keys($available)
            : $indexes;

        foreach ($indexes as $index) {
            $query->orWhere($index, $operator, $available[$index]);
        }
    }
}
