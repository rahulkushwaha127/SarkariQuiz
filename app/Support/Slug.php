<?php

namespace App\Support;

use Illuminate\Support\Str;

class Slug
{
    public static function make(string $name): string
    {
        $slug = Str::slug($name);
        return $slug !== '' ? $slug : Str::random(8);
    }
}

