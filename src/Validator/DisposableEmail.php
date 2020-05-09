<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class DisposableEmail
 * @package App\Validator
 * @Annotation
 */
class DisposableEmail extends Constraint
{
    public $message = "Don't use a disposable email";
}
