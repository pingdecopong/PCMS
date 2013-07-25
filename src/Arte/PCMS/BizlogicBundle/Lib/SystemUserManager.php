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
     * ユーザー取得
     * @param $id
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser
     * @throws \Exception
     */
    public function getTbSystemUser($id)
    {
        try {
            $queryBuilder = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')
                ->createQueryBuilder('u')
                ->leftJoin('u.TBDepartmentDepartmentId', 'd')
                ->select(array('u', 'd'))
                ->andWhere('u.id = :id')
                ->andWhere('u.DeleteFlag = :DeleteFlag')
                ->setParameter('id', $id)
                ->setParameter('DeleteFlag', false)
                ->getQuery();

            $tbSystemUser = $queryBuilder->getSingleResult();

        }catch(\Exception $e){
            throw $e;
        }
        return $tbSystemUser;
    }

    /**
     * ユーザー新規作成
     * @param TBSystemUser $systemUser
     * @return TBSystemUser
     * @throws \Exception
     * @TODO CreatedUserIdの実装
     */
    public function createSystemUser(TBSystemUser $systemUser)
    {
        //ユーザー情報取得
        $createSystemUser = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);

        try {
            $systemUser->setSalt('aaa');
            $systemUser->setPassword('bbb');
            $systemUser->setActive(true);
            $systemUser->setDeleteFlag(false);
            $systemUser->setTBSystemUserCreatedUserId($createSystemUser);
//            $systemUser->setTbsystemuser($createSystemUser);
            $systemUser->setCreatedDatetime(new \DateTime());

            $this->em->persist($systemUser);
            $this->em->flush();

        }catch (\Exception $e){
            throw $e;
        }

        return $systemUser;
    }

    /**
     * ユーザー更新
     * @param TBSystemUser $systemUser
     * @return TBSystemUser
     * @throws \Exception
     */
    public function updateSystemUser(TBSystemUser $systemUser)
    {
        //ユーザー情報取得
        $createSystemUser = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);

        try {

//            $systemUser->setTbsystemuser($createSystemUser);
            $systemUser->setTBSystemUserCreatedUserId($createSystemUser);
            $systemUser->setUpdatedDatetime(new \DateTime());

            $this->em->persist($systemUser);
            $this->em->flush();

        }catch (\Exception $e){
            throw $e;
        }

        return $systemUser;
    }

    /**
     * ユーザー削除
     * @param TBSystemUser $systemUser
     * @return TBSystemUser
     * @throws \Exception
     */
    public function deleteSystemUser(TBSystemUser $systemUser)
    {
        //ユーザー情報取得
//        $createSystemUser = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);

        try {

            $systemUser->setDeleteFlag(true);

            $this->em->persist($systemUser);
            $this->em->flush();

        }catch (\Exception $e){
            throw $e;
        }

        return $systemUser;
    }

}