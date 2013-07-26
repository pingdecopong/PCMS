<?php

namespace Arte\PCMS\BizlogicBundle\Entity;

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
    private $Cost;

    /**
     * @var string
     */
    private $Note;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster
     */
    private $TBProjectCostMasterProjectCostMasterId;


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
     * Set TBProjectCostMasterProjectCostMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster $tBProjectCostMasterProjectCostMasterId
     * @return TBProductionCost
     */
    public function setTBProjectCostMasterProjectCostMasterId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster $tBProjectCostMasterProjectCostMasterId = null)
    {
        $this->TBProjectCostMasterProjectCostMasterId = $tBProjectCostMasterProjectCostMasterId;
    
        return $this;
    }

    /**
     * Get TBProjectCostMasterProjectCostMasterId
     *
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster 
     */
    public function getTBProjectCostMasterProjectCostMasterId()
    {
        return $this->TBProjectCostMasterProjectCostMasterId;
    }
}