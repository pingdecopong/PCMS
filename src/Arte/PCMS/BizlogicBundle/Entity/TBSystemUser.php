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
    private $DeleteFlug;

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
    private $tbsystemusers;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tbdepartments;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBDepartment
     */
    private $tbdepartment;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser
     */
    private $tbsystemuser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tbsystemusers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tbdepartments = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set DeleteFlug
     *
     * @param boolean $deleteFlug
     * @return TBSystemUser
     */
    public function setDeleteFlug($deleteFlug)
    {
        $this->DeleteFlug = $deleteFlug;
    
        return $this;
    }

    /**
     * Get DeleteFlug
     *
     * @return boolean 
     */
    public function getDeleteFlug()
    {
        return $this->DeleteFlug;
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
     * Add tbsystemusers
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tbsystemusers
     * @return TBSystemUser
     */
    public function addTbsystemuser(\Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tbsystemusers)
    {
        $this->tbsystemusers[] = $tbsystemusers;
    
        return $this;
    }

    /**
     * Remove tbsystemusers
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tbsystemusers
     */
    public function removeTbsystemuser(\Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tbsystemusers)
    {
        $this->tbsystemusers->removeElement($tbsystemusers);
    }

    /**
     * Get tbsystemusers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTbsystemusers()
    {
        return $this->tbsystemusers;
    }

    /**
     * Add tbdepartments
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBDepartment $tbdepartments
     * @return TBSystemUser
     */
    public function addTbdepartment(\Arte\PCMS\BizlogicBundle\Entity\TBDepartment $tbdepartments)
    {
        $this->tbdepartments[] = $tbdepartments;
    
        return $this;
    }

    /**
     * Remove tbdepartments
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBDepartment $tbdepartments
     */
    public function removeTbdepartment(\Arte\PCMS\BizlogicBundle\Entity\TBDepartment $tbdepartments)
    {
        $this->tbdepartments->removeElement($tbdepartments);
    }

    /**
     * Get tbdepartments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTbdepartments()
    {
        return $this->tbdepartments;
    }

    /**
     * Set tbdepartment
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBDepartment $tbdepartment
     * @return TBSystemUser
     */
    public function setTbdepartment(\Arte\PCMS\BizlogicBundle\Entity\TBDepartment $tbdepartment = null)
    {
        $this->tbdepartment = $tbdepartment;
    
        return $this;
    }

    /**
     * Get tbdepartment
     *
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBDepartment 
     */
    public function getTbdepartment()
    {
        return $this->tbdepartment;
    }

    /**
     * Set tbsystemuser
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tbsystemuser
     * @return TBSystemUser
     */
    public function setTbsystemuser(\Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tbsystemuser = null)
    {
        $this->tbsystemuser = $tbsystemuser;
    
        return $this;
    }

    /**
     * Get tbsystemuser
     *
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser 
     */
    public function getTbsystemuser()
    {
        return $this->tbsystemuser;
    }
}