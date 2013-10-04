<?php

namespace Arte\PCMS\PublicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TBProductionCost
 */
class TBProductionCost
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $ProjectCostMasterId;

    /**
     * @var integer
     */
    private $SystemUserId;

    /**
     * @var \DateTime
     */
    private $WorkDate;

    /**
     * @var integer
     */
    private $Cost;

    /**
     * @var string
     */
    private $Note;

    /**
     * @var boolean
     */
    private $DeleteFlag;

    /**
     * @var \Arte\PCMS\PublicBundle\Entity\TBProjectCostMaster
     */
    private $TBProjectCostMasterProjectCostMasterId;

    /**
     * @var \Arte\PCMS\PublicBundle\Entity\TBSystemUser
     */
    private $TBSystemUserSystemUserId;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ProjectCostMasterId
     *
     * @param integer $projectCostMasterId
     * @return TBProductionCost
     */
    public function setProjectCostMasterId($projectCostMasterId)
    {
        $this->ProjectCostMasterId = $projectCostMasterId;
    
        return $this;
    }

    /**
     * Get ProjectCostMasterId
     *
     * @return integer 
     */
    public function getProjectCostMasterId()
    {
        return $this->ProjectCostMasterId;
    }

    /**
     * Set SystemUserId
     *
     * @param integer $systemUserId
     * @return TBProductionCost
     */
    public function setSystemUserId($systemUserId)
    {
        $this->SystemUserId = $systemUserId;
    
        return $this;
    }

    /**
     * Get SystemUserId
     *
     * @return integer 
     */
    public function getSystemUserId()
    {
        return $this->SystemUserId;
    }

    /**
     * Set WorkDate
     *
     * @param \DateTime $workDate
     * @return TBProductionCost
     */
    public function setWorkDate($workDate)
    {
        $this->WorkDate = $workDate;
    
        return $this;
    }

    /**
     * Get WorkDate
     *
     * @return \DateTime 
     */
    public function getWorkDate()
    {
        return $this->WorkDate;
    }

    /**
     * Set Cost
     *
     * @param integer $cost
     * @return TBProductionCost
     */
    public function setCost($cost)
    {
        $this->Cost = $cost;
    
        return $this;
    }

    /**
     * Get Cost
     *
     * @return integer 
     */
    public function getCost()
    {
        return $this->Cost;
    }

    /**
     * Set Note
     *
     * @param string $note
     * @return TBProductionCost
     */
    public function setNote($note)
    {
        $this->Note = $note;
    
        return $this;
    }

    /**
     * Get Note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->Note;
    }

    /**
     * Set DeleteFlag
     *
     * @param boolean $deleteFlag
     * @return TBProductionCost
     */
    public function setDeleteFlag($deleteFlag)
    {
        $this->DeleteFlag = $deleteFlag;
    
        return $this;
    }

    /**
     * Get DeleteFlag
     *
     * @return boolean 
     */
    public function getDeleteFlag()
    {
        return $this->DeleteFlag;
    }

    /**
     * Set TBProjectCostMasterProjectCostMasterId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\TBProjectCostMaster $tBProjectCostMasterProjectCostMasterId
     * @return TBProductionCost
     */
    public function setTBProjectCostMasterProjectCostMasterId(\Arte\PCMS\PublicBundle\Entity\TBProjectCostMaster $tBProjectCostMasterProjectCostMasterId = null)
    {
        $this->TBProjectCostMasterProjectCostMasterId = $tBProjectCostMasterProjectCostMasterId;
    
        return $this;
    }

    /**
     * Get TBProjectCostMasterProjectCostMasterId
     *
     * @return \Arte\PCMS\PublicBundle\Entity\TBProjectCostMaster 
     */
    public function getTBProjectCostMasterProjectCostMasterId()
    {
        return $this->TBProjectCostMasterProjectCostMasterId;
    }

    /**
     * Set TBSystemUserSystemUserId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\TBSystemUser $tBSystemUserSystemUserId
     * @return TBProductionCost
     */
    public function setTBSystemUserSystemUserId(\Arte\PCMS\PublicBundle\Entity\TBSystemUser $tBSystemUserSystemUserId = null)
    {
        $this->TBSystemUserSystemUserId = $tBSystemUserSystemUserId;
    
        return $this;
    }

    /**
     * Get TBSystemUserSystemUserId
     *
     * @return \Arte\PCMS\PublicBundle\Entity\TBSystemUser 
     */
    public function getTBSystemUserSystemUserId()
    {
        return $this->TBSystemUserSystemUserId;
    }
}
