<?php

namespace Arte\PCMS\BizlogicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TBCustomer
 */
class TBCustomer
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
     * @var boolean
     */
    private $DeleteFlag;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBProjectMastersCustomerId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->TBProjectMastersCustomerId = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return TBCustomer
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
     * Set DeleteFlag
     *
     * @param boolean $deleteFlag
     * @return TBCustomer
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
     * Add TBProjectMastersCustomerId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $tBProjectMastersCustomerId
     * @return TBCustomer
     */
    public function addTBProjectMastersCustomerId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $tBProjectMastersCustomerId)
    {
        $this->TBProjectMastersCustomerId[] = $tBProjectMastersCustomerId;
    
        return $this;
    }

    /**
     * Remove TBProjectMastersCustomerId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $tBProjectMastersCustomerId
     */
    public function removeTBProjectMastersCustomerId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $tBProjectMastersCustomerId)
    {
        $this->TBProjectMastersCustomerId->removeElement($tBProjectMastersCustomerId);
    }

    /**
     * Get TBProjectMastersCustomerId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTBProjectMastersCustomerId()
    {
        return $this->TBProjectMastersCustomerId;
    }
}