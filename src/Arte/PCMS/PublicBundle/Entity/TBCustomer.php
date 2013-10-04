<?php

namespace Arte\PCMS\PublicBundle\Entity;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $VProjectViewsCustomerId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->TBProjectMastersCustomerId = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VProjectViewsCustomerId = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \Arte\PCMS\PublicBundle\Entity\TBProjectMaster $tBProjectMastersCustomerId
     * @return TBCustomer
     */
    public function addTBProjectMastersCustomerId(\Arte\PCMS\PublicBundle\Entity\TBProjectMaster $tBProjectMastersCustomerId)
    {
        $this->TBProjectMastersCustomerId[] = $tBProjectMastersCustomerId;
    
        return $this;
    }

    /**
     * Remove TBProjectMastersCustomerId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\TBProjectMaster $tBProjectMastersCustomerId
     */
    public function removeTBProjectMastersCustomerId(\Arte\PCMS\PublicBundle\Entity\TBProjectMaster $tBProjectMastersCustomerId)
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

    /**
     * Add VProjectViewsCustomerId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\VProjectView $vProjectViewsCustomerId
     * @return TBCustomer
     */
    public function addVProjectViewsCustomerId(\Arte\PCMS\PublicBundle\Entity\VProjectView $vProjectViewsCustomerId)
    {
        $this->VProjectViewsCustomerId[] = $vProjectViewsCustomerId;
    
        return $this;
    }

    /**
     * Remove VProjectViewsCustomerId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\VProjectView $vProjectViewsCustomerId
     */
    public function removeVProjectViewsCustomerId(\Arte\PCMS\PublicBundle\Entity\VProjectView $vProjectViewsCustomerId)
    {
        $this->VProjectViewsCustomerId->removeElement($vProjectViewsCustomerId);
    }

    /**
     * Get VProjectViewsCustomerId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVProjectViewsCustomerId()
    {
        return $this->VProjectViewsCustomerId;
    }
}
