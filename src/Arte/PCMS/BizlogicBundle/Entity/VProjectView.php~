<?php

namespace Arte\PCMS\BizlogicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VProjectView
 */
class VProjectView
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
     * @var integer
     */
    private $CustomerId;

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
     * @var integer
     */
    private $ProjectTotalCost;

    /**
     * @var integer
     */
    private $ProductionTotalCost;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $VProjectUsersProjectMasterId;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser
     */
    private $TBSystemUserManagerId;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBCustomer
     */
    private $TBCustomerCustomerId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->VProjectUsersProjectMasterId = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return VProjectView
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
     * @return VProjectView
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
     * Set CustomerId
     *
     * @param integer $customerId
     * @return VProjectView
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
     * Set PeriodStart
     *
     * @param \DateTime $periodStart
     * @return VProjectView
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
     * @return VProjectView
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
     * @return VProjectView
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
     * Set ProjectTotalCost
     *
     * @param integer $projectTotalCost
     * @return VProjectView
     */
    public function setProjectTotalCost($projectTotalCost)
    {
        $this->ProjectTotalCost = $projectTotalCost;
    
        return $this;
    }

    /**
     * Get ProjectTotalCost
     *
     * @return integer 
     */
    public function getProjectTotalCost()
    {
        return $this->ProjectTotalCost;
    }

    /**
     * Set ProductionTotalCost
     *
     * @param integer $productionTotalCost
     * @return VProjectView
     */
    public function setProductionTotalCost($productionTotalCost)
    {
        $this->ProductionTotalCost = $productionTotalCost;
    
        return $this;
    }

    /**
     * Get ProductionTotalCost
     *
     * @return integer 
     */
    public function getProductionTotalCost()
    {
        return $this->ProductionTotalCost;
    }

    /**
     * Add VProjectUsersProjectMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\VProjectUser $vProjectUsersProjectMasterId
     * @return VProjectView
     */
    public function addVProjectUsersProjectMasterId(\Arte\PCMS\BizlogicBundle\Entity\VProjectUser $vProjectUsersProjectMasterId)
    {
        $this->VProjectUsersProjectMasterId[] = $vProjectUsersProjectMasterId;
    
        return $this;
    }

    /**
     * Remove VProjectUsersProjectMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\VProjectUser $vProjectUsersProjectMasterId
     */
    public function removeVProjectUsersProjectMasterId(\Arte\PCMS\BizlogicBundle\Entity\VProjectUser $vProjectUsersProjectMasterId)
    {
        $this->VProjectUsersProjectMasterId->removeElement($vProjectUsersProjectMasterId);
    }

    /**
     * Get VProjectUsersProjectMasterId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVProjectUsersProjectMasterId()
    {
        return $this->VProjectUsersProjectMasterId;
    }

    /**
     * Set TBSystemUserManagerId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tBSystemUserManagerId
     * @return VProjectView
     */
    public function setTBSystemUserManagerId(\Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tBSystemUserManagerId = null)
    {
        $this->TBSystemUserManagerId = $tBSystemUserManagerId;
    
        return $this;
    }

    /**
     * Get TBSystemUserManagerId
     *
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser 
     */
    public function getTBSystemUserManagerId()
    {
        return $this->TBSystemUserManagerId;
    }

    /**
     * Set TBCustomerCustomerId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBCustomer $tBCustomerCustomerId
     * @return VProjectView
     */
    public function setTBCustomerCustomerId(\Arte\PCMS\BizlogicBundle\Entity\TBCustomer $tBCustomerCustomerId = null)
    {
        $this->TBCustomerCustomerId = $tBCustomerCustomerId;
    
        return $this;
    }

    /**
     * Get TBCustomerCustomerId
     *
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBCustomer 
     */
    public function getTBCustomerCustomerId()
    {
        return $this->TBCustomerCustomerId;
    }
}