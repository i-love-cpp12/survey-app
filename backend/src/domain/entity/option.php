<?php
declare(strict_types = 1);
require_once("user.php");
namespace app\domain\entity;

use InvalidArgumentException;
use app\domain\value_object\User;

class Option
{
    readonly public int $id;
    readonly public string $value;
    /** @var User[] */
    private array $votes;
    /** @param User[] $votes */
    function __construct(int $id, string $value, array $votes = [])
    {
        if($id < 0)
            throw new InvalidArgumentException("option vote id can not be negative");
        if(empty($value))
            throw new InvalidArgumentException("option value can not be empty");

        $this->id = $id;
        $this->value = $value;
        foreach($votes as $vote)
        {
            if(!$vote instanceof User)
                throw new InvalidArgumentException("all option votes has to be type of ". User::class);   
        }
        $this->votes = $votes;
    }
    public function getVotesCount(): int
    {
        return count($this->votes);
    }
    public function vote(User $user): bool
    {
        if($this->hasVoted($user)) return false;
        $this->votes[] = $user;
        return true;
    }
    public function hasVoted(User $user): bool
    {
        foreach($this->votes as $vote)
        {
            if($vote->token->value === $user->token->value)
                return true;
        }
        return false;
    }
}