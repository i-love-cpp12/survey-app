<?php
declare(strict_types = 1);
require_once("option.php");

namespace app\domain\entity;
use InvalidArgumentException;
use app\domain\entity\Option;
use app\domain\value_object\User;

class Survey
{
    public static int $minCodeSize = 3;
    public static int $maxCodeSize = 20;

    readonly public int $id;
    readonly public string $question;
    readonly public string $code;
    public bool $isActive;

    /** @var Option[] */
    private array $options;

    function __construct(int $id, string $question, string $code, array $options, bool $isActive = true)
    {
        if($id < 0)
            throw new InvalidArgumentException("survey vote id can not be negative");
        if(empty($question))
            throw new InvalidArgumentException("Survey question can not be empty");
        if(empty($question))
            throw new InvalidArgumentException("Survey question can not be empty");
        if(strlen($code) < self::$minCodeSize || strlen($code) > self::$maxCodeSize)
            throw new InvalidArgumentException("Survey code must be (" . self::$minCodeSize . " - " . self::$maxCodeSize . ") long");
        if(empty($options))
            throw new InvalidArgumentException("Survey has to have options");
        foreach($options as $option)
        {
            if(!$option instanceof Option)
                throw new InvalidArgumentException("All options given has to be instance of " . Option::class . " class");
        }

        $this->id = $id;
        $this->question = $question;
        $this->code = strtoupper($code);
        $this->isActive = $isActive;
        $this->options = $options;
    }

    /** @return Option[] */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function findOption(int $optionId): ?Option
    {
        foreach($this->options as $option)
        {
            if($option->id === $optionId)
                return $option;
        }
        return null;
    }

    public function addOption(Option $option): void
    {
        $this->options[] = $option;
    }

    public function vote(int $optionId, User $user): bool
    {
        $optionIndex = null;

        foreach($this->options as $i => $option)
        {
            if($option->id === $optionId)
                $optionIndex = $i;
        }

        if(!$optionIndex) return false;

        return $this->options[$optionIndex]->vote($user);
    }
}
