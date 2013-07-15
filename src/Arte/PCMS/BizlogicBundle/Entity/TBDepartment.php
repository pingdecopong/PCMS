<?php

namespace Arte\PCMS\BizlogicBundle\Entity;

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
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser
     */
    private $tbsystemuser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tbsystemusers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set DeleteFlug
     *
     * @param boolean $deleteFlug
     * @return TBDepartment
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
     * Add tbsystemusers
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tbsystemusers
     * @return TBDepartment
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
     * Set tbsystemuser
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tbsystemuser
     * @return TBDepartment
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