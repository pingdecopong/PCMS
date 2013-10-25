<?php
namespace Arte\PCMS\BizlogicBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ExistsUsername extends Constraint
{
    public $message = 'ログインID：%string%は既に使用されています。他のログインIDを指定して下さい。';

    public function validatedBy()
    {
        return "ExistsUsernameValidator";
    }
}