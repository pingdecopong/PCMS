<?php
namespace Arte\PCMS\PublicBundle\Form;


use Symfony\Component\Validator\Constraints as Assert;

class TBProductionCostModel {

//    /**
//     * @var \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster
//     */
//    private $TBProjectMaster;
    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser
     * @Assert\NotBlank()
     */
    private $TBSystemUser;
    /**
     * @var \DateTime
     * @Assert\NotBlank()
     */
    private $WorkDate;
    /**
     * @var integer
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @Assert\Range(min = 1)
     */
    private $Cost;
    /**
     * @var string
     */
    private $Note;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster
     * @Assert\NotBlank()
     */
    private $TBProjectCost;

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
     * @param string $Note
     */
    public function setNote($Note)
    {
        $this->Note = $Note;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->Note;
    }

//    /**
//     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $TBProjectMaster
//     */
//    public function setTBProjectMaster($TBProjectMaster)
//    {
//        $this->TBProjectMaster = $TBProjectMaster;
//    }
//
//    /**
//     * @return \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster
//     */
//    public function getTBProjectMaster()
//    {
//        return $this->TBProjectMaster;
//    }

    /**
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $TBSystemUser
     */
    public function setTBSystemUser($TBSystemUser)
    {
        $this->TBSystemUser = $TBSystemUser;
    }

    /**
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser
     */
    public function getTBSystemUser()
    {
        return $this->TBSystemUser;
    }

    /**
     * @param \DateTime $WorkDate
     */
    public function setWorkDate($WorkDate)
    {
        $this->WorkDate = $WorkDate;
    }

    /**
     * @return \DateTime
     */
    public function getWorkDate()
    {
        return $this->WorkDate;
    }

    /**
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster $TBProjectCost
     */
    public function setTBProjectCost($TBProjectCost)
    {
        $this->TBProjectCost = $TBProjectCost;
    }

    /**
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster
     */
    public function getTBProjectCost()
    {
        return $this->TBProjectCost;
    }

}