<?php
declare(strict_types = 1);

namespace app\application\DTO;

class VoteDTO
{
    function __construct(
        readonly public string $surveyCode,
        readonly public ?int $optionId,
        readonly public string $unhashedToken){}
}