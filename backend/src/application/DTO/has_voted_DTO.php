<?php
declare(strict_types=1);

namespace app\application\DTO;

class HasVotedDTO
{
    function __construct(
        readonly public string $surveyCode,
        readonly public string $unhashedToken
    ){}
}