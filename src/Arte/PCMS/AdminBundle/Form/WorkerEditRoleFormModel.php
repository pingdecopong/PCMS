<?php

namespace Arte\PCMS\AdminBundle\Form;


use Symfony\Component\Validator\Constraints as Assert;

class WorkerEditRoleFormModel
{
    /**
     * @var integer
     * @Assert\NotBlank(groups={"WorkerEdit2Form"})
     */
    private $Id;
    /**
     * @var
     * @Assert\NotBlank(groups={"WorkerEdit2Form"})
     */
    private $Role;

    /**
     * @param int $Id
     */
    public function setId($Id)
    {
        $this->Id = $Id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @param mixed $Role
     */
    public function setRole($Role)
    {
        $this->Role = $Role;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->Role;
    }

}