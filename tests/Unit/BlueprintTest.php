<?php

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BlueprintTest extends TestCase
{
    public function testBlueprintEncryptedColumn(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->encrypted('social_security_number');
        });

        $this->assertTrue(
            Schema::hasColumns('users', [
                'social_security_number',
                'social_security_number_index',
            ])
        );
    }

    public function testBlueprintEncryptedColumnWithCustomIndexes(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->encrypted('social_security_number', [
                'social_security_number_index',
                'custom_index',
            ]);
        });

        $this->assertTrue(
            Schema::hasColumns('users', [
                'social_security_number',
                'social_security_number_index',
                'custom_index',
            ])
        );
    }

    public function testBlueprintNullableEncryptedColumn(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->nullableEncrypted('social_security_number');
        });

        $this->assertTrue(
            Schema::hasColumns('users', [
                'social_security_number',
                'social_security_number_index',
            ])
        );
    }

    public function testBlueprintNullableEncryptedColumnWithCustomIndexes(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->nullableEncrypted('social_security_number', [
                'social_security_number_index',
                'custom_index',
            ]);
        });

        $this->assertTrue(
            Schema::hasColumns('users', [
                'social_security_number',
                'social_security_number_index',
                'custom_index',
            ])
        );
    }
}
