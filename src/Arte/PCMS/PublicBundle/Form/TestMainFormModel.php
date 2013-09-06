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

class TestMainFormModel {

    private $id;
    /**
     * @var string 名称
     * @Assert\NotBlank()
     */
    private $Name;

    /**
     * @var string 名称2
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = "5"
     * )
     */
    private $Name2;

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

    /**
     * @param string $Name2
     */
    public function setName2($Name2)
    {
        $this->Name2 = $Name2;
    }

    /**
     * @return string
     */
    public function getName2()
    {
        return $this->Name2;
    }




}