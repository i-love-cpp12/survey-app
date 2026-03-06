<?php
declare(strict_types = 1);

namespace app\domain\value_object;

use app\domain\value_object\Token;

class User
{
    readonly public Token $token;
    public function __construct(Token $token)
    {
        $this->token = $token;
    }
}
