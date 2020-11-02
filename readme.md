# CipherSweet for Laravel

A Laravel implementation of [Paragon Initiative Enterprises CipherSweet](https://ciphersweet.paragonie.com) searchable field level encryption.

Make sure you have some basic understanding of CipherSweet before continuing.

## Installation

Install the package using composer:
```
composer require bjorn-voesten/laravel-ciphersweet
```

The package will then automatically register itself.

#### Encryption key

In your `.env` file you should add:
```dotenv
CIPHERSWEET_KEY=
```
And then generate an encryption key:
```
php artisan ciphersweet:key
```

#### Config file

Publish the config file:
```
php artisan vendor:publish --tag=ciphersweet-config
```

## Usage

### Define encryption

Add the `BjornVoesten\CipherSweet\Concerns\WithAttributeEncryption` trait to your model <br> 
and add the `BjornVoesten\CipherSweet\Casts\Encrypted` cast to the attributes you want to encrypt.
```php
<?php

use Illuminate\Database\Eloquent\Model;
use BjornVoesten\CipherSweet\Concerns\WithAttributeEncryption;
use BjornVoesten\CipherSweet\Casts\Encrypted;

class User extends Model
{
    use WithAttributeEncryption;
    
    protected $fillable = [
        'social_security_number',
    ];

    protected $casts = [
        'social_security_number' => Encrypted::class,
    ];
}
```

By default, the index column name is generated using the name suffixed by `_index`. <br>
So `social_security_number` will use `social_security_number_index`.

#### Using custom indexes

Alternatively you can define multiple indexes per attribute and and define more options.

```php
<?php

use Illuminate\Database\Eloquent\Model;
use BjornVoesten\CipherSweet\Concerns\WithAttributeEncryption;
use BjornVoesten\CipherSweet\Casts\Encrypted;
use BjornVoesten\CipherSweet\Contracts\Attribute;
use BjornVoesten\CipherSweet\Contracts\Index;

class User extends Model
{
    // ...

    /**
     * Encrypt the social security number.
     *
     * @param \BjornVoesten\CipherSweet\Contracts\Attribute $attribute
     * @return void
     */
    public function encryptSocialSecurityNumberAttribute(Attribute $attribute): void
    {
        $attribute->index('social_security_number_last_four_index', function (Index $index) {
            $index
                ->bits(16)
                ->transform(new LastFourDigits());
        });
    }
}
```

### Encrypt and decrypt

Attributes will be automatically encrypted and decrypted when filling and retrieving attribute values.

**Note** Because the package uses Laravel casts it is not possible to combine the `Encrypted` cast and accessors/mutators.  

### Searching

**Note** When searching with the `equal to` operator models will be returned when the value is found in one of all available or defined indexes. When searching with the `not equal to` operator all models where the value is not found in any of the available or the defined indexes are returned. 

**Note**
Because of the limited search possibilities in CipherSweet only the `=` and `!=` operators are available when searching encrypted attributes. 

#### `whereEncrypted`

```php
 User::query()
    ->whereEncrypted('social_security_number', '=', '123-456-789')
    ->get();
```

#### `orWhereEncrypted`

```php
 User::query()
    ->whereEncrypted('social_security_number', '=', '123-456-789')
    ->orWhereEncrypted('social_security_number', '=', '456-123-789')
    ->get();
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email [security@bjornvoesten.com](mailto:security@bjornvoesten.com) instead of using the issue tracker.

## Testing

```
make test
```
