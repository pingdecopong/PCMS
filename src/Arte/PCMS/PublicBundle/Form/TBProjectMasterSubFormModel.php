<?php

namespace Arte\PCMS\PublicBundle\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Class TBProjectMasterSubFormModel
 *
 * @Assert\Callback(methods={"isValid"})
 */
class TBProjectMasterSubFormModel {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string　名称
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = "20"
     * )
     */
    private $Name;

    /**
     * @var integer　コスト
     *
     * @Assert\Type(type="numeric", message="半角数字を入力して下さい。")
     */
    private $Cost;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $SubForms;

    /**
     * @var integer no
     */
    private $sortNo;

    /**
     * @var bool グループフラグ
     */
    private $GroupFlag;

    function __construct()
    {
        $this->SubForms = new ArrayCollection();
    }

    public function isValid(ExecutionContextInterface $context)
    {
        if($this->GroupFlag == false && empty($this->Cost))
        {
            $context->addViolationAt('Cost', '空であってはなりません。', array(), null);
        }
    }

    /**
     * @param int $Cost
     */
    public function setCost($Cost)
    {
        $this->Cost = $Cost;
    }

    /**
     * @return int
     */
    public function getCost()
    {
        return $this->Cost;
    }

    /**
     * @param string $Name
     */
    public function setName($Name)
    {
        $this->Name = $Name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function addSubForm($Branch)
    {
        $this->SubForms->add($Branch);
    }

    public function removeSubForm($Branch)
    {
        $this->SubForms->removeElement($Branch);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubForms()
    {
        return $this->SubForms;
    }

    /**
     * @param boolean $GroupFlag
     */
    public function setGroupFlag($GroupFlag)
    {
        $this->GroupFlag = $GroupFlag;
    }

    /**
     * @return boolean
     */
    public function getGroupFlag()
    {
        return $this->GroupFlag;
    }

    /**
     * @param int $sortNo
     */
    public function setSortNo($sortNo)
    {
        $this->sortNo = $sortNo;
    }

    /**
     * @return int
     */
    public function getSortNo()
    {
        return $this->sortNo;
    }

}