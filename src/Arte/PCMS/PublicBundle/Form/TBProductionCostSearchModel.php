<?php

namespace Arte\PCMS\PublicBundle\Form;

use Doctrine\ORM\Mapping as ORM;

/**
 * TBProductionCostSearchModel
 */
class TBProductionCostSearchModel
{
    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster
     */
    private $TBProjectMaster;
    /**
     * @var integer
     */
    private $Status;
    /**
     * @var \DateTime
     */
    private $PeriodStart;
    /**
     * @var \DateTime
     */
    private $PeriodEnd;
    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser
     */
    private $TBSystemUser;

    /**
     * @param \DateTime $PeriodEnd
     */
    public function setPeriodEnd($PeriodEnd)
    {
        $this->PeriodEnd = $PeriodEnd;
    }

    /**
     * @return \DateTime
     */
    public function getPeriodEnd()
    {
        return $this->PeriodEnd;
    }

    /**
     * @param \DateTime $PeriodStart
     */
    public function setPeriodStart($PeriodStart)
    {
        $this->PeriodStart = $PeriodStart;
    }

    /**
     * @return \DateTime
     */
    public function getPeriodStart()
    {
        return $this->PeriodStart;
    }

    /**
     * @param int $Status
     */
    public function setStatus($Status)
    {
        $this->Status = $Status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->Status;
    }

    /**
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster $TBProjectMaster
     */
    public function setTBProjectMaster($TBProjectMaster)
    {
        $this->TBProjectMaster = $TBProjectMaster;
    }

    /**
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster
     */
    public function getTBProjectMaster()
    {
        return $this->TBProjectMaster;
    }

    /**
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $TBSystemUser
     */
    public function setTBSystemUser($TBSystemUser)
    {
        $this->TBSystemUser = $TBSystemUser;
    }

    /**
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser
     */
    public function getTBSystemUser()
    {
        return $this->TBSystemUser;
    }

}
