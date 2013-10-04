<?php

namespace Arte\PCMS\PublicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TBProjectUser
 */
class TBProjectUser
{
    /**
     * @var integer
     */
    private $SystemUserId;

    /**
     * @var integer
     */
    private $ProjectMasterId;

    /**
     * @var integer
     */
    private $RoleNo;

    /**
     * @var \Arte\PCMS\PublicBundle\Entity\TBSystemUser
     */
    private $TBSystemUserSystemUserId;

    /**
     * @var \Arte\PCMS\PublicBundle\Entity\TBProjectMaster
     */
    private $TBProjectMasterProjectMasterId;


    /**
     * Set SystemUserId
     *
     * @param integer $systemUserId
     * @return TBProjectUser
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
     * Set ProjectMasterId
     *
     * @param integer $projectMasterId
     * @return TBProjectUser
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
     * Set RoleNo
     *
     * @param integer $roleNo
     * @return TBProjectUser
     */
    public function setRoleNo($roleNo)
    {
        $this->RoleNo = $roleNo;
    
        return $this;
    }

    /**
     * Get RoleNo
     *
     * @return integer 
     */
    public function getRoleNo()
    {
        return $this->RoleNo;
    }

    /**
     * Set TBSystemUserSystemUserId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\TBSystemUser $tBSystemUserSystemUserId
     * @return TBProjectUser
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

    /**
     * Set TBProjectMasterProjectMasterId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\TBProjectMaster $tBProjectMasterProjectMasterId
     * @return TBProjectUser
     */
    public function setTBProjectMasterProjectMasterId(\Arte\PCMS\PublicBundle\Entity\TBProjectMaster $tBProjectMasterProjectMasterId = null)
    {
        $this->TBProjectMasterProjectMasterId = $tBProjectMasterProjectMasterId;
    
        return $this;
    }

    /**
     * Get TBProjectMasterProjectMasterId
     *
     * @return \Arte\PCMS\PublicBundle\Entity\TBProjectMaster 
     */
    public function getTBProjectMasterProjectMasterId()
    {
        return $this->TBProjectMasterProjectMasterId;
    }
}
