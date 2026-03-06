<?php
declare(strict_types = 1);

namespace app\domain\entity;

use InvalidArgumentException;

class Option
{
    readonly public int $id;
    readonly public string $value;
    private int $voteCount;
    function __construct(int $id, string $value, int $voteCount = 0)
    {
        if($id < 0)
            throw new InvalidArgumentException("option vote id can not be negative");
        if(empty($value))
            throw new InvalidArgumentException("option value can not be empty");
        if($voteCount < 0)
            throw new InvalidArgumentException("option vote count can not be negative");

        $this->id = $id;
        $this->value = $value;
        $this->voteCount = $voteCount;
    }
    public function getVotesCount(): int
    {
        return $this->voteCount;
    }
    public function addVote(): void
    {
        $this->voteCount++;
    }
}