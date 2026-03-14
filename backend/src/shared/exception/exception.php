<?php
declare(strict_types=1);

namespace app\shared\exception;
use \Exception;

class SurveyException extends Exception {}

class SurveyNotFoundException extends SurveyException {}
class OptionNotFoundException extends SurveyException {}
class AlreadyVotedException extends SurveyException {}
class MustNotBeNullException extends SurveyException {}
class ValidationException extends SurveyException {}