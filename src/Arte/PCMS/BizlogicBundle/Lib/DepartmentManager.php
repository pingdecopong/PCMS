<?php
/**
 * 部署管理クラス
 */

namespace Arte\PCMS\BizlogicBundle\Lib;


use Doctrine\ORM\EntityManager;

class DepartmentManager {

    private $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

}