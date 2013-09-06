<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fhirashima
 * Date: 13/08/15
 * Time: 17:47
 * To change this template use File | Settings | File Templates.
 */

namespace Arte\PCMS\PublicBundle\Form;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class TestSubFormModel {

    private $id;

    /**
     * @var string name
     * @Assert\NotBlank()
     */
    private $Name;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $subForms;

    function __construct()
    {
        $this->subForms = new ArrayCollection();
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->Name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $subForms
     */
//    public function setSubForms(ArrayCollection $subForms)
//    {
//        $this->subForms = $subForms;
//    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubForms()
    {
        return $this->subForms;
    }

    public function addSubForm($subForm)
    {
        $this->subForms->add($subForm);
    }

    public function removeSubForm($subForm)
    {
        $this->subForms->removeElement($subForm);
    }


}