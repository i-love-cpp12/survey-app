<?php
declare(strict_types=1);

namespace app\domain\value_object;

require_once(__DIR__ . "/user.php");


use app\domain\value_object\User;
use InvalidArgumentException;
use LogicException;

class Vote
{
    private ?int $id;
    readonly public int $surveyId;
    readonly public int $optionId;
    readonly public User $user;

    function __construct(?int $id, int $surveyId, int $optionId, User $user)
    {
        $this->id = null;
        $this->setId($id);
        
        if($surveyId < 0)
            throw new InvalidArgumentException("Surevy id can not be negative");
        if($optionId < 0)
            throw new InvalidArgumentException("Option id can not be negative");
        
        
        $this->surveyId = $surveyId;
        $this->optionId = $optionId;
        $this->user = $user;
    }
    function getId(): ?int
    {
        return $this->id;
    }
    function setId($id): void
    {
        if($this->id)
            throw new LogicException("Vote id is already set");
        if($id < 0)
            throw new InvalidArgumentException("Vote id can not be negative");
        $this->id = $id;
    }
}