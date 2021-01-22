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
        return function (string $name, ?array $indexes = null, ?string $after = null, bool $nullable = false): void {
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
                $addedColumn = $this->string($column)->nullable($nullable);

                if($after) {
                    $addedColumn->after($after);

                    $after = $column;
                }
            }

            $this->index($columns);
        };
    }

    public function nullableEncrypted(): Closure
    {
        return function (string $name, ?array $indexes = null, ?string $after = null): void {
            $this->encrypted($name, $indexes, $after, true);
        };
    }
}
