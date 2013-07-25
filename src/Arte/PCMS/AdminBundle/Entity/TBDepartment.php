<?php

namespace Arte\PCMS\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TBDepartment
 */
class TBDepartment
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
     * @var integer
     */
    private $SortNo;

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
    private $TBSystemUsersDepartmentId;

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
        $this->TBSystemUsersDepartmentId = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return TBDepartment
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
     * Set SortNo
     *
     * @param integer $sortNo
     * @return TBDepartment
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
     * @return TBDepartment
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
     * @return TBDepartment
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
     * @return TBDepartment
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
     * @return TBDepartment
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
     * @return TBDepartment
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
     * Add TBSystemUsersDepartmentId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUsersDepartmentId
     * @return TBDepartment
     */
    public function addTBSystemUsersDepartmentId(\Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUsersDepartmentId)
    {
        $this->TBSystemUsersDepartmentId[] = $tBSystemUsersDepartmentId;
    
        return $this;
    }

    /**
     * Remove TBSystemUsersDepartmentId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUsersDepartmentId
     */
    public function removeTBSystemUsersDepartmentId(\Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUsersDepartmentId)
    {
        $this->TBSystemUsersDepartmentId->removeElement($tBSystemUsersDepartmentId);
    }

    /**
     * Get TBSystemUsersDepartmentId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTBSystemUsersDepartmentId()
    {
        return $this->TBSystemUsersDepartmentId;
    }

    /**
     * Set TBSystemUserUpdatedUserId
     *
     * @param \Arte\PCMS\AdminBundle\Entity\TBSystemUser $tBSystemUserUpdatedUserId
     * @return TBDepartment
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
     * @return TBDepartment
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
