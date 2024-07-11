<?php

namespace App\Http\Requests;

abstract class BaseRequest
{
    protected array $data = [];

    public function __construct(
        protected ?array $query = null,
        protected ?array $post = null,
        protected ?array $files = null
    )
    {
        $this->query ??= $_GET;
        $this->post ??= $_POST;
        $this->files ??= $_FILES;
    }

    public function input($key = null, $default = null)
    {
        if (func_num_args() > 0) {
            return key_exists($key, $this->data) ? $this->data[$key] : $default;
        }

        return $this->data;
    }

    protected function getData(): array
    {
        return $this->post + $this->query + $this->files;
    }

    public function validate(): void
    {
        $this->data = $this->getData();
        $this->callValidator();
    }

    protected function callValidator(): void
    {

    }
}