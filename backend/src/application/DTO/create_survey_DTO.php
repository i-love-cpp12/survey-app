<?php
declare(strict_types=1);

namespace app\application\DTO;

class CreateSurveyDTO
{
    function __construct(
        readonly public ?string $code,
        readonly public ?string $question,

        /** @var array<string> */
        readonly public ?array $options
    ){}
}