<?php
declare(strict_types = 1);

namespace app\domain\entity;

require_once(__DIR__ . "/option.php");
require_once(__DIR__ . "/../../shared/exception/exception.php");


use app\domain\entity\Option;

use app\shared\exception\OptionNotFoundException;
use LogicException;
use InvalidArgumentException;

class Survey
{
    public static int $minCodeSize = 3;
    public static int $maxCodeSize = 20;
    public static $allowedCodeChars = "1234567890QWERTYUIOPASDFGHJKLZXCVBNM";

    private ?int $id;
    readonly public string $question;
    readonly public string $code;
    public bool $isActive;

    /** @var Option[] */
    private array $options;

    function __construct(?int $id, string $question, string $code, array $options, bool $isActive = true)
    {
        if(empty($question))
            throw new InvalidArgumentException("Survey question can not be empty");
        if(empty($question))
            throw new InvalidArgumentException("Survey question can not be empty");
        if(!self::validateCode($code))
            throw new InvalidArgumentException("Survey code must be (" . self::$minCodeSize . " - " . self::$maxCodeSize . ") long and only contain characters from this list(".self::$allowedCodeChars."), but $code were given");
        if(empty($options))
            throw new InvalidArgumentException("Survey has to have options");
        foreach($options as $option)
        {
            if(!$option instanceof Option)
                throw new InvalidArgumentException("All options given has to be instance of " . Option::class . " class");
        }
        $this->id = null;
        $this->setId($id);

        $this->question = $question;
        $this->code = strtoupper($code);
        $this->isActive = $isActive;
        $this->options = $options;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        if($this->id)
            throw new LogicException("Id is already set");
        if($id < 0)
            throw new InvalidArgumentException("Id can not be negative");
        $this->id = $id;
    }
    public static function validateCodeLenght(int $length): bool
    {
        return $length >= self::$minCodeSize && $length <= self::$maxCodeSize;
    }
    public static function validateCode(string $code): bool
    {
        $code = strtoupper($code);
        $lenght = strlen($code);
        if(!self::validateCodeLenght($lenght))
            return false;
            
        
        for($i = 0; $i < $lenght; ++$i)
        {
            $char = $code[$i];
            if(!str_contains(self::$allowedCodeChars, $char))
                return false;
        }

        return true;
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
    public function vote(int $optionId): void
    {
        $optionIndex = null;

        foreach($this->options as $i => $option)
        {
            if($option->id === $optionId)
            {
                $optionIndex = $i;
                break;
            }
        }

        if($optionIndex === null)
            throw new OptionNotFoundException("Option id not found");

        $this->options[$optionIndex]->addVote();
    }
}
