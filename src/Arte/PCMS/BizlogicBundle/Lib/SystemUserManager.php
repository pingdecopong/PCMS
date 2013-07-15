<?php
/**
 * ユーザー管理クラス
 */

namespace Arte\PCMS\BizlogicBundle\Lib;


use Arte\PCMS\BizlogicBundle\Entity\TBSystemUser;
use Doctrine\ORM\EntityManager;

class SystemUserManager {

    private $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $id
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser
     * @throws \Exception
     */
    public function getTbSystemUser($id)
    {
        try {
            $queryBuilder = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')
                ->createQueryBuilder('u')
                ->leftJoin('u.tbdepartment', 'd')
                ->select(array('u', 'd'))
                ->andWhere('u.id = :id')
                ->andWhere('u.DeleteFlug = :DeleteFlug')
                ->setParameter('id', $id)
                ->setParameter('DeleteFlug', false)
                ->getQuery();

            $tbSystemUser = $queryBuilder->getSingleResult();

        }catch(\Exception $e){
            throw $e;
        }
        return $tbSystemUser;
    }

    public function createSystemUser(TBSystemUser $systemUser)
    {
        //ユーザー情報取得
        $createSystemUser = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);

        try {
            $systemUser->setSalt('aaa');
            $systemUser->setPassword('bbb');
            $systemUser->setActive(true);
            $systemUser->setDeleteFlug(false);
            $systemUser->setTbsystemuser($createSystemUser);
            $systemUser->setCreatedDatetime(new \DateTime());

            $this->em->persist($systemUser);
            $this->em->flush();

        }catch (\Exception $e){
            throw $e;
        }

        return $systemUser;
    }

    public function updateSystemUser(TBSystemUser $systemUser)
    {
        //ユーザー情報取得
        $createSystemUser = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);

        try {

            $systemUser->setTbsystemuser($createSystemUser);
            $systemUser->setUpdatedDatetime(new \DateTime());

            $this->em->persist($systemUser);
            $this->em->flush();

        }catch (\Exception $e){
            throw $e;
        }

        return $systemUser;
    }

    public function deleteSystemUser(TBSystemUser $systemUser)
    {
        //ユーザー情報取得
//        $createSystemUser = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);

        try {

            $systemUser->setDeleteFlug(true);

            $this->em->persist($systemUser);
            $this->em->flush();

        }catch (\Exception $e){
            throw $e;
        }

        return $systemUser;
    }

}