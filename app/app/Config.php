<?php

namespace App;

use App\Helpers\Arr;

class Config
{
    protected static self $instance;

    protected array $config = [];

    protected array $checkedConfigsForInclusion = [];

    public static function getInstance(): static
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function parseConfigName(string $key): ?string
    {
        return explode('.', $key, 2)[0] ?? null;
    }

    protected function includeConfig(?string $configName): void
    {
        if (!$configName ||
            key_exists($configName, $this->checkedConfigsForInclusion)
        ) {
            return;
        }

        $configPath = root_path() . '/config/' . $configName . '.php';

        if (file_exists($configPath)) {
            $this->config[$configName] = require $configPath;
        }

        $this->checkedConfigsForInclusion[$configPath] = $configName;
    }

    public function has(?string $key): bool
    {
        $this->includeConfig($this->parseConfigName($key));

        return Arr::has($key, $this->config);
    }

    public function get(?string $key, mixed $default = null): mixed
    {
        $this->includeConfig($this->parseConfigName($key));

        return Arr::get($key, $this->config, $default);
    }
}