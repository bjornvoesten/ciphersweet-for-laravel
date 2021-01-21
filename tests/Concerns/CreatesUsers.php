<?php

namespace Tests\Concerns;

use Tests\Mocks\User;

trait CreatesUsers
{
    /**
     * Create a new user instance.
     *
     * @param string|null $socialSecurityNumber
     * @return \Tests\Mocks\User|\Illuminate\Database\Eloquent\Model
     */
    protected function user(?string $socialSecurityNumber): User
    {
        return User::query()->create([
            'social_security_number' => $socialSecurityNumber,
        ]);
    }
}
