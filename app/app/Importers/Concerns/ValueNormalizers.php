<?php

namespace App\Importers\Concerns;

use App\Helpers\Phone;

trait ValueNormalizers
{
    protected function normalizerForInteger(mixed $value): string|int
    {
        return resolve_value(preg_replace('/\s/', '', $value));
    }

    protected function normalizerForPhones(mixed $value): string
    {
        $phones = explode(',', $value);
        foreach ($phones as $i => $phone) {
            $phones[$i] = Phone::format($phone);
        }

        return implode(', ', $phones);
    }
}
