<?php

namespace Tests\Unit;

use Exception;
use Tests\Concerns\CreatesUsers;
use Tests\Concerns\CreateUsersTable;
use Tests\Mocks\User;
use Tests\TestCase;

class QueryTest extends TestCase
{
    use CreateUsersTable;
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsersTable();
    }

    public function testCanQueryEncryptedAttributeWithWhereClause(): void
    {
        $userOne = $this->user('123-456-789');
        $userTwo = $this->user('789-456-123');

        // Assert success.
        /** @var \Illuminate\Database\Eloquent\Collection $keys */
        $keys = User::query()
            ->whereEncrypted('social_security_number', '=', '123-456-789')
            ->get()
            ->modelKeys();

        static::assertContains($userOne->id, $keys);
        static::assertNotContains($userTwo->id, $keys);

        // Assert success using provided index.
        /** @var \Illuminate\Database\Eloquent\Collection $keys */
        $keys = User::query()
            ->whereEncrypted('social_security_number', '=', '123-456-789', [
                'social_security_number_index',
            ])
            ->get()
            ->modelKeys();

        static::assertContains($userOne->id, $keys);
        static::assertNotContains($userTwo->id, $keys);

        // Assert undefined index exception.
        $this->expectException(Exception::class);

        User::query()
            ->whereEncrypted('social_security_number', '=', '123-456-789', [
                'non_existing_index',
            ])
            ->get();
    }

    public function testCanQueryNullableAttributes(): void
    {
        $userOne = $this->user('123-456-789');
        $userTwo = $this->user(null);

        // Assert success.
        /** @var \Illuminate\Database\Eloquent\Collection $keys */
        $keys = User::query()
            ->whereEncrypted('social_security_number', '=', '123-456-789')
            ->get()
            ->modelKeys();

        static::assertContains($userOne->id, $keys);
        static::assertNotContains($userTwo->id, $keys);
    }

    public function testCanQueryEncryptedAttributeWithOrWhereClause(): void
    {
        $userOne = $this->user('123-456-789');
        $userTwo = $this->user('456-123-789');
        $userThree = $this->user('789-456-123');

        // Assert success.
        /** @var \Illuminate\Database\Eloquent\Collection $keys */
        $keys = User::query()
            ->whereEncrypted('social_security_number', '=', '123-456-789')
            ->orWhereEncrypted('social_security_number', '=', '789-456-123')
            ->get()
            ->modelKeys();

        static::assertContains($userOne->id, $keys);
        static::assertNotContains($userTwo->id, $keys);
        static::assertContains($userThree->id, $keys);

        // Assert success using provided index.
        /** @var \Illuminate\Database\Eloquent\Collection $keys */
        $keys = User::query()
            ->whereEncrypted('social_security_number', '=', '123-456-789')
            ->orWhereEncrypted('social_security_number', '=', '789-456-123', [
                'social_security_number_index',
            ])
            ->get()
            ->modelKeys();

        static::assertContains($userOne->id, $keys);
        static::assertNotContains($userTwo->id, $keys);
        static::assertContains($userThree->id, $keys);

        // Assert undefined index exception.
        $this->expectException(Exception::class);

        User::query()
            ->orWhereEncrypted('social_security_number', '=', '123-456-789', [
                'non_existing_index',
            ])
            ->get();
    }

    public function testCanQueryEncryptedAttributeWithWhereInClause(): void
    {
        $userOne = $this->user('123-456-789');
        $userTwo = $this->user('789-456-123');
        $userThree = $this->user('456-789-123');

        // Assert success.
        /** @var \Illuminate\Database\Eloquent\Collection $keys */
        $keys = User::query()
            ->whereInEncrypted(
                'social_security_number',
                [
                    '123-456-789',
                    '789-456-123',
                ]
            )
            ->get()
            ->modelKeys();

        static::assertContains($userOne->id, $keys);
        static::assertContains($userTwo->id, $keys);
        static::assertNotContains($userThree->id, $keys);

        // Assert success using provided index.
        /** @var \Illuminate\Database\Eloquent\Collection $keys */
        $keys = User::query()
            ->whereInEncrypted(
                'social_security_number',
                [
                    '123-456-789',
                    '789-456-123',
                ],
                ['social_security_number_index']
            )
            ->get()
            ->modelKeys();

        static::assertContains($userOne->id, $keys);
        static::assertContains($userTwo->id, $keys);
        static::assertNotContains($userThree->id, $keys);

        $keys = User::query()
            ->whereInEncrypted(
                'social_security_number',
                ['789-456-123'],
                ['non_existing_index']
            )
            ->get()
            ->modelKeys();

        static::assertEmpty($keys);
    }

    public function testCanQueryEncryptedAttributeWithOrWhereInClause(): void
    {
        $userOne = $this->user('123-456-789');
        $userTwo = $this->user('789-456-123');
        $userThree = $this->user('456-789-123');

        // Assert success.
        /** @var \Illuminate\Database\Eloquent\Collection $keys */
        $keys = User::query()
            ->whereInEncrypted(
                'social_security_number',
                ['123-456-789']
            )
            ->orWhereInEncrypted(
                'social_security_number',
                ['789-456-123']
            )
            ->get()
            ->modelKeys();

        static::assertContains($userOne->id, $keys);
        static::assertContains($userTwo->id, $keys);
        static::assertNotContains($userThree->id, $keys);

        // Assert success using provided index.
        /** @var \Illuminate\Database\Eloquent\Collection $keys */
        $keys = User::query()
            ->whereInEncrypted(
                'social_security_number',
                ['123-456-789'],
                ['social_security_number_index']
            )
            ->orWhereInEncrypted(
                'social_security_number',
                ['789-456-123'],
                ['social_security_number_index']
            )
            ->get()
            ->modelKeys();

        static::assertContains($userOne->id, $keys);
        static::assertContains($userTwo->id, $keys);
        static::assertNotContains($userThree->id, $keys);

        $keys = User::query()
            ->whereInEncrypted(
                'social_security_number',
                ['789-456-123'],
                ['non_existing_index']
            )
            ->orWhereInEncrypted(
                'social_security_number',
                ['789-456-123'],
                ['non_existing_index']
            )
            ->get()
            ->modelKeys();

        static::assertEmpty($keys);
    }
}
