<?php

namespace BjornVoesten\CipherSweet\Macros;

use Closure;

/**
 * @mixin \Illuminate\Database\Schema\Blueprint
 */
class Blueprint
{
    public function encrypted(): Closure
    {
        return function (string $name, ?array $indexes = null): void {
            $columns = empty($indexes)
                ? [
                    $name,
                    "{$name}_index",
                ]
                : array_merge(
                    [$name],
                    $indexes
                );

            foreach ($columns as $column) {
                $this->string($column);
            }

            $this->index($columns);
        };
    }

    public function nullableEncrypted(): Closure
    {
        return function (string $name, ?array $indexes = null): void {
            $columns = empty($indexes)
                ? [
                    $name,
                    "{$name}_index",
                ]
                : array_merge(
                    [$name],
                    $indexes
                );

            foreach ($columns as $column) {
                $this->string($column)->nullable();
            }

            $this->index($columns);
        };
    }
}
