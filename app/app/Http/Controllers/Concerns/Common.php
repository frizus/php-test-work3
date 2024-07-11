<?php

namespace App\Http\Controllers\Concerns;

trait Common
{
    protected function setAsXmlResponse(): void
    {
        header('Content-type: application/xml');
    }
}
