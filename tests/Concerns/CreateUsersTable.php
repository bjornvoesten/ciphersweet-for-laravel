<?php

namespace Tests\Concerns;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CreateUsersTable
{
    protected function createUsersTable(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('social_security_number')->nullable();
            $table->string('social_security_number_index')->nullable();
            $table->string('custom_index')->nullable();
            $table->timestamps();
        });
    }
}
