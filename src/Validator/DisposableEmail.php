<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class DisposableEmail
 * @Annotation
 */
class DisposableEmail extends Constraint
{
    public $message = "Don't use a disposable email";
}
