<?php
namespace Arte\PCMS\BizlogicBundle\Validator\Constraints;


use Arte\PCMS\BizlogicBundle\Lib\SystemUserManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Validator;

class ExistsUsernameValidator extends ConstraintValidator
{
    /**
     * @var \Arte\PCMS\BizlogicBundle\Lib\SystemUserManager
     */
    private $systemUserManager;

    function __construct(SystemUserManager $systemUserManager)
    {
        $this->systemUserManager = $systemUserManager;
    }

    public function validate($value, Constraint $constraint)
    {
        $result = $this->systemUserManager->existsUsername($value);
        if($result){
            $this->context->addViolation($constraint->message, array('%string%' => $value));
        }
    }

}