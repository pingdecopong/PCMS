<?php

namespace Arte\PCMS\PublicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VProjectUser
 */
class VProjectUser
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
     * @var \Arte\PCMS\PublicBundle\Entity\VProjectView
     */
    private $VProjectViewProjectMasterId;


    /**
     * Set SystemUserId
     *
     * @param integer $systemUserId
     * @return VProjectUser
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
     * @return VProjectUser
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
     * @return VProjectUser
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
     * @return VProjectUser
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
     * Set VProjectViewProjectMasterId
     *
     * @param \Arte\PCMS\PublicBundle\Entity\VProjectView $vProjectViewProjectMasterId
     * @return VProjectUser
     */
    public function setVProjectViewProjectMasterId(\Arte\PCMS\PublicBundle\Entity\VProjectView $vProjectViewProjectMasterId = null)
    {
        $this->VProjectViewProjectMasterId = $vProjectViewProjectMasterId;
    
        return $this;
    }

    /**
     * Get VProjectViewProjectMasterId
     *
     * @return \Arte\PCMS\PublicBundle\Entity\VProjectView 
     */
    public function getVProjectViewProjectMasterId()
    {
        return $this->VProjectViewProjectMasterId;
    }
}
