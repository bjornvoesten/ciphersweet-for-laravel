<?php

return [

    /*
     * The key should be a string with a size of 32 bytes and is
     * used to generate keys when encrypting attributes. Please set
     * the key before deploying the application and do not change
     * the key when you already have attributes encrypted!
     */
    'key' => env('CIPHERSWEET_KEY'),

    /*
     * You may specify which encryption algorithm has to be used
     * to encrypt all attributes.
     *
     * Supported: "modern", "fips"
     */
    'crypto' => env('CIPHERSWEET_CRYPTO', 'modern'),

];
