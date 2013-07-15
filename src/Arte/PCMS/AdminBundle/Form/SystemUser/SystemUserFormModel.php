<?php
/**
 * システムユーザー　登録画面用モデル
 */

namespace Arte\PCMS\AdminBundle\Form\SystemUser;


use Symfony\Component\Validator\Constraints as Assert;

class SystemUserFormModel {

    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="40")
     */
    private $loginId;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="40")
     */
    private $displayName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="40")
     */
    private $displayNameKana;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="10")
     */
    private $nickName;

    /**
     * @var
     */
    private $department;

    /**
     * @var string メールアドレス
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="100")
     */
    private $mailAddress;

    /**
     * @var int 権限番号
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="numeric")
     */
    private $systemRoleId;

    /**
     * @var string
     */
    private $buttonAction;

    /**
     * @param string $buttonAction
     */
    public function setButtonAction($buttonAction)
    {
        $this->buttonAction = $buttonAction;
    }

    /**
     * @return string
     */
    public function getButtonAction()
    {
        return $this->buttonAction;
    }

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
     * @param string $displayNameKana
     */
    public function setDisplayNameKana($displayNameKana)
    {
        $this->displayNameKana = $displayNameKana;
    }

    /**
     * @return string
     */
    public function getDisplayNameKana()
    {
        return $this->displayNameKana;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @param string $nickName
     */
    public function setNickName($nickName)
    {
        $this->nickName = $nickName;
    }

    /**
     * @return string
     */
    public function getNickName()
    {
        return $this->nickName;
    }

    /**
     * @param int $systemRoleId
     */
    public function setSystemRoleId($systemRoleId)
    {
        $this->systemRoleId = $systemRoleId;
    }

    /**
     * @return int
     */
    public function getSystemRoleId()
    {
        return $this->systemRoleId;
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

}