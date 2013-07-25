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
        $departments = array();

        ///1
        $tbDepartment = new TBDepartment();
        $tbDepartment->setName('部署01');
        $tbDepartment->setSortNo(1);
        $tbDepartment->setDeleteFlug(false);
        $tbDepartment->setCreatedDatetime(new \DateTime("2012-01-01 0:0:0"));
        $manager->persist($tbDepartment);
        $manager->flush();
        $departments[] = $tbDepartment;

        ///2
        $tbDepartment = new TBDepartment();
        $tbDepartment->setName('部署02');
        $tbDepartment->setSortNo(2);
        $tbDepartment->setDeleteFlug(true);
        $tbDepartment->setCreatedDatetime(new \DateTime("2012-01-02 0:0:0"));
        $manager->persist($tbDepartment);
        $manager->flush();
        $departments[] = $tbDepartment;

        ///3
        $tbDepartment = new TBDepartment();
        $tbDepartment->setName('部署03');
        $tbDepartment->setSortNo(3);
        $tbDepartment->setDeleteFlug(false);
        $tbDepartment->setCreatedDatetime(new \DateTime("2012-01-03 0:0:0"));
        $manager->persist($tbDepartment);
        $manager->flush();
        $departments[] = $tbDepartment;

        $createdDateTime = new \DateTime("2012-01-04 0:0:0");
        for($i=4; $i<=5; $i++)
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


        //SystemUser2
        ///1
        $tbSystemUser = new TBSystemUser();
        $tbSystemUser->setLoginId('test001');
        $tbSystemUser->setSalt('a');
        $tbSystemUser->setPassword('a');
        $tbSystemUser->setActive(true);
        $tbSystemUser->setSystemRoleId(1);
        $tbSystemUser->setDisplayName('てすと001');
        $tbSystemUser->setDisplayNameKana('テスト001');
        $tbSystemUser->setNickName('t001');
        $tbSystemUser->setMailAddress('test001@test.com');
        $tbSystemUser->setTbdepartment($departments[0]);
        $tbSystemUser->setLastLoginDatetime(new \DateTime("2012-07-01 0:0:0"));
        $tbSystemUser->setDeleteFlug(false);
        $tbSystemUser->setCreatedDatetime(new \DateTime("2012-01-01 0:0:0"));
        $manager->persist($tbSystemUser);
        $manager->flush();

        ///2 削除データ
        $tbSystemUser = new TBSystemUser();
        $tbSystemUser->setLoginId('test002');
        $tbSystemUser->setSalt('a');
        $tbSystemUser->setPassword('a');
        $tbSystemUser->setActive(true);
        $tbSystemUser->setSystemRoleId(1);
        $tbSystemUser->setDisplayName('てすと002');
        $tbSystemUser->setDisplayNameKana('テスト002');
        $tbSystemUser->setNickName('t002');
        $tbSystemUser->setMailAddress('test002@test.com');
        $tbSystemUser->setTbdepartment($departments[0]);
        $tbSystemUser->setLastLoginDatetime(new \DateTime("2012-07-02 0:0:0"));
        $tbSystemUser->setDeleteFlug(true);
        $tbSystemUser->setCreatedDatetime(new \DateTime("2012-01-02 0:0:0"));
        $manager->persist($tbSystemUser);
        $manager->flush();

        ///3 無効データ
        $tbSystemUser = new TBSystemUser();
        $tbSystemUser->setLoginId('test003');
        $tbSystemUser->setSalt('a');
        $tbSystemUser->setPassword('a');
        $tbSystemUser->setActive(false);
        $tbSystemUser->setSystemRoleId(1);
        $tbSystemUser->setDisplayName('てすと003');
        $tbSystemUser->setDisplayNameKana('テスト003');
        $tbSystemUser->setNickName('t003');
        $tbSystemUser->setMailAddress('test003@test.com');
        $tbSystemUser->setTbdepartment($departments[0]);
        $tbSystemUser->setLastLoginDatetime(new \DateTime("2012-07-03 0:0:0"));
        $tbSystemUser->setDeleteFlug(false);
        $tbSystemUser->setCreatedDatetime(new \DateTime("2012-01-03 0:0:0"));
        $manager->persist($tbSystemUser);
        $manager->flush();

        $lastLoginDateTime = new \DateTime("2013-07-04 0:0:0");
        $createdDateTime = new \DateTime("2012-01-04 0:0:0");
        for($i=4; $i<=10; $i++)
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
/*
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

        //SystemUser2
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
*/
}