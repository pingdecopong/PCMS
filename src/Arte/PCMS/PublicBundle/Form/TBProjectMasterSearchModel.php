<?php

namespace Arte\PCMS\PublicBundle\Form;

use Doctrine\ORM\Mapping as ORM;

/**
 * TBProjectMasterSearchModel
 */
class TBProjectMasterSearchModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $Name;

    /**
     * @var integer
     */
    private $Status;

    /**
     * @var string
     */
    private $Explanation;

    /**
     * @var integer
     */
    private $CustomerId;

    /**
     * @var boolean
     */
    private $DeleteFlag;

    /**
     * @var \DateTime
     */
    private $PeriodStart;

    /**
     * @var \DateTime
     */
    private $PeriodEnd;

    /**
     * @var integer
     */
    private $ManagerId;

    /**
     * @var string
     */
    private $EstimateFilePath;

    /**
     * @var string
     */
    private $ScheduleFilePath;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBProjectUsersProjectMasterId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBProjectCostMastersProjectMasterId;

    /**
     * @var \Arte\PCMS\PublicBundle\Entity\TBCustomer
     */
    private $TBCustomerCustomerId;

    /**
     * @var \Arte\PCMS\PublicBundle\Entity\TBSystemUser
     */
    private $TBSystemUserManagerId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->TBProjectUsersProjectMasterId = new \Doctrine\Common\Collections\ArrayCollection();
        $this->TBProjectCostMastersProjectMasterId = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Set Name
     *
     * @param string $name
     * @return TBProjectMasterSearchModel
     */
    public function setName($name)
    {
        $this->Name = $name;
    
        return $this;
    }

    /**
     * Get Name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * Set Status
     *
     * @param integer $status
     * @return TBProjectMasterSearchModel
     */
    public function setStatus($status)
    {
        $this->Status = $status;
    
        return $this;
    }

    /**
     * Get Status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->Status;
    }

    /**
     * Set Explanation
     *
     * @param string $explanation
     * @return TBProjectMasterSearchModel
     */
    public function setExplanation($explanation)
    {
        $this->Explanation = $explanation;
    
        return $this;
    }

    /**
     * Get Explanation
     *
     * @return string 
     */
    public function getExplanation()
    {
        return $this->Explanation;
    }

    /**
     * Set CustomerId
     *
     * @param integer $customerId
     * @return TBProjectMasterSearchModel
     */
    public function setCustomerId($customerId)
    {
        $this->CustomerId = $customerId;
    
        return $this;
    }

    /**
     * Get CustomerId
     *
     * @return integer 
     */
    public function getCustomerId()
    {
        return $this->CustomerId;
    }

    /**
     * Set DeleteFlag
     *
     * @param boolean $deleteFlag
     * @return TBProjectMasterSearchModel
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
     * Set PeriodStart
     *
     * @param \DateTime $periodStart
     * @return TBProjectMasterSearchModel
     */
    public function setPeriodStart($periodStart)
    {
        $this->PeriodStart = $periodStart;
    
        return $this;
    }

    /**
     * Get PeriodStart
     *
     * @return \DateTime 
     */
    public function getPeriodStart()
    {
        return $this->PeriodStart;
    }

    /**
     * Set PeriodEnd
     *
     * @param \DateTime $periodEnd
     * @return TBProjectMasterSearchModel
     */
    public function setPeriodEnd($periodEnd)
    {
        $this->PeriodEnd = $periodEnd;
    
        return $this;
    }

    /**
     * Get PeriodEnd
     *
     * @return \DateTime 
     */
    public function getPeriodEnd()
    {
        return $this->PeriodEnd;
    }

    /**
     * Set ManagerId
     *
     * @param integer $managerId
     * @return TBProjectMasterSearchModel
     */
    public function setManagerId($managerId)
    {
        $this->ManagerId = $managerId;
    
        return $this;
    }

    /**
     * Get ManagerId
     *
     * @return integer 
     */
    public function getManagerId()
    {
        return $this->ManagerId;
    }

    /**
     * Set EstimateFilePath
     *
     * @param string $estimateFilePath
     * @return TBProjectMasterSearchModel
     */
    public function setEstimateFilePath($estimateFilePath)
    {
        $this->EstimateFilePath = $estimateFilePath;
    
        return $this;
    }

    /**
     * Get EstimateFilePath
     *
     * @return string 
     */
    public function getEstimateFilePath()
    {
        return $this->EstimateFilePath;
    }

    /**
     * Set ScheduleFilePath
     *
     * @param string $scheduleFilePath
     * @return TBProjectMasterSearchModel
     */
    public function setScheduleFilePath($scheduleFilePath)
    {
        $this->ScheduleFilePath = $scheduleFilePath;
    
        return $this;
    }

    /**
     * Get ScheduleFilePath
     *
     * @return string 
     */
    public function getScheduleFilePath()
    {
        return $this->ScheduleFilePath;
    }

    /**
     * Add TBProjectUsersProjectMasterId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\TBProjectUser $tBProjectUsersProjectMasterId
     * @return TBProjectMasterSearchModel
     */
    public function addTBProjectUsersProjectMasterId(\Arte\PCMS\PublicBundle\Entity\TBProjectUser $tBProjectUsersProjectMasterId)
    {
        $this->TBProjectUsersProjectMasterId[] = $tBProjectUsersProjectMasterId;
    
        return $this;
    }

    /**
     * Remove TBProjectUsersProjectMasterId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\TBProjectUser $tBProjectUsersProjectMasterId
     */
    public function removeTBProjectUsersProjectMasterId(\Arte\PCMS\PublicBundle\Entity\TBProjectUser $tBProjectUsersProjectMasterId)
    {
        $this->TBProjectUsersProjectMasterId->removeElement($tBProjectUsersProjectMasterId);
    }

    /**
     * Get TBProjectUsersProjectMasterId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTBProjectUsersProjectMasterId()
    {
        return $this->TBProjectUsersProjectMasterId;
    }

    /**
     * Add TBProjectCostMastersProjectMasterId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\TBProjectCostMaster $tBProjectCostMastersProjectMasterId
     * @return TBProjectMasterSearchModel
     */
    public function addTBProjectCostMastersProjectMasterId(\Arte\PCMS\PublicBundle\Entity\TBProjectCostMaster $tBProjectCostMastersProjectMasterId)
    {
        $this->TBProjectCostMastersProjectMasterId[] = $tBProjectCostMastersProjectMasterId;
    
        return $this;
    }

    /**
     * Remove TBProjectCostMastersProjectMasterId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\TBProjectCostMaster $tBProjectCostMastersProjectMasterId
     */
    public function removeTBProjectCostMastersProjectMasterId(\Arte\PCMS\PublicBundle\Entity\TBProjectCostMaster $tBProjectCostMastersProjectMasterId)
    {
        $this->TBProjectCostMastersProjectMasterId->removeElement($tBProjectCostMastersProjectMasterId);
    }

    /**
     * Get TBProjectCostMastersProjectMasterId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTBProjectCostMastersProjectMasterId()
    {
        return $this->TBProjectCostMastersProjectMasterId;
    }

    /**
     * Set TBCustomerCustomerId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\TBCustomer $tBCustomerCustomerId
     * @return TBProjectMasterSearchModel
     */
    public function setTBCustomerCustomerId(\Arte\PCMS\PublicBundle\Entity\TBCustomer $tBCustomerCustomerId = null)
    {
        $this->TBCustomerCustomerId = $tBCustomerCustomerId;
    
        return $this;
    }

    /**
     * Get TBCustomerCustomerId
     *
     * @return \Arte\PCMS\PublicBundle\Entity\TBCustomer 
     */
    public function getTBCustomerCustomerId()
    {
        return $this->TBCustomerCustomerId;
    }

    /**
     * Set TBSystemUserManagerId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\TBSystemUser $tBSystemUserManagerId
     * @return TBProjectMasterSearchModel
     */
    public function setTBSystemUserManagerId(\Arte\PCMS\PublicBundle\Entity\TBSystemUser $tBSystemUserManagerId = null)
    {
        $this->TBSystemUserManagerId = $tBSystemUserManagerId;
    
        return $this;
    }

    /**
     * Get TBSystemUserManagerId
     *
     * @return \Arte\PCMS\PublicBundle\Entity\TBSystemUser 
     */
    public function getTBSystemUserManagerId()
    {
        return $this->TBSystemUserManagerId;
    }
}
