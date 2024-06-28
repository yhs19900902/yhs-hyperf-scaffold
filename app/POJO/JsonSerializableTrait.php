<?php

declare(strict_types=1);


namespace App\POJO;

trait JsonSerializableTrait
{
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}