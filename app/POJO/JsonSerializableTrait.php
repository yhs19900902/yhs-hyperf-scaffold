<?php

declare(strict_types=1);


namespace App\POJO;

trait JsonSerializableTrait
{
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function __toString(): string
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE);
    }
}