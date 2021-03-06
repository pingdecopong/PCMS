<?php

namespace Arte\PCMS\BizlogicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TBProjectUser
 */
class TBProjectUser
{
    /**
     * 管理者
     */
    const ROLE_MANAGER = 1;
    /**
     * 一般担当者
     */
    const ROLE_GENERAL = 2;
    /**
     * 表示のみ
     */
    const ROLE_READONLY = 3;

    /**
     * 権限一覧取得
     * @return array　権限一覧
     */
    public static function getRoleLists()
    {
        return array(
            self::ROLE_MANAGER,
            self::ROLE_GENERAL,
            self::ROLE_READONLY,
        );
    }
//    /**
//     * @var integer
//     */
//    private $id;

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
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser
     */
    private $TBSystemUserSystemUserId;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster
     */
    private $TBProjectMasterProjectMasterId;


//    /**
//     * Get id
//     *
//     * @return integer
//     */
//    public function getId()
//    {
//        return $this->id;
//    }

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
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tBSystemUserSystemUserId
     * @return TBProjectUser
     */
    public function setTBSystemUserSystemUserId(\Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tBSystemUserSystemUserId = null)
    {
        $this->TBSystemUserSystemUserId = $tBSystemUserSystemUserId;
    
        return $this;
    }

    /**
     * Get TBSystemUserSystemUserId
     *
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser 
     */
    public function getTBSystemUserSystemUserId()
    {
        return $this->TBSystemUserSystemUserId;
    }

    /**
     * Set TBProjectMasterProjectMasterId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $tBProjectMasterProjectMasterId
     * @return TBProjectUser
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
}