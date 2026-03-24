<?php
declare(strict_types=1);

namespace app\shared\exception;
use \Exception;
use Throwable;

class SurveyException extends Exception {}

class AlreadyExistsException extends SurveyException {
    function __construct(string $message = "", Throwable|null $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
class NotFoundException extends SurveyException {
    function __construct(string $message = "", Throwable|null $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }
}
class AlreadyVotedException extends SurveyException {
    function __construct(string $message = "", Throwable|null $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
class ValidationException extends SurveyException {
    function __construct(string $message = "", Throwable|null $previous = null)
    {
        parent::__construct($message, 406, $previous);
    }
}