<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hirashimafumitake
 * Date: 2013/07/16
 * Time: 5:37
 * To change this template use File | Settings | File Templates.
 */

namespace Arte\PCMS\BizlogicBundle\DataFixtures\ORM;


use Arte\PCMS\BizlogicBundle\Entity\TBDepartment;
use Arte\PCMS\BizlogicBundle\Entity\TBSystemUser;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSystemUserData implements FixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        //Department
        $createdDateTime = new \DateTime("2012-01-01 0:0:0");
        $departments = array();
        for($i=1; $i<=5; $i++)
        {
            $tbDepartment = new TBDepartment();
            $tbDepartment->setName('部署'.sprintf('%02d', $i));
            $tbDepartment->setSortNo($i);
            $tbDepartment->setDeleteFlug(false);
            $tbDepartment->setCreatedDatetime($createdDateTime);

            $manager->persist($tbDepartment);
            $manager->flush();
            $departments[] = $tbDepartment;

            $createdDateTime->modify('+1 day');
        }

        //SystemUser
        $createdDateTime = new \DateTime("2012-01-01 0:0:0");
        $lastLoginDateTime = new \DateTime("2013-01-01 0:0:0");
        for($i=1; $i<=10; $i++)
        {
            $tbSystemUser = new TBSystemUser();
            $tbSystemUser->setLoginId('test'.sprintf('%03d', $i));
            $tbSystemUser->setSalt('a');
            $tbSystemUser->setPassword('a');
            $tbSystemUser->setActive(true);
            $tbSystemUser->setSystemRoleId(1);
            $tbSystemUser->setDisplayName('てすと'.sprintf('%03d', $i));
            $tbSystemUser->setDisplayNameKana('テスト'.sprintf('%03d', $i));
            $tbSystemUser->setNickName('t'.sprintf('%03d', $i));
            $tbSystemUser->setMailAddress('test'.sprintf('%03d', $i).'@test.com');
            $tbSystemUser->setTbdepartment($departments[0]);
            $tbSystemUser->setLastLoginDatetime($lastLoginDateTime);
            $tbSystemUser->setDeleteFlug(false);
            $tbSystemUser->setCreatedDatetime($createdDateTime);

            $manager->persist($tbSystemUser);
            $manager->flush();

            $createdDateTime->modify('+1 day');
            $lastLoginDateTime->modify('+1 day');
        }
    }
}