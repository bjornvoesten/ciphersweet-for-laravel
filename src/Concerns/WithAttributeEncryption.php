<?php

namespace BjornVoesten\CipherSweet\Concerns;

use Illuminate\Database\Eloquent\Builder;

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
     */
    public function encrypt(string $attribute, $value): array
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
     * @return string|null
     */
    public function decrypt(string $attribute): ?string
    {
        return app('ciphersweet')->decrypt(
            $this, $attribute, $this->attributes[$attribute] ?? null
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
     * @param string $boolean
     * @return void
     */
    public function scopeWhereEncrypted(Builder $query, string $column, $operator, $value, array $indexes = [], $boolean = 'and'): void
    {
        /** @var array $available */
        $available = $this->encrypt($column, $value)[1];

        $indexes = empty($indexes) ? array_keys($available) : $indexes;

        $method = $boolean === 'or'
            ? 'orWhere'
            : 'where';

        $query->{$method}(function (Builder $query) use ($available, $operator, $indexes) {
            foreach ($indexes as $key => $index) {
                $query->where($index, $operator, $available[$index]);
            }
        });
    }

    /**
     * Add an or where clause to the query for an encrypted column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param array $indexes
     * @param string $boolean
     * @return void
     */
    public function scopeOrWhereEncrypted(
        Builder $query, string $column, string $operator, $value,
        array $indexes = [], $boolean = 'or'
    ): void
    {
        $this->scopeWhereEncrypted(
            $query, $column, $operator, $value, $indexes, $boolean
        );
    }

    /**
     * Add a where in clause to the query for an encrypted column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @param array $values
     * @param array $indexes
     * @param string $boolean
     * @return void
     * @throws \Exception
     */
    public function scopeWhereInEncrypted(
        Builder $query, string $column, array $values, array $indexes = [],
        $boolean = 'and'
    ): void
    {
        $values = array_map(function (string $value) use ($column) {
            return $this->encrypt($column, $value)[1];
        }, $values);

        $available = array_keys($values[0]);

        $indexes = empty($indexes) ? $available : $indexes;

        $method = $boolean === 'or'
            ? 'orWhere'
            : 'where';

        $query->{$method}(function (Builder $query) use ($values, $indexes) {
            foreach ($indexes as $key => $index) {
                (bool) $key
                    ? $query->orWhereIn($index, $values)
                    : $query->whereIn($index, $values);
            }
        });
    }

    /**
     * Add a or where in clause to the query for an encrypted column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @param array $values
     * @param array $indexes
     * @param string $boolean
     * @return void
     * @throws \Exception
     */
    public function scopeOrWhereInEncrypted(
        Builder $query, string $column, array $values, array $indexes = [],
        $boolean = 'or'
    ): void
    {
        $this->scopeWhereInEncrypted(
            $query, $column, $values, $indexes, $boolean
        );
    }
}
