<?php

namespace BjornVoesten\CipherSweet;

use BjornVoesten\CipherSweet\Contracts\Index;
use BjornVoesten\CipherSweet\Exceptions\CipherSweetException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ParagonIE\CipherSweet\Backend\FIPSCrypto;
use ParagonIE\CipherSweet\Backend\ModernCrypto;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\CipherSweet;
use ParagonIE\CipherSweet\EncryptedField;
use ParagonIE\CipherSweet\KeyProvider\StringProvider;

class CipherSweetService
{
    /**
     * @var \ParagonIE\CipherSweet\CipherSweet
     */
    protected $engine;

    /**
     * @var \ParagonIE\CipherSweet\Contract\KeyProviderInterface|\ParagonIE\CipherSweet\KeyProvider\StringProvider
     */
    protected $provider;

    /**
     * Create a new encryption service.
     *
     * @return void
     * @throws \BjornVoesten\CipherSweet\Exceptions\CipherSweetException
     * @throws \ParagonIE\CipherSweet\Exception\CryptoOperationException
     */
    public function __construct()
    {
        $this->provider = $this->provider();

        $this->engine = new CipherSweet(
            $this->provider, $this->crypto()
        );
    }

    /**
     * Create the string provider.
     *
     * @return \ParagonIE\CipherSweet\Contract\KeyProviderInterface
     * @throws \BjornVoesten\CipherSweet\Exceptions\CipherSweetException
     * @throws \ParagonIE\CipherSweet\Exception\CryptoOperationException
     */
    private function provider()
    {
        $key = config('ciphersweet.key');

        if (empty($key)) {
            throw new CipherSweetException(
                'No encryption key provided'
            );
        }

        return new StringProvider($key);
    }

    /**
     * Create the crypto provider instance.
     *
     * @return \ParagonIE\CipherSweet\Contract\BackendInterface
     * @throws \BjornVoesten\CipherSweet\Exceptions\CipherSweetException
     */
    protected function crypto()
    {
        switch (config('ciphersweet.crypto')) {
            case 'fips':
                return new FIPSCrypto();
            case 'modern':
                return new ModernCrypto();
            default:
                throw new CipherSweetException(
                    'Unsupported crypto'
                );
        }
    }

    /**
     * Get an encrypted field instance for the given attribute.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $attribute
     * @return \ParagonIE\CipherSweet\EncryptedField
     * @throws \ParagonIE\CipherSweet\Exception\BlindIndexNameCollisionException
     * @throws \ParagonIE\CipherSweet\Exception\CryptoOperationException
     */
    protected function field(Model $model, string $attribute)
    {
        $attribute = new Attribute($attribute);

        // Check whether a method for custom encryption exists and get the
        // custom indexes from the method, or set the default index.
        $method = 'encrypt' . Str::studly($attribute->column) . 'Attribute';

        if (method_exists($model, $method)) {
            $model->{$method}($attribute);
        } else {
            $attribute->index($attribute->column . '_index');
        }

        // Create the encrypted field instance.
        $field = new EncryptedField(
            $this->engine,
            $table = $model->getTable(),
            $attribute->column,
        );

        // Map and add the indexes to the encrypted
        // field instance.
        collect($attribute->indexes)
            ->map(function (Index $index) {
                return $index = new BlindIndex(
                    $index->column,
                    $index->transformers,
                    $index->bits,
                    $index->fast,
                );
            })
            ->each(
                fn($index) => $field->addBlindIndex($index)
            );

        return $field;
    }

    /**
     * Encrypt a model attribute.
     *
     * @param \Illuminate\Database\Eloquent\Model|\BjornVoesten\CipherSweet\Concerns\WithAttributeEncryption $model
     * @param string $attribute
     * @param string|int|boolean $value
     * @return array
     * @throws \ParagonIE\CipherSweet\Exception\BlindIndexNameCollisionException
     * @throws \ParagonIE\CipherSweet\Exception\BlindIndexNotFoundException
     * @throws \ParagonIE\CipherSweet\Exception\CryptoOperationException
     * @throws \SodiumException
     */
    public function encrypt(Model $model, string $attribute, $value)
    {
        return $this
            ->field($model, $attribute)
            ->prepareForStorage($value);
    }

    /**
     * Decrypt a model attribute.
     *
     * @param \Illuminate\Database\Eloquent\Model|\BjornVoesten\CipherSweet\Concerns\WithAttributeEncryption $model
     * @param string $attribute
     * @param string|int|boolean $value
     * @return string
     * @throws \ParagonIE\CipherSweet\Exception\BlindIndexNameCollisionException
     * @throws \ParagonIE\CipherSweet\Exception\CryptoOperationException
     */
    public function decrypt(Model $model, string $attribute, $value)
    {
        return $this
            ->field($model, $attribute)
            ->decryptValue($value);
    }
}
