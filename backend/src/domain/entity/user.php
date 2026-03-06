<?php
declare(strict_types = 1);

namespace app\domain\entity;

use app\domain\entity\Token;

class User
{
    readonly public Token $token;
    public function __construct(Token $token)
    {
        $this->token = $token;
    }
}
