<?php
declare(strict_types=1);

namespace app\shared\exception;
use \Exception;

class SurveyNotFoundException extends Exception {}
class OptionNotFoundException extends Exception {}
class AlreadyVotedException extends Exception {}
class ValidationException extends Exception {}