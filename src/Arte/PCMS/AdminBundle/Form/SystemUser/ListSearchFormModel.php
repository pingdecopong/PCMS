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
    private $LoginId;

    /**
     * @var string
     */
    private $DisplayName;

    /**
     * @var string
     */
    private $MailAddress;

    /**
     * @var
     */
    private $Department;

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
     * @param string $LoginId
     */
    public function setLoginId($LoginId)
    {
        $this->LoginId = $LoginId;
    }

    /**
     * @return string
     */
    public function getLoginId()
    {
        return $this->LoginId;
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
     * @param mixed $Department
     */
    public function setDepartment($Department)
    {
        $this->Department = $Department;
    }

    /**
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->Department;
    }

}