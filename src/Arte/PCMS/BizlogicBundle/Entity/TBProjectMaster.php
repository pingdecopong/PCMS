<?php

namespace Arte\PCMS\BizlogicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TBProjectMaster
 */
class TBProjectMaster
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
     * @var boolean
     */
    private $DeleteFlag;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBProjectUsersProjectMasterId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBProjectCostMastersProjectMasterId;

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
     * @return TBProjectMaster
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
     * @return TBProjectMaster
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
     * @return TBProjectMaster
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
     * Set DeleteFlag
     *
     * @param boolean $deleteFlag
     * @return TBProjectMaster
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
     * Add TBProjectUsersProjectMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectUser $tBProjectUsersProjectMasterId
     * @return TBProjectMaster
     */
    public function addTBProjectUsersProjectMasterId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectUser $tBProjectUsersProjectMasterId)
    {
        $this->TBProjectUsersProjectMasterId[] = $tBProjectUsersProjectMasterId;
    
        return $this;
    }

    /**
     * Remove TBProjectUsersProjectMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectUser $tBProjectUsersProjectMasterId
     */
    public function removeTBProjectUsersProjectMasterId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectUser $tBProjectUsersProjectMasterId)
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
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster $tBProjectCostMastersProjectMasterId
     * @return TBProjectMaster
     */
    public function addTBProjectCostMastersProjectMasterId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster $tBProjectCostMastersProjectMasterId)
    {
        $this->TBProjectCostMastersProjectMasterId[] = $tBProjectCostMastersProjectMasterId;
    
        return $this;
    }

    /**
     * Remove TBProjectCostMastersProjectMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster $tBProjectCostMastersProjectMasterId
     */
    public function removeTBProjectCostMastersProjectMasterId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster $tBProjectCostMastersProjectMasterId)
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
     * @var integer
     */
    private $CustomerId;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBCustomer
     */
    private $TBCustomerCustomerId;


    /**
     * Set CustomerId
     *
     * @param integer $customerId
     * @return TBProjectMaster
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
     * Set TBCustomerCustomerId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBCustomer $tBCustomerCustomerId
     * @return TBProjectMaster
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