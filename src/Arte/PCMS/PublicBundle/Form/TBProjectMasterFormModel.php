<?php
namespace Arte\PCMS\PublicBundle\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TBProjectMaster
 */
class TBProjectMasterFormModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string　案件名
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = "50"
     * )
     */
    private $Name;

    /**
     * @var integer　状態
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {1, 2, 3})
     */
    private $Status;

    /**
     * @var string　説明
     * @Assert\Length(
     *      max = "200"
     * )
     */
    private $Explanation;

    /**
     * @var \DateTime　開始日
     * @Assert\Date()
     */
    private $PeriodStart;

    /**
     * @var \DateTime　終了日
     * @Assert\Date()
     */
    private $PeriodEnd;

    /**
     * @var string　見積ファイルパス
     */
    private $EstimateFilePath;

    /**
     * @var string　スケジュールファイルパス
     */
    private $ScheduleFilePath;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBCustomer　顧客
     * @Assert\NotBlank()
     */
    private $TBCustomerCustomerId;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser　管理者
     * @Assert\NotBlank()
     */
    private $TBSystemUserManagerId;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $SubForms;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->SubForms = new ArrayCollection();
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
     * @return TBProjectMaster
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
     * Set Status
     *
     * @param integer $status
     * @return TBProjectMaster
     */
    public function setStatus($status)
    {
        $this->Status = $status;
    
        return $this;
    }

    /**
     * Get Status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->Status;
    }

    /**
     * Set Explanation
     *
     * @param string $explanation
     * @return TBProjectMaster
     */
    public function setExplanation($explanation)
    {
        $this->Explanation = $explanation;
    
        return $this;
    }

    /**
     * Get Explanation
     *
     * @return string 
     */
    public function getExplanation()
    {
        return $this->Explanation;
    }

    /**
     * Set PeriodStart
     *
     * @param \DateTime $periodStart
     * @return TBProjectMaster
     */
    public function setPeriodStart($periodStart)
    {
        $this->PeriodStart = $periodStart;
    
        return $this;
    }

    /**
     * Get PeriodStart
     *
     * @return \DateTime 
     */
    public function getPeriodStart()
    {
        return $this->PeriodStart;
    }

    /**
     * Set PeriodEnd
     *
     * @param \DateTime $periodEnd
     * @return TBProjectMaster
     */
    public function setPeriodEnd($periodEnd)
    {
        $this->PeriodEnd = $periodEnd;
    
        return $this;
    }

    /**
     * Get PeriodEnd
     *
     * @return \DateTime 
     */
    public function getPeriodEnd()
    {
        return $this->PeriodEnd;
    }

    /**
     * Set EstimateFilePath
     *
     * @param string $estimateFilePath
     * @return TBProjectMaster
     */
    public function setEstimateFilePath($estimateFilePath)
    {
        $this->EstimateFilePath = $estimateFilePath;
    
        return $this;
    }

    /**
     * Get EstimateFilePath
     *
     * @return string 
     */
    public function getEstimateFilePath()
    {
        return $this->EstimateFilePath;
    }

    /**
     * Set ScheduleFilePath
     *
     * @param string $scheduleFilePath
     * @return TBProjectMaster
     */
    public function setScheduleFilePath($scheduleFilePath)
    {
        $this->ScheduleFilePath = $scheduleFilePath;
    
        return $this;
    }

    /**
     * Get ScheduleFilePath
     *
     * @return string 
     */
    public function getScheduleFilePath()
    {
        return $this->ScheduleFilePath;
    }

    /**
     * Set TBCustomerCustomerId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBCustomer $tBCustomerCustomerId
     * @return TBProjectMaster
     */
    public function setTBCustomerCustomerId(\Arte\PCMS\BizlogicBundle\Entity\TBCustomer $tBCustomerCustomerId = null)
    {
        $this->TBCustomerCustomerId = $tBCustomerCustomerId;
    
        return $this;
    }

    /**
     * Get TBCustomerCustomerId
     *
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBCustomer 
     */
    public function getTBCustomerCustomerId()
    {
        return $this->TBCustomerCustomerId;
    }

    /**
     * Set TBSystemUserManagerId
     *
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tBSystemUserManagerId
     * @return TBProjectMaster
     */
    public function setTBSystemUserManagerId(\Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tBSystemUserManagerId = null)
    {
        $this->TBSystemUserManagerId = $tBSystemUserManagerId;
    
        return $this;
    }

    /**
     * Get TBSystemUserManagerId
     *
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser 
     */
    public function getTBSystemUserManagerId()
    {
        return $this->TBSystemUserManagerId;
    }

    public function addSubForm($Branch)
    {
        $this->SubForms->add($Branch);
    }

    public function removeSubForm($Branch)
    {
        $this->SubForms->removeElement($Branch);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubForms()
    {
        return $this->SubForms;
    }
}
