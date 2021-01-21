<?php

namespace Tests\Unit;

use BjornVoesten\CipherSweet\Contracts\Attribute;
use BjornVoesten\CipherSweet\Contracts\Index;
use Tests\Concerns\CreatesUsers;
use Tests\Concerns\CreateUsersTable;
use Tests\Mocks\User;
use Tests\TestCase;

class EncryptionTest extends TestCase
{
    use CreateUsersTable;
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsersTable();
    }

    public function testAttributesAreEncryptedWhenMade(): void
    {
        $user = new User([
            'social_security_number' => '123-456-789',
        ]);

        static::assertSame(
            '123-456-789',
            $user->social_security_number
        );

        static::assertNotEmpty(
            $user->social_security_number_index
        );
    }

    public function testAttributesAreEncryptedWhenCreated(): void
    {
        $user = $this->user('123-456-789');

        static::assertNotSame(
            '123-456-789',
            $user->getRawOriginal('social_security_number')
        );

        static::assertNotEmpty(
            $user->social_security_number_index
        );

        $this->assertDatabaseHasFor(User::class, [
            'social_security_number' => $user->getRawOriginal('social_security_number'),
        ]);

        $this->assertDatabaseMissingFor(User::class, [
            'social_security_number' => $user->getAttribute('social_security_number'),
        ]);
    }

    public function testAttributesAreEncryptedWithCustomIndexes(): void
    {
        $user = new class extends User {
            public function encryptSocialSecurityNumberAttribute(Attribute $attribute): void
            {
                $attribute->index('custom_index', function (Index $index) {
                    $index
                        ->bits(32)
                        ->fast();
                });
            }
        };

        $user
            ->fill([
                'social_security_number' => '123-456-789',
            ])
            ->save();

        static::assertNotSame(
            '123-456-789',
            $user->getRawOriginal('social_security_number')
        );

        static::assertNotEmpty(
            $user->getAttribute('custom_index')
        );

        $this->assertDatabaseHasFor(User::class, [
            'social_security_number' => $user->getRawOriginal('social_security_number'),
        ]);

        $this->assertDatabaseMissingFor(User::class, [
            'social_security_number' => $user->getAttribute('social_security_number'),
        ]);
    }

    public function testAttributesAreDecryptedWhenAccessed(): void
    {
        $user = $this->user('123-456-789');

        static::assertSame(
            '123-456-789',
            $user->getAttribute('social_security_number')
        );
    }

    public function testAttributesCanBeMadeNull(): void
    {
        $user = $this->user('123-456-789');

        static::assertSame(
            '123-456-789',
            $user->social_security_number
        );

        $user->social_security_number = null;

        static::assertNull(
            $user->social_security_number
        );

        static::assertNull(
            $user->social_security_number_index
        );
    }
}
