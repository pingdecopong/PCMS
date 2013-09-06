<?php

namespace Arte\PCMS\BizlogicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TBProjectCostMaster
 */
class TBProjectCostMaster
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $ProjectMasterId;

    /**
     * @var string
     */
    private $Name;

    /**
     * @var integer
     */
    private $Cost;

    /**
     * @var integer
     */
    private $SortNo;

    /**
     * @var boolean
     */
    private $DeleteFlag;

    /**
     * @var string
     */
    private $HierarchyPath;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBProductionCostsProjectCostMasterId;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster
     */
    private $TBProjectMasterProjectMasterId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->TBProductionCostsProjectCostMasterId = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set ProjectMasterId
     *
     * @param integer $projectMasterId
     * @return TBProjectCostMaster
     */
    public function setProjectMasterId($projectMasterId)
    {
        $this->ProjectMasterId = $projectMasterId;
    
        return $this;
    }

    /**
     * Get ProjectMasterId
     *
     * @return integer 
     */
    public function getProjectMasterId()
    {
        return $this->ProjectMasterId;
    }

    /**
     * Set Name
     *
     * @param string $name
     * @return TBProjectCostMaster
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
     * Set Cost
     *
     * @param integer $cost
     * @return TBProjectCostMaster
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
     * Set SortNo
     *
     * @param integer $sortNo
     * @return TBProjectCostMaster
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
     * Set DeleteFlag
     *
     * @param boolean $deleteFlag
     * @return TBProjectCostMaster
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
     * Set HierarchyPath
     *
     * @param string $hierarchyPath
     * @return TBProjectCostMaster
     */
    public function setHierarchyPath($hierarchyPath)
    {
        $this->HierarchyPath = $hierarchyPath;
    
        return $this;
    }

    /**
     * Get HierarchyPath
     *
     * @return string 
     */
    public function getHierarchyPath()
    {
        return $this->HierarchyPath;
    }

    /**
     * Add TBProductionCostsProjectCostMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProductionCost $tBProductionCostsProjectCostMasterId
     * @return TBProjectCostMaster
     */
    public function addTBProductionCostsProjectCostMasterId(\Arte\PCMS\BizlogicBundle\Entity\TBProductionCost $tBProductionCostsProjectCostMasterId)
    {
        $this->TBProductionCostsProjectCostMasterId[] = $tBProductionCostsProjectCostMasterId;
    
        return $this;
    }

    /**
     * Remove TBProductionCostsProjectCostMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProductionCost $tBProductionCostsProjectCostMasterId
     */
    public function removeTBProductionCostsProjectCostMasterId(\Arte\PCMS\BizlogicBundle\Entity\TBProductionCost $tBProductionCostsProjectCostMasterId)
    {
        $this->TBProductionCostsProjectCostMasterId->removeElement($tBProductionCostsProjectCostMasterId);
    }

    /**
     * Get TBProductionCostsProjectCostMasterId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTBProductionCostsProjectCostMasterId()
    {
        return $this->TBProductionCostsProjectCostMasterId;
    }

    /**
     * Set TBProjectMasterProjectMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $tBProjectMasterProjectMasterId
     * @return TBProjectCostMaster
     */
    public function setTBProjectMasterProjectMasterId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $tBProjectMasterProjectMasterId = null)
    {
        $this->TBProjectMasterProjectMasterId = $tBProjectMasterProjectMasterId;
    
        return $this;
    }

    /**
     * Get TBProjectMasterProjectMasterId
     *
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster 
     */
    public function getTBProjectMasterProjectMasterId()
    {
        return $this->TBProjectMasterProjectMasterId;
    }
    /**
     * @var integer
     */
    private $TBProjectCostHierarchyMasterId;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBProjectCostHierarchyMaster
     */
    private $TBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId;


    /**
     * Set TBProjectCostHierarchyMasterId
     *
     * @param integer $tBProjectCostHierarchyMasterId
     * @return TBProjectCostMaster
     */
    public function setTBProjectCostHierarchyMasterId($tBProjectCostHierarchyMasterId)
    {
        $this->TBProjectCostHierarchyMasterId = $tBProjectCostHierarchyMasterId;
    
        return $this;
    }

    /**
     * Get TBProjectCostHierarchyMasterId
     *
     * @return integer 
     */
    public function getTBProjectCostHierarchyMasterId()
    {
        return $this->TBProjectCostHierarchyMasterId;
    }

    /**
     * Set TBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectCostHierarchyMaster $tBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId
     * @return TBProjectCostMaster
     */
    public function setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectCostHierarchyMaster $tBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId = null)
    {
        $this->TBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId = $tBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId;
    
        return $this;
    }

    /**
     * Get TBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId
     *
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBProjectCostHierarchyMaster 
     */
    public function getTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId()
    {
        return $this->TBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId;
    }
}