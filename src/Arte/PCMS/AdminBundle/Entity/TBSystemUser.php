<?php

namespace Arte\PCMS\AdminBundle\Entity;

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
     * @var integer
     */
    private $CreatedUserId;

    /**
     * @var \DateTime
     */
    private $CreatedDatetime;

    /**
     * @var integer
     */
    private $UpdatedUserId;

    /**
     * @var \DateTime
     */
    private $UpdatedDatetime;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBSystemUsersUpdatedUserId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBSystemUsersCreatedUserId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBDepartmentsUpdatedUserId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBDepartmentsCreatedUserId;

    /**
     * @var \Arte\PCMS\AdminBundle\Entity\TBDepartment
     */
    private $TBDepartmentDepartmentId;

    /**
     * @var \Arte\PCMS\AdminBundle\Entity\TBSystemUser
     */
    private $TBSystemUserUpdatedUserId;

    /**
     * @var \Arte\PCMS\AdminBundle\Entity\TBSystemUser
     */
    private $TBSystemUserCreatedUserId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->TBSystemUsersUpdatedUserId = new \Doctrine\Common\Collections\ArrayCollection();
        $this->TBSystemUsersCreatedUserId = new \Doctrine\Common\Collections\ArrayCollection();
        $this->TBDepartmentsUpdatedUserId = new \Doctrine\Common\Collections\ArrayCollection();
        $this->TBDepartmentsCreatedUserId = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set CreatedUserId
     *
     * @param integer $createdUserId
     * @return TBSystemUser
     */
    public function setCreatedUserId($createdUserId)
    {
        $this->CreatedUserId = $createdUserId;
    
        return $this;
    }

    /**
     * Get CreatedUserId
     *
     * @return integer 
     */
    public function getCreatedUserId()
    {
        return $this->CreatedUserId;
    }

    /**
     * Set CreatedDatetime
     *
     * @param \DateTime $createdDatetime
     * @return TBSystemUser
     */
    public function setCreatedDatetime($createdDatetime)
    {
        $this->CreatedDatetime = $createdDatetime;
    
        return $this;
    }

    /**
     * Get CreatedDatetime
     *
     * @return \DateTime 
     */
    public function getCreatedDatetime()
    {
        return $this->CreatedDatetime;
    }

    /**
     * Set UpdatedUserId
     *
     * @param integer $updatedUserId
     * @return TBSystemUser
     */
    public function setUpdatedUserId($updatedUserId)
    {
        $this->UpdatedUserId = $updatedUserId;
    
        return $this;
    }

    /**
     * Get UpdatedUserId
     *
     * @return integer 
     */
    public function getUpdatedUserId()
    {
        return $this->UpdatedUserId;
    }

    /**
     * Set UpdatedDatetime
     *
     * @param \DateTime $updatedDatetime
     * @return TBSystemUser
     */
    public function setUpdatedDatetime($updatedDatetime)
    {
        $this->UpdatedDatetime = $updatedDatetime;
    
        return $this;
    }

    /**
     * Get UpdatedDatetime
     *
     * @return \DateTime 
     */
    public function getUpdatedDatetime()
    {
        return $this->UpdatedDatetime;
    }

    /**
     * Add TBSystemUsersUpdatedUserId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUsersUpdatedUserId
     * @return TBSystemUser
     */
    public function addTBSystemUsersUpdatedUserId(\Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUsersUpdatedUserId)
    {
        $this->TBSystemUsersUpdatedUserId[] = $tBSystemUsersUpdatedUserId;
    
        return $this;
    }

    /**
     * Remove TBSystemUsersUpdatedUserId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUsersUpdatedUserId
     */
    public function removeTBSystemUsersUpdatedUserId(\Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUsersUpdatedUserId)
    {
        $this->TBSystemUsersUpdatedUserId->removeElement($tBSystemUsersUpdatedUserId);
    }

    /**
     * Get TBSystemUsersUpdatedUserId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTBSystemUsersUpdatedUserId()
    {
        return $this->TBSystemUsersUpdatedUserId;
    }

    /**
     * Add TBSystemUsersCreatedUserId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUsersCreatedUserId
     * @return TBSystemUser
     */
    public function addTBSystemUsersCreatedUserId(\Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUsersCreatedUserId)
    {
        $this->TBSystemUsersCreatedUserId[] = $tBSystemUsersCreatedUserId;
    
        return $this;
    }

    /**
     * Remove TBSystemUsersCreatedUserId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUsersCreatedUserId
     */
    public function removeTBSystemUsersCreatedUserId(\Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUsersCreatedUserId)
    {
        $this->TBSystemUsersCreatedUserId->removeElement($tBSystemUsersCreatedUserId);
    }

    /**
     * Get TBSystemUsersCreatedUserId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTBSystemUsersCreatedUserId()
    {
        return $this->TBSystemUsersCreatedUserId;
    }

    /**
     * Add TBDepartmentsUpdatedUserId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBDepartment $tBDepartmentsUpdatedUserId
     * @return TBSystemUser
     */
    public function addTBDepartmentsUpdatedUserId(\Arte\PCMS\AdminBundle\Entity\TBDepartment $tBDepartmentsUpdatedUserId)
    {
        $this->TBDepartmentsUpdatedUserId[] = $tBDepartmentsUpdatedUserId;
    
        return $this;
    }

    /**
     * Remove TBDepartmentsUpdatedUserId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBDepartment $tBDepartmentsUpdatedUserId
     */
    public function removeTBDepartmentsUpdatedUserId(\Arte\PCMS\AdminBundle\Entity\TBDepartment $tBDepartmentsUpdatedUserId)
    {
        $this->TBDepartmentsUpdatedUserId->removeElement($tBDepartmentsUpdatedUserId);
    }

    /**
     * Get TBDepartmentsUpdatedUserId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTBDepartmentsUpdatedUserId()
    {
        return $this->TBDepartmentsUpdatedUserId;
    }

    /**
     * Add TBDepartmentsCreatedUserId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBDepartment $tBDepartmentsCreatedUserId
     * @return TBSystemUser
     */
    public function addTBDepartmentsCreatedUserId(\Arte\PCMS\AdminBundle\Entity\TBDepartment $tBDepartmentsCreatedUserId)
    {
        $this->TBDepartmentsCreatedUserId[] = $tBDepartmentsCreatedUserId;
    
        return $this;
    }

    /**
     * Remove TBDepartmentsCreatedUserId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBDepartment $tBDepartmentsCreatedUserId
     */
    public function removeTBDepartmentsCreatedUserId(\Arte\PCMS\AdminBundle\Entity\TBDepartment $tBDepartmentsCreatedUserId)
    {
        $this->TBDepartmentsCreatedUserId->removeElement($tBDepartmentsCreatedUserId);
    }

    /**
     * Get TBDepartmentsCreatedUserId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTBDepartmentsCreatedUserId()
    {
        return $this->TBDepartmentsCreatedUserId;
    }

    /**
     * Set TBDepartmentDepartmentId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBDepartment $tBDepartmentDepartmentId
     * @return TBSystemUser
     */
    public function setTBDepartmentDepartmentId(\Arte\PCMS\AdminBundle\Entity\TBDepartment $tBDepartmentDepartmentId = null)
    {
        $this->TBDepartmentDepartmentId = $tBDepartmentDepartmentId;
    
        return $this;
    }

    /**
     * Get TBDepartmentDepartmentId
     *
     * @return \Arte\PCMS\AdminBundle\Entity\TBDepartment 
     */
    public function getTBDepartmentDepartmentId()
    {
        return $this->TBDepartmentDepartmentId;
    }

    /**
     * Set TBSystemUserUpdatedUserId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUserUpdatedUserId
     * @return TBSystemUser
     */
    public function setTBSystemUserUpdatedUserId(\Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUserUpdatedUserId = null)
    {
        $this->TBSystemUserUpdatedUserId = $tBSystemUserUpdatedUserId;
    
        return $this;
    }

    /**
     * Get TBSystemUserUpdatedUserId
     *
     * @return \Arte\PCMS\AdminBundle\Entity\TBSystemUser 
     */
    public function getTBSystemUserUpdatedUserId()
    {
        return $this->TBSystemUserUpdatedUserId;
    }

    /**
     * Set TBSystemUserCreatedUserId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUserCreatedUserId
     * @return TBSystemUser
     */
    public function setTBSystemUserCreatedUserId(\Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUserCreatedUserId = null)
    {
        $this->TBSystemUserCreatedUserId = $tBSystemUserCreatedUserId;
    
        return $this;
    }

    /**
     * Get TBSystemUserCreatedUserId
     *
     * @return \Arte\PCMS\AdminBundle\Entity\TBSystemUser 
     */
    public function getTBSystemUserCreatedUserId()
    {
        return $this->TBSystemUserCreatedUserId;
    }
}
