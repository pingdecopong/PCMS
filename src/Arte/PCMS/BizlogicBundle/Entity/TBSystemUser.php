<?php

namespace Arte\PCMS\BizlogicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Arte\PCMS\BizlogicBundle\Validator\Constraints as CustomAssert;

//use Symfony\Component\Validator\ExecutionContext;


/**
 * TBSystemUser
 * @Assert\Callback(methods={"isFormPasswordEqualValid"}, groups={"newform", "editform"})
 */
class TBSystemUser implements UserInterface, \Serializable
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     * @Assert\Type(type="string", groups={"db"})
     * @Assert\NotBlank(groups={"form", "db"})
     * @Assert\NotNull(groups={"db"})
     * @Assert\Length(min = "6", max="40", groups={"form", "db"})
     * @CustomAssert\ExistsUsername(groups={"newform"})
     */
    private $Username;

    /**
     * @var string
     * @Assert\NotBlank(groups={"db"})
     * @Assert\NotNull(groups={"db"})
     * @Assert\Length(min = "8", groups={"db"})
     */
    private $Salt;

    /**
     * @var string
     * @Assert\NotBlank(groups={"db"})
     * @Assert\NotNull(groups={"db"})
     * @Assert\Length(min = "8", groups={"db"})
     */
    private $Password;

    /**
     * @var boolean
     * @Assert\NotNull(groups={"db"})
     */
    private $Active;

    /**
     * @var integer
     * @Assert\Type(type="int", groups={"db"})
     * @Assert\NotBlank(groups={"form", "db"})
     * @Assert\NotNull(groups={"db"})
     * @Assert\Choice(choices = {1, 2}, groups={"form", "db"})
     */
    private $SystemRoleId;

    /**
     * @var string
     * @Assert\Type(type="string", groups={"db"})
     * @Assert\NotBlank(groups={"form", "db"})
     * @Assert\NotNull(groups={"db"})
     * @Assert\Length(max="50", groups={"form", "db"})
     */
    private $DisplayName;

    /**
     * @var string
     * @Assert\Type(type="string", groups={"db"})
     * @Assert\NotBlank(groups={"form", "db"})
     * @Assert\NotNull(groups={"db"})
     * @Assert\Length(max="50", groups={"form", "db"})
     */
    private $DisplayNameKana;

    /**
     * @var string
     * @Assert\Type(type="string", groups={"db"})
     * @Assert\NotBlank(groups={"form", "db"})
     * @Assert\NotNull(groups={"db"})
     * @Assert\Length(max="10", groups={"form", "db"})
     */
    private $NickName;

    /**
     * @var string
     * @Assert\Type(type="string", groups={"db"})
     * @Assert\NotBlank(groups={"form", "db"})
     * @Assert\NotNull(groups={"db"})
     * @Assert\Length(max="100", groups={"form", "db"})
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
     * @Assert\NotNull(groups={"db"})
     */
    private $DeleteFlag;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBProjectUsersSystemUserId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBProjectMastersManagerId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $VProjectUsersSystemUserId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $VProjectViewsManagerId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $TBProductionCostsSystemUserId;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBDepartment
     * @Assert\NotBlank(groups={"form", "db"})
     * @Assert\NotNull(groups={"db"})
     */
    private $TBDepartmentDepartmentId;

    /**
     * @var string フォーム入力用 パスワード
     * @Assert\NotBlank(groups={"newform", "editform"})
     * @Assert\Length(min = "8", max="50", groups={"newform", "editform"})
     */
    private $FormPassword;

    /**
     * @var string フォーム入力用 パスワード確認
     * @Assert\NotBlank(groups={"newform", "editform"})
     */
    private $FormPasswordConfirm;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Salt = md5(uniqid(null, true));

        $this->TBProjectUsersSystemUserId = new \Doctrine\Common\Collections\ArrayCollection();
        $this->TBProjectMastersManagerId = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VProjectUsersSystemUserId = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VProjectViewsManagerId = new \Doctrine\Common\Collections\ArrayCollection();
        $this->TBProductionCostsSystemUserId = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getRoles()
    {
        $role = array();
        switch($this->getSystemRoleId()){
            case 1:
                $role[] = 'ROLE_ADMIN';
                break;
            case 2:
                $role[] = 'ROLE_USER';
                break;
        }

        return $role;
    }
    public function eraseCredentials()
    {
    }
    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            ) = unserialize($serialized);
    }

    public function getName()
    {
        return $this->DisplayName;
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
     * Set Username
     *
     * @param string $username
     * @return TBSystemUser
     */
    public function setUsername($username)
    {
        $this->Username = $username;
    
        return $this;
    }

    /**
     * Get Username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->Username;
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
     * Add TBProjectMastersManagerId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $tBProjectMastersManagerId
     * @return TBSystemUser
     */
    public function addTBProjectMastersManagerId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $tBProjectMastersManagerId)
    {
        $this->TBProjectMastersManagerId[] = $tBProjectMastersManagerId;
    
        return $this;
    }

    /**
     * Remove TBProjectMastersManagerId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $tBProjectMastersManagerId
     */
    public function removeTBProjectMastersManagerId(\Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $tBProjectMastersManagerId)
    {
        $this->TBProjectMastersManagerId->removeElement($tBProjectMastersManagerId);
    }

    /**
     * Get TBProjectMastersManagerId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTBProjectMastersManagerId()
    {
        return $this->TBProjectMastersManagerId;
    }

    /**
     * Add VProjectUsersSystemUserId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\VProjectUser $vProjectUsersSystemUserId
     * @return TBSystemUser
     */
    public function addVProjectUsersSystemUserId(\Arte\PCMS\BizlogicBundle\Entity\VProjectUser $vProjectUsersSystemUserId)
    {
        $this->VProjectUsersSystemUserId[] = $vProjectUsersSystemUserId;
    
        return $this;
    }

    /**
     * Remove VProjectUsersSystemUserId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\VProjectUser $vProjectUsersSystemUserId
     */
    public function removeVProjectUsersSystemUserId(\Arte\PCMS\BizlogicBundle\Entity\VProjectUser $vProjectUsersSystemUserId)
    {
        $this->VProjectUsersSystemUserId->removeElement($vProjectUsersSystemUserId);
    }

    /**
     * Get VProjectUsersSystemUserId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVProjectUsersSystemUserId()
    {
        return $this->VProjectUsersSystemUserId;
    }

    /**
     * Add VProjectViewsManagerId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\VProjectView $vProjectViewsManagerId
     * @return TBSystemUser
     */
    public function addVProjectViewsManagerId(\Arte\PCMS\BizlogicBundle\Entity\VProjectView $vProjectViewsManagerId)
    {
        $this->VProjectViewsManagerId[] = $vProjectViewsManagerId;
    
        return $this;
    }

    /**
     * Remove VProjectViewsManagerId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\VProjectView $vProjectViewsManagerId
     */
    public function removeVProjectViewsManagerId(\Arte\PCMS\BizlogicBundle\Entity\VProjectView $vProjectViewsManagerId)
    {
        $this->VProjectViewsManagerId->removeElement($vProjectViewsManagerId);
    }

    /**
     * Get VProjectViewsManagerId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVProjectViewsManagerId()
    {
        return $this->VProjectViewsManagerId;
    }

    /**
     * Add TBProductionCostsSystemUserId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProductionCost $tBProductionCostsSystemUserId
     * @return TBSystemUser
     */
    public function addTBProductionCostsSystemUserId(\Arte\PCMS\BizlogicBundle\Entity\TBProductionCost $tBProductionCostsSystemUserId)
    {
        $this->TBProductionCostsSystemUserId[] = $tBProductionCostsSystemUserId;
    
        return $this;
    }

    /**
     * Remove TBProductionCostsSystemUserId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProductionCost $tBProductionCostsSystemUserId
     */
    public function removeTBProductionCostsSystemUserId(\Arte\PCMS\BizlogicBundle\Entity\TBProductionCost $tBProductionCostsSystemUserId)
    {
        $this->TBProductionCostsSystemUserId->removeElement($tBProductionCostsSystemUserId);
    }

    /**
     * Get TBProductionCostsSystemUserId
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTBProductionCostsSystemUserId()
    {
        return $this->TBProductionCostsSystemUserId;
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

    /**
     * @param string $FormPassword
     */
    public function setFormPassword($FormPassword)
    {
        $this->FormPassword = $FormPassword;
    }

    /**
     * @return string
     */
    public function getFormPassword()
    {
        return $this->FormPassword;
    }

    /**
     * @param string $FormPasswordConfirm
     */
    public function setFormPasswordConfirm($FormPasswordConfirm)
    {
        $this->FormPasswordConfirm = $FormPasswordConfirm;
    }

    /**
     * @return string
     */
    public function getFormPasswordConfirm()
    {
        return $this->FormPasswordConfirm;
    }

//    /**
//     * パスワードとパスワード確認が同じ文字列か確認する
//     * @Assert\True(message = "「パスワード」と「パスワード確認」が同じではありません。", groups={"form"})
//     */
//    public function isFormPasswordConfirmValid()
//    {
//        return $this->FormPassword == $this->FormPasswordConfirm;
//    }

    /**
     * パスワードとパスワード確認が同じ文字列か確認する
     * @param ExecutionContextInterface $context
     */
    public function isFormPasswordEqualValid(ExecutionContextInterface $context)
    {
        if ($this->FormPassword !== $this->FormPasswordConfirm)
        {
            $context->addViolationAt('FormPasswordConfirm', '「パスワード」と「パスワード確認」が同じではありません。', array(), null);
        }
    }
}