<?php

namespace Tests\Mocks;

use BjornVoesten\CipherSweet\Casts\Encrypted;
use BjornVoesten\CipherSweet\Concerns\WithAttributeEncryption;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use WithAttributeEncryption;

    protected $table = 'users';

    protected $fillable = [
        'social_security_number',
    ];

    protected $casts = [
        'social_security_number' => Encrypted::class,
    ];
}
