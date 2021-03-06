<?php

namespace Arte\PCMS\BizlogicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TBProjectCostHierarchyMaster
 */
class TBProjectCostHierarchyMaster
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $TBProjectMasterId;

    /**
     * @var string
     */
    private $Name;

    /**
     * @var string
     */
    private $Path;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBProjectCostMastersTBProjectCostHierarchyMasterId;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster
     */
    private $TBProjectMasterTBProjectMasterId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->TBProjectCostMastersTBProjectCostHierarchyMasterId = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set TBProjectMasterId
     *
     * @param integer $tBProjectMasterId
     * @return TBProjectCostHierarchyMaster
     */
    public function setTBProjectMasterId($tBProjectMasterId)
    {
        $this->TBProjectMasterId = $tBProjectMasterId;
    
        return $this;
    }

    /**
     * Get TBProjectMasterId
     *
     * @return integer 
     */
    public function getTBProjectMasterId()
    {
        return $this->TBProjectMasterId;
    }

    /**
     * Set Name
     *
     * @param string $name
     * @return TBProjectCostHierarchyMaster
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
     * Set Path
     *
     * @param string $path
     * @return TBProjectCostHierarchyMaster
     */
    public function setPath($path)
    {
        $this->Path = $path;
    
        return $this;
    }

    /**
     * Get Path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->Path;
    }

    /**
     * Add TBProjectCostMastersTBProjectCostHierarchyMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster $tBProjectCostMastersTBProjectCostHierarchyMasterId
     * @return TBProjectCostHierarchyMaster
     */
    public function addTBProjectCostMastersTBProjectCostHierarchyMasterId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster $tBProjectCostMastersTBProjectCostHierarchyMasterId)
    {
        $this->TBProjectCostMastersTBProjectCostHierarchyMasterId[] = $tBProjectCostMastersTBProjectCostHierarchyMasterId;
    
        return $this;
    }

    /**
     * Remove TBProjectCostMastersTBProjectCostHierarchyMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster $tBProjectCostMastersTBProjectCostHierarchyMasterId
     */
    public function removeTBProjectCostMastersTBProjectCostHierarchyMasterId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster $tBProjectCostMastersTBProjectCostHierarchyMasterId)
    {
        $this->TBProjectCostMastersTBProjectCostHierarchyMasterId->removeElement($tBProjectCostMastersTBProjectCostHierarchyMasterId);
    }

    /**
     * Get TBProjectCostMastersTBProjectCostHierarchyMasterId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTBProjectCostMastersTBProjectCostHierarchyMasterId()
    {
        return $this->TBProjectCostMastersTBProjectCostHierarchyMasterId;
    }

    /**
     * Set TBProjectMasterTBProjectMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $tBProjectMasterTBProjectMasterId
     * @return TBProjectCostHierarchyMaster
     */
    public function setTBProjectMasterTBProjectMasterId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $tBProjectMasterTBProjectMasterId = null)
    {
        $this->TBProjectMasterTBProjectMasterId = $tBProjectMasterTBProjectMasterId;
    
        return $this;
    }

    /**
     * Get TBProjectMasterTBProjectMasterId
     *
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster 
     */
    public function getTBProjectMasterTBProjectMasterId()
    {
        return $this->TBProjectMasterTBProjectMasterId;
    }
    /**
     * @var integer
     */
    private $SortNo;


    /**
     * Set SortNo
     *
     * @param integer $sortNo
     * @return TBProjectCostHierarchyMaster
     */
    public function setSortNo($sortNo)
    {
        $this->SortNo = $sortNo;
    
        return $this;
    }

    /**
     * Get SortNo
     *
     * @return integer 
     */
    public function getSortNo()
    {
        return $this->SortNo;
    }
    /**
     * @var boolean
     */
    private $DeleteFlag;


    /**
     * Set DeleteFlag
     *
     * @param boolean $deleteFlag
     * @return TBProjectCostHierarchyMaster
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
}