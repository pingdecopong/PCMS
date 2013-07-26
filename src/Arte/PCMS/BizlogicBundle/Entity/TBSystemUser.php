<?php

namespace Arte\PCMS\BizlogicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TBSystemUser
 */
class TBSystemUser
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $LoginId;

    /**
     * @var string
     */
    private $Salt;

    /**
     * @var string
     */
    private $Password;

    /**
     * @var boolean
     */
    private $Active;

    /**
     * @var integer
     */
    private $SystemRoleId;

    /**
     * @var string
     */
    private $DisplayName;

    /**
     * @var string
     */
    private $DisplayNameKana;

    /**
     * @var string
     */
    private $NickName;

    /**
     * @var string
     */
    private $MailAddress;

    /**
     * @var integer
     */
    private $DepartmentId;

    /**
     * @var \DateTime
     */
    private $LastLoginDatetime;

    /**
     * @var boolean
     */
    private $DeleteFlag;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBProjectUsersSystemUserId;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBDepartment
     */
    private $TBDepartmentDepartmentId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->TBProjectUsersSystemUserId = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set LoginId
     *
     * @param string $loginId
     * @return TBSystemUser
     */
    public function setLoginId($loginId)
    {
        $this->LoginId = $loginId;
    
        return $this;
    }

    /**
     * Get LoginId
     *
     * @return string 
     */
    public function getLoginId()
    {
        return $this->LoginId;
    }

    /**
     * Set Salt
     *
     * @param string $salt
     * @return TBSystemUser
     */
    public function setSalt($salt)
    {
        $this->Salt = $salt;
    
        return $this;
    }

    /**
     * Get Salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->Salt;
    }

    /**
     * Set Password
     *
     * @param string $password
     * @return TBSystemUser
     */
    public function setPassword($password)
    {
        $this->Password = $password;
    
        return $this;
    }

    /**
     * Get Password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->Password;
    }

    /**
     * Set Active
     *
     * @param boolean $active
     * @return TBSystemUser
     */
    public function setActive($active)
    {
        $this->Active = $active;
    
        return $this;
    }

    /**
     * Get Active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->Active;
    }

    /**
     * Set SystemRoleId
     *
     * @param integer $systemRoleId
     * @return TBSystemUser
     */
    public function setSystemRoleId($systemRoleId)
    {
        $this->SystemRoleId = $systemRoleId;
    
        return $this;
    }

    /**
     * Get SystemRoleId
     *
     * @return integer 
     */
    public function getSystemRoleId()
    {
        return $this->SystemRoleId;
    }

    /**
     * Set DisplayName
     *
     * @param string $displayName
     * @return TBSystemUser
     */
    public function setDisplayName($displayName)
    {
        $this->DisplayName = $displayName;
    
        return $this;
    }

    /**
     * Get DisplayName
     *
     * @return string 
     */
    public function getDisplayName()
    {
        return $this->DisplayName;
    }

    /**
     * Set DisplayNameKana
     *
     * @param string $displayNameKana
     * @return TBSystemUser
     */
    public function setDisplayNameKana($displayNameKana)
    {
        $this->DisplayNameKana = $displayNameKana;
    
        return $this;
    }

    /**
     * Get DisplayNameKana
     *
     * @return string 
     */
    public function getDisplayNameKana()
    {
        return $this->DisplayNameKana;
    }

    /**
     * Set NickName
     *
     * @param string $nickName
     * @return TBSystemUser
     */
    public function setNickName($nickName)
    {
        $this->NickName = $nickName;
    
        return $this;
    }

    /**
     * Get NickName
     *
     * @return string 
     */
    public function getNickName()
    {
        return $this->NickName;
    }

    /**
     * Set MailAddress
     *
     * @param string $mailAddress
     * @return TBSystemUser
     */
    public function setMailAddress($mailAddress)
    {
        $this->MailAddress = $mailAddress;
    
        return $this;
    }

    /**
     * Get MailAddress
     *
     * @return string 
     */
    public function getMailAddress()
    {
        return $this->MailAddress;
    }

    /**
     * Set DepartmentId
     *
     * @param integer $departmentId
     * @return TBSystemUser
     */
    public function setDepartmentId($departmentId)
    {
        $this->DepartmentId = $departmentId;
    
        return $this;
    }

    /**
     * Get DepartmentId
     *
     * @return integer 
     */
    public function getDepartmentId()
    {
        return $this->DepartmentId;
    }

    /**
     * Set LastLoginDatetime
     *
     * @param \DateTime $lastLoginDatetime
     * @return TBSystemUser
     */
    public function setLastLoginDatetime($lastLoginDatetime)
    {
        $this->LastLoginDatetime = $lastLoginDatetime;
    
        return $this;
    }

    /**
     * Get LastLoginDatetime
     *
     * @return \DateTime 
     */
    public function getLastLoginDatetime()
    {
        return $this->LastLoginDatetime;
    }

    /**
     * Set DeleteFlag
     *
     * @param boolean $deleteFlag
     * @return TBSystemUser
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
     * Add TBProjectUsersSystemUserId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectUser $tBProjectUsersSystemUserId
     * @return TBSystemUser
     */
    public function addTBProjectUsersSystemUserId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectUser $tBProjectUsersSystemUserId)
    {
        $this->TBProjectUsersSystemUserId[] = $tBProjectUsersSystemUserId;
    
        return $this;
    }

    /**
     * Remove TBProjectUsersSystemUserId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectUser $tBProjectUsersSystemUserId
     */
    public function removeTBProjectUsersSystemUserId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectUser $tBProjectUsersSystemUserId)
    {
        $this->TBProjectUsersSystemUserId->removeElement($tBProjectUsersSystemUserId);
    }

    /**
     * Get TBProjectUsersSystemUserId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTBProjectUsersSystemUserId()
    {
        return $this->TBProjectUsersSystemUserId;
    }

    /**
     * Set TBDepartmentDepartmentId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBDepartment $tBDepartmentDepartmentId
     * @return TBSystemUser
     */
    public function setTBDepartmentDepartmentId(\Arte\PCMS\BizlogicBundle\Entity\TBDepartment $tBDepartmentDepartmentId = null)
    {
        $this->TBDepartmentDepartmentId = $tBDepartmentDepartmentId;
    
        return $this;
    }

    /**
     * Get TBDepartmentDepartmentId
     *
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBDepartment 
     */
    public function getTBDepartmentDepartmentId()
    {
        return $this->TBDepartmentDepartmentId;
    }
}