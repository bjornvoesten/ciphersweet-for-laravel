<?php

namespace BjornVoesten\CipherSweet\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Str;
use ParagonIE\ConstantTime\Hex;

class KeyGenerate extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ciphersweet:key
                    {--show : Display the key instead of modifying files}
                    {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and set a random encryption key';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $key = $this->generateRandomKey();

        if ($this->option('show')) {
            $this->line('<comment>' . $key . '</comment>');
            return;
        }

        // Next, we will replace the application key in the environment file so it is
        // automatically setup for this developer. This key gets generated using a
        // secure random byte generator and is later base64 encoded for storage.
        if (!$this->setKeyInEnvironmentFile($key)) {
            return;
        }

        $this->laravel['config']['ciphersweet.key'] = $key;

        $this->info('Application key set successfully.');
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     * @throws \Exception
     */
    protected function generateRandomKey()
    {
        return Hex::encode(random_bytes(32));
    }

    /**
     * Set the application key in the environment file.
     *
     * @param string $key
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key)
    {
        $currentKey = env('CIPHERSWEET_KEY');

        if (strlen($currentKey)) {
            return false;
        }

        if (!$this->confirmToProceed()) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($key);

        return true;
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param string $key
     * @return void
     */
    protected function writeNewEnvironmentFileWith($key)
    {
        $content = file_get_contents(
            $this->laravel->environmentFilePath()
        );

        if (!Str::contains($content, 'CIPHERSWEET_KEY')) {
            file_put_contents(
                $this->laravel->environmentFilePath(),
                'CIPHERSWEET_KEY=' . $key,
                FILE_APPEND
            );

            return;
        }

        file_put_contents(
            $this->laravel->environmentFilePath(),
            preg_replace(
                $this->keyReplacementPattern(),
                'CIPHERSWEET_KEY=' . $key,
                $content
            )
        );
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern()
    {
        $escaped = preg_quote('=' . $this->laravel['config']['ciphersweet.key'], '/');

        return "/^CIPHERSWEET_KEY{$escaped}/m";
    }
}
