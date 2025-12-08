<?php

use App\Core\Lang;

function __(string $key, ?string $default = null, array $replacements = []): string
{
    return Lang::get($key, $default, $replacements);
}
