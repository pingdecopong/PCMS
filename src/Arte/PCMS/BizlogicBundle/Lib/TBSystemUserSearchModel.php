<?php

namespace Arte\PCMS\BizlogicBundle\Lib;

use Arte\PCMS\BizlogicBundle\Entity\TBDepartment;
use Symfony\Component\Validator\Constraints as Assert;

class TBSystemUserSearchModel {

    /**
     * @var string
     * @Assert\Length(
     *      max = "40"
     * )
     */
    private $Username;

    /**
     * @var boolean
     */
    private $Active;

    /**
     * @var string
     * @Assert\Length(
     *      max = "50"
     * )
     */
    private $DisplayName;

    /**
     * @var string
     * @Assert\Length(
     *      max = "100"
     * )
     */
    private $MailAddress;

    /**
     * @var TBDepartment
     */
    private $TBDepartmentDepartmentId;

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * @param boolean $Active
     */
    public function setActive($Active)
    {
        $this->Active = $Active;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->Active;
    }

    /**
     * @param string $DisplayName
     */
    public function setDisplayName($DisplayName)
    {
        $this->DisplayName = $DisplayName;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->DisplayName;
    }

    /**
     * @param string $Username
     */
    public function setUsername($Username)
    {
        $this->Username = $Username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->Username;
    }

    /**
     * @param string $MailAddress
     */
    public function setMailAddress($MailAddress)
    {
        $this->MailAddress = $MailAddress;
    }

    /**
     * @return string
     */
    public function getMailAddress()
    {
        return $this->MailAddress;
    }

    /**
     * Set TBDepartmentDepartmentId
     *
     * @param TBDepartment $tBDepartmentDepartmentId
     * @return TBSystemUserSearchModel
     */
    public function setTBDepartmentDepartmentId(TBDepartment $tBDepartmentDepartmentId = null)
    {
        $this->TBDepartmentDepartmentId = $tBDepartmentDepartmentId;

        return $this;
    }

    /**
     * Get TBDepartmentDepartmentId
     *
     * @return TBDepartment
     */
    public function getTBDepartmentDepartmentId()
    {
        return $this->TBDepartmentDepartmentId;
    }

}