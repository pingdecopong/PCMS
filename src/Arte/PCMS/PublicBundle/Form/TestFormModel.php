<?php

namespace Arte\PCMS\PublicBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;

class TestFormModel {
    /**
     * @var string 名称
     * @Assert\NotBlank(groups={"TestForm"})
     */
    private $Name;

    /**
     * @param mixed $Name
     */
    public function setName($Name)
    {
        $this->Name = $Name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->Name;
    }

}