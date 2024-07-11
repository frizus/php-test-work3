<?php

namespace App\Http\Requests;

abstract class BaseRequest
{
    protected array $data;

    public function __construct(
        protected ?array $query = null,
        protected ?array $post = null,
        protected ?array $files = null,
        $validate = true,
    )
    {
        $this->query ??= $_GET;
        $this->post ??= $_POST;
        $this->files ??= $_FILES;
        if ($validate) {
            $this->validate();
        }
    }

    public function input()
    {
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

    public function callValidator(): void
    {

    }
}