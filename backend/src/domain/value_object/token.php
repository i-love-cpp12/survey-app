<?php
declare(strict_types = 1);

namespace app\domain\value_object;

use InvalidArgumentException;

class Token
{
    readonly public string $value;
    function __construct(string $inputString)
    {
        if(empty($inputString))
            throw new InvalidArgumentException("input string can not be empty");
        $this->value = hash("sha256", $inputString);
    }
}