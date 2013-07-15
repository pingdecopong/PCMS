<?php
/**
 * リスト検索用モデル
 */

namespace Arte\PCMS\AdminBundle\Form\SystemUser;

use Symfony\Component\Validator\Constraints as Assert;

class ListSearchFormModel {
    /**
     * @var string
     * @Assert\Length(
     *      min = "2",
     *      max = "10"
     * )
     */
    private $loginId;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var string
     */
    private $mailAddress;

    /**
     * @var
     */
    private $department;

    /**
     * @param mixed $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $loginId
     */
    public function setLoginId($loginId)
    {
        $this->loginId = $loginId;
    }

    /**
     * @return string
     */
    public function getLoginId()
    {
        return $this->loginId;
    }

    /**
     * @param string $mailAddress
     */
    public function setMailAddress($mailAddress)
    {
        $this->mailAddress = $mailAddress;
    }

    /**
     * @return string
     */
    public function getMailAddress()
    {
        return $this->mailAddress;
    }

}