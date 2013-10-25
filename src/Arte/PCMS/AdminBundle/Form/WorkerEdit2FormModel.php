<?php

namespace Arte\PCMS\AdminBundle\Form;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class WorkerEdit2FormModel
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @Assert\NotBlank(groups={"WorkerEdit2Form"})
     */
    private $WorkerEditRoleForms;

    function __construct()
    {
        $this->WorkerEditRoleForms = new ArrayCollection();
    }
//    /**
//     * @param mixed $WorkerEditRoleForms
//     */
//    public function setWorkerEditRoleForms($WorkerEditRoleForms)
//    {
//        $this->WorkerEditRoleForms = $WorkerEditRoleForms;
//    }

    public function addWorkerEditRoleForm($Branch)
    {
        $this->WorkerEditRoleForms->add($Branch);
    }

    public function removeWorkerEditRoleForm($Branch)
    {
        $this->WorkerEditRoleForms->removeElement($Branch);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getWorkerEditRoleForms()
    {
        return $this->WorkerEditRoleForms;
    }

}