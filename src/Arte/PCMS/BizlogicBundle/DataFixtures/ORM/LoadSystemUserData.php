<?php

namespace Arte\PCMS\BizlogicBundle\DataFixtures\ORM;


use Arte\PCMS\BizlogicBundle\Entity\TBCustomer;
use Arte\PCMS\BizlogicBundle\Entity\TBDepartment;
use Arte\PCMS\BizlogicBundle\Entity\TBProductionCost;
use Arte\PCMS\BizlogicBundle\Entity\TBProjectCostHierarchyMaster;
use Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster;
use Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster;
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
        $tbDepartment->setDeleteFlag(false);
        $manager->persist($tbDepartment);
        $manager->flush();
        $departments[0] = $tbDepartment;

        ///2
        $tbDepartment = new TBDepartment();
        $tbDepartment->setName('部署02');
        $tbDepartment->setSortNo(2);
        $tbDepartment->setDeleteFlag(true);
        $manager->persist($tbDepartment);
        $manager->flush();
        $departments[1] = $tbDepartment;

        ///3
        $tbDepartment = new TBDepartment();
        $tbDepartment->setName('部署03');
        $tbDepartment->setSortNo(3);
        $tbDepartment->setDeleteFlag(false);
        $manager->persist($tbDepartment);
        $manager->flush();
        $departments[2] = $tbDepartment;

        $createdDateTime = new \DateTime("2012-01-04 0:0:0");
        for($i=4; $i<=5; $i++)
        {
            $tbDepartment = new TBDepartment();
            $tbDepartment->setName('部署'.sprintf('%02d', $i));
            $tbDepartment->setSortNo($i);
            $tbDepartment->setDeleteFlag(false);

            $manager->persist($tbDepartment);
            $manager->flush();
            $departments[] = $tbDepartment;

            $createdDateTime->modify('+1 day');
        }


        //SystemUser2
        $systemUsers = array();
        ///1
        $tbSystemUser = new TBSystemUser();
        $tbSystemUser->setUsername('test001');
        $tbSystemUser->setSalt(null);
        $tbSystemUser->setPassword('a');
        $tbSystemUser->setActive(true);
        $tbSystemUser->setSystemRoleId(1);
        $tbSystemUser->setDisplayName('てすと001');
        $tbSystemUser->setDisplayNameKana('テスト001');
        $tbSystemUser->setNickName('t001');
        $tbSystemUser->setMailAddress('test001@test.com');
        $tbSystemUser->setTBDepartmentDepartmentId($departments[0]);
        $tbSystemUser->setLastLoginDatetime(new \DateTime("2012-07-01 0:0:0"));
        $tbSystemUser->setDeleteFlag(false);
        $manager->persist($tbSystemUser);
        $manager->flush();
        $systemUsers[0] = $tbSystemUser;

        ///2 削除データ
        $tbSystemUser = new TBSystemUser();
        $tbSystemUser->setUsername('test002');
        $tbSystemUser->setSalt(null);
        $tbSystemUser->setPassword('a');
        $tbSystemUser->setActive(true);
        $tbSystemUser->setSystemRoleId(1);
        $tbSystemUser->setDisplayName('てすと002');
        $tbSystemUser->setDisplayNameKana('テスト002');
        $tbSystemUser->setNickName('t002');
        $tbSystemUser->setMailAddress('test002@test.com');
        $tbSystemUser->setTBDepartmentDepartmentId($departments[0]);
        $tbSystemUser->setLastLoginDatetime(new \DateTime("2012-07-02 0:0:0"));
        $tbSystemUser->setDeleteFlag(true);
        $manager->persist($tbSystemUser);
        $manager->flush();
        $systemUsers[1] = $tbSystemUser;

        ///3 無効データ
        $tbSystemUser = new TBSystemUser();
        $tbSystemUser->setUsername('test003');
        $tbSystemUser->setSalt(null);
        $tbSystemUser->setPassword('a');
        $tbSystemUser->setActive(false);
        $tbSystemUser->setSystemRoleId(1);
        $tbSystemUser->setDisplayName('てすと003');
        $tbSystemUser->setDisplayNameKana('テスト003');
        $tbSystemUser->setNickName('t003');
        $tbSystemUser->setMailAddress('test003@test.com');
        $tbSystemUser->setTBDepartmentDepartmentId($departments[0]);
        $tbSystemUser->setLastLoginDatetime(new \DateTime("2012-07-03 0:0:0"));
        $tbSystemUser->setDeleteFlag(false);
        $manager->persist($tbSystemUser);
        $manager->flush();
        $systemUsers[2] = $tbSystemUser;

        $lastLoginDateTime = new \DateTime("2013-07-04 0:0:0");
        $createdDateTime = new \DateTime("2012-01-04 0:0:0");
        for($i=4; $i<=10; $i++)
        {
            $tbSystemUser = new TBSystemUser();
            $tbSystemUser->setUsername('test'.sprintf('%03d', $i));
            $tbSystemUser->setSalt(null);
            $tbSystemUser->setPassword('a');
            $tbSystemUser->setActive(true);
            $tbSystemUser->setSystemRoleId(1);
            $tbSystemUser->setDisplayName('てすと'.sprintf('%03d', $i));
            $tbSystemUser->setDisplayNameKana('テスト'.sprintf('%03d', $i));
            $tbSystemUser->setNickName('t'.sprintf('%03d', $i));
            $tbSystemUser->setMailAddress('test'.sprintf('%03d', $i).'@test.com');
            $tbSystemUser->setTBDepartmentDepartmentId($departments[0]);
            $tbSystemUser->setLastLoginDatetime($lastLoginDateTime);
            $tbSystemUser->setDeleteFlag(false);

            $manager->persist($tbSystemUser);
            $manager->flush();
            $systemUsers[] = $tbSystemUser;

            $createdDateTime->modify('+1 day');
            $lastLoginDateTime->modify('+1 day');
        }

        //TBCustomer
        $customers = array();
        ///1
        $tbCustomer = new TBCustomer();
        $tbCustomer->setName('顧客1');
        $tbCustomer->setDeleteFlag(false);
        $manager->persist($tbCustomer);
        $manager->flush();
        $customers[0] = $tbCustomer;

        ///2
        $tbCustomer = new TBCustomer();
        $tbCustomer->setName('顧客2');
        $tbCustomer->setDeleteFlag(true);
        $manager->persist($tbCustomer);
        $manager->flush();
        $customers[1] = $tbCustomer;

        ///3
        $tbCustomer = new TBCustomer();
        $tbCustomer->setName('顧客3');
        $tbCustomer->setDeleteFlag(false);
        $manager->persist($tbCustomer);
        $manager->flush();
        $customers[2] = $tbCustomer;
/*
        //TBProject
        $projectMasters = array();
        ///1
        $tbprojectMaster = new TBProjectMaster();
        $tbprojectMaster->setName('案件1');
        $tbprojectMaster->setStatus(1);
        $tbprojectMaster->setExplanation('案件1説明');
        $tbprojectMaster->setTBCustomerCustomerId($customers[0]);
        $tbprojectMaster->setDeleteFlag(false);
        $projectStartDate = new \DateTime("2013-08-01 0:0:0");
        $tbprojectMaster->setPeriodStart($projectStartDate);
        $tbprojectMaster->setPeriodEnd($projectStartDate->modify('+5 day'));
        $tbprojectMaster->setTBSystemUserManagerId($systemUsers[0]);
        $tbprojectMaster->setEstimateFilePath('estimate1');
        $tbprojectMaster->setScheduleFilePath('schedule1');
        $manager->persist($tbprojectMaster);
        $manager->flush();
        $projectMasters[0] = $tbprojectMaster;

        ///2
        $tbprojectMaster = new TBProjectMaster();
        $tbprojectMaster->setName('案件2');
        $tbprojectMaster->setStatus(1);
        $tbprojectMaster->setExplanation('案件2説明');
        $tbprojectMaster->setTBCustomerCustomerId($customers[0]);
        $tbprojectMaster->setDeleteFlag(false);
        $projectStartDate = new \DateTime("2013-08-07 0:0:0");
        $tbprojectMaster->setPeriodStart($projectStartDate);
        $tbprojectMaster->setPeriodEnd($projectStartDate->modify('+5 day'));
        $tbprojectMaster->setTBSystemUserManagerId($systemUsers[0]);
        $tbprojectMaster->setEstimateFilePath('estimate2');
        $tbprojectMaster->setScheduleFilePath('schedule2');
        $manager->persist($tbprojectMaster);
        $manager->flush();
        $projectMasters[1] = $tbprojectMaster;

        //TBProjectCostMaster
        $projectcostmasters = array();
        ///1
        $tbProjectCostMaster = new TBProjectCostMaster();
        $tbProjectCostMaster->setTBProjectMasterProjectMasterId($projectMasters[0]);
        $tbProjectCostMaster->setName('製造1');
        $tbProjectCostMaster->setCost(60);
        $tbProjectCostMaster->setSortNo(1);
        $tbProjectCostMaster->setDeleteFlag(false);
        $tbProjectCostMaster->setHierarchyPath('');
        $manager->persist($tbProjectCostMaster);
        $manager->flush();
        $projectcostmasters[0] = $tbProjectCostMaster;

        ///2
        $tbProjectCostMaster = new TBProjectCostMaster();
        $tbProjectCostMaster->setTBProjectMasterProjectMasterId($projectMasters[0]);
        $tbProjectCostMaster->setName('製造2');
        $tbProjectCostMaster->setCost(120);
        $tbProjectCostMaster->setSortNo(2);
        $tbProjectCostMaster->setDeleteFlag(false);
        $tbProjectCostMaster->setHierarchyPath('');
        $manager->persist($tbProjectCostMaster);
        $manager->flush();
        $projectcostmasters[1] = $tbProjectCostMaster;

        ///3
        $tbProjectCostMaster = new TBProjectCostMaster();
        $tbProjectCostMaster->setTBProjectMasterProjectMasterId($projectMasters[1]);
        $tbProjectCostMaster->setName('要件定義');
        $tbProjectCostMaster->setCost(400);
        $tbProjectCostMaster->setSortNo(3);
        $tbProjectCostMaster->setDeleteFlag(false);
        $tbProjectCostMaster->setHierarchyPath('');
        $manager->persist($tbProjectCostMaster);
        $manager->flush();
        $projectcostmasters[2] = $tbProjectCostMaster;

        //TBProductionCost
        $productionCosts = array();
        $workDate = new \DateTime("2013-07-01 0:0:0");
        ///1
        $tbProductionCost = new TBProductionCost();
        $tbProductionCost->setTBProjectCostMasterProjectCostMasterId($projectcostmasters[0]);
        $tbProductionCost->setTBSystemUserSystemUserId($systemUsers[0]);
        $tbProductionCost->setWorkDate($workDate);
        $tbProductionCost->setCost(30);
        $tbProductionCost->setNote('備考1');
        $tbProductionCost->setDeleteFlag(false);
        $manager->persist($tbProductionCost);
        $manager->flush();
        $productionCosts[0] = $tbProductionCost;

        ///2$createdDateTime->modify('+1 day');
        $tbProductionCost = new TBProductionCost();
        $tbProductionCost->setTBProjectCostMasterProjectCostMasterId($projectcostmasters[0]);
        $tbProductionCost->setTBSystemUserSystemUserId($systemUsers[0]);
        $createdDateTime->modify('+1 day');
        $tbProductionCost->setWorkDate($workDate);
        $tbProductionCost->setCost(15);
        $tbProductionCost->setNote('備考2');
        $tbProductionCost->setDeleteFlag(false);
        $manager->persist($tbProductionCost);
        $manager->flush();
        $productionCosts[1] = $tbProductionCost;

        ///3
        $tbProductionCost = new TBProductionCost();
        $tbProductionCost->setTBProjectCostMasterProjectCostMasterId($projectcostmasters[1]);
        $tbProductionCost->setTBSystemUserSystemUserId($systemUsers[0]);
        $createdDateTime->modify('+1 day');
        $tbProductionCost->setWorkDate($workDate);
        $tbProductionCost->setCost(60);
        $tbProductionCost->setNote('備考3');
        $tbProductionCost->setDeleteFlag(false);
        $manager->persist($tbProductionCost);
        $manager->flush();
        $productionCosts[2] = $tbProductionCost;
*/


        /**
         *                      cost        realCost
         *  納品                  480
         *  製造
         *      一般側
         *          申請管理        4320
         *          ログイン        960
         *      管理側
         *          ユーザー管理  2880        780
         *          顧客管理        1920
         *  設計                  2400
         *  要件定義                1440        600
         */

        //プロジェクト
        $projectMasters = array();
        $projectCostHierarchyMasters = array();
        $projectCostMasters = array();
        $productionCosts = array();

        //案件マスタ
        $tbprojectMaster = new TBProjectMaster();
        $tbprojectMaster->setId(1);
        $tbprojectMaster->setName('案件3');
        $tbprojectMaster->setStatus(1);
        $tbprojectMaster->setExplanation('案件3説明');
        $tbprojectMaster->setTBCustomerCustomerId($customers[0]);
        $tbprojectMaster->setDeleteFlag(false);
        $tbprojectMaster->setPeriodStart(new \DateTime("2013-08-1 0:0:0"));
        $tbprojectMaster->setPeriodEnd(new \DateTime("2013-08-5 0:0:0"));
        $tbprojectMaster->setTBSystemUserManagerId($systemUsers[0]);//てすと001
        $tbprojectMaster->setEstimateFilePath('estimate3');
        $tbprojectMaster->setScheduleFilePath('schedule3');
        $manager->persist($tbprojectMaster);

        $metadata = $manager->getClassMetaData(get_class($tbprojectMaster));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        $manager->flush();
        $projectMasters[0] = $tbprojectMaster;

        //案件コスト階層マスタ
        //root
        $tbprojectCostHierarchyMaster = new TBProjectCostHierarchyMaster();
        $tbprojectCostHierarchyMaster->setTBProjectMasterTBProjectMasterId($projectMasters[0]);//案件3
        $tbprojectCostHierarchyMaster->setName("root");
        $tbprojectCostHierarchyMaster->setSortNo(0);
        $tbprojectCostHierarchyMaster->setDeleteFlag(false);
        $tbprojectCostHierarchyMaster->setPath("\\");
        $manager->persist($tbprojectCostHierarchyMaster);
        $manager->flush();
        $projectCostHierarchyMasters[0] = $tbprojectCostHierarchyMaster;
        //製造
        $tbprojectCostHierarchyMaster = new TBProjectCostHierarchyMaster();
        $tbprojectCostHierarchyMaster->setTBProjectMasterTBProjectMasterId($projectMasters[0]);//案件3
        $tbprojectCostHierarchyMaster->setName("製造");
        $tbprojectCostHierarchyMaster->setSortNo(2);
        $tbprojectCostHierarchyMaster->setDeleteFlag(false);
        $tbprojectCostHierarchyMaster->setPath("\\".$projectCostHierarchyMasters[0]->getId()."\\");
        $manager->persist($tbprojectCostHierarchyMaster);
        $manager->flush();
        $projectCostHierarchyMasters[1] = $tbprojectCostHierarchyMaster;
        //管理側
        $tbprojectCostHierarchyMaster = new TBProjectCostHierarchyMaster();
        $tbprojectCostHierarchyMaster->setTBProjectMasterTBProjectMasterId($projectMasters[0]);//案件3
        $tbprojectCostHierarchyMaster->setName("管理側");
        $tbprojectCostHierarchyMaster->setSortNo(2);
        $tbprojectCostHierarchyMaster->setDeleteFlag(false);
        $tbprojectCostHierarchyMaster->setPath($projectCostHierarchyMasters[1]->getPath().$projectCostHierarchyMasters[1]->getId()."\\");
        $manager->persist($tbprojectCostHierarchyMaster);
        $manager->flush();
        $projectCostHierarchyMasters[2] = $tbprojectCostHierarchyMaster;
        //一般側
        $tbprojectCostHierarchyMaster = new TBProjectCostHierarchyMaster();
        $tbprojectCostHierarchyMaster->setTBProjectMasterTBProjectMasterId($projectMasters[0]);//案件3
        $tbprojectCostHierarchyMaster->setName("一般側");
        $tbprojectCostHierarchyMaster->setSortNo(1);
        $tbprojectCostHierarchyMaster->setDeleteFlag(false);
        $tbprojectCostHierarchyMaster->setPath($projectCostHierarchyMasters[1]->getPath().$projectCostHierarchyMasters[1]->getId()."\\");
        $manager->persist($tbprojectCostHierarchyMaster);
        $manager->flush();
        $projectCostHierarchyMasters[3] = $tbprojectCostHierarchyMaster;

        //案件コストマスタ
        //要件定義
        $tbProjectCostMaster = new TBProjectCostMaster();
        $tbProjectCostMaster->setTBProjectMasterProjectMasterId($projectMasters[0]);//案件3
        $tbProjectCostMaster->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($projectCostHierarchyMasters[0]);//root
        $tbProjectCostMaster->setName('要件定義');
        $tbProjectCostMaster->setCost(1440);//3人日
        $tbProjectCostMaster->setSortNo(4);
        $tbProjectCostMaster->setDeleteFlag(false);
        $tbProjectCostMaster->setHierarchyPath('');
        $manager->persist($tbProjectCostMaster);
        $manager->flush();
        $projectCostMasters[0] = $tbProjectCostMaster;
        //設計
        $tbProjectCostMaster = new TBProjectCostMaster();
        $tbProjectCostMaster->setTBProjectMasterProjectMasterId($projectMasters[0]);//案件3
        $tbProjectCostMaster->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($projectCostHierarchyMasters[0]);//root
        $tbProjectCostMaster->setName('設計');
        $tbProjectCostMaster->setCost(2400);//5人日
        $tbProjectCostMaster->setSortNo(3);
        $tbProjectCostMaster->setDeleteFlag(false);
        $tbProjectCostMaster->setHierarchyPath('');
        $manager->persist($tbProjectCostMaster);
        $manager->flush();
        $projectCostMasters[1] = $tbProjectCostMaster;
        //管理側　ユーザー管理
        $tbProjectCostMaster = new TBProjectCostMaster();
        $tbProjectCostMaster->setTBProjectMasterProjectMasterId($projectMasters[0]);//案件3
        $tbProjectCostMaster->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($projectCostHierarchyMasters[2]);//管理側
        $tbProjectCostMaster->setName('ユーザー管理');
        $tbProjectCostMaster->setCost(2880);//6人日
        $tbProjectCostMaster->setSortNo(1);
        $tbProjectCostMaster->setDeleteFlag(false);
        $tbProjectCostMaster->setHierarchyPath('');
        $manager->persist($tbProjectCostMaster);
        $manager->flush();
        $projectCostMasters[2] = $tbProjectCostMaster;
        //管理側　顧客管理
        $tbProjectCostMaster = new TBProjectCostMaster();
        $tbProjectCostMaster->setTBProjectMasterProjectMasterId($projectMasters[0]);//案件3
        $tbProjectCostMaster->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($projectCostHierarchyMasters[2]);//管理側
        $tbProjectCostMaster->setName('顧客管理');
        $tbProjectCostMaster->setCost(1920);//4人日
        $tbProjectCostMaster->setSortNo(2);
        $tbProjectCostMaster->setDeleteFlag(false);
        $tbProjectCostMaster->setHierarchyPath('');
        $manager->persist($tbProjectCostMaster);
        $manager->flush();
        $projectCostMasters[3] = $tbProjectCostMaster;
        //一般側　ログイン
        $tbProjectCostMaster = new TBProjectCostMaster();
        $tbProjectCostMaster->setTBProjectMasterProjectMasterId($projectMasters[0]);//案件3
        $tbProjectCostMaster->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($projectCostHierarchyMasters[3]);//一般側
        $tbProjectCostMaster->setName('ログイン');
        $tbProjectCostMaster->setCost(960);//2人日
        $tbProjectCostMaster->setSortNo(2);
        $tbProjectCostMaster->setDeleteFlag(false);
        $tbProjectCostMaster->setHierarchyPath('');
        $manager->persist($tbProjectCostMaster);
        $manager->flush();
        $projectCostMasters[4] = $tbProjectCostMaster;
        //一般側　申請管理
        $tbProjectCostMaster = new TBProjectCostMaster();
        $tbProjectCostMaster->setTBProjectMasterProjectMasterId($projectMasters[0]);//案件3
        $tbProjectCostMaster->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($projectCostHierarchyMasters[3]);//一般側
        $tbProjectCostMaster->setName('申請管理');
        $tbProjectCostMaster->setCost(4320);//9人日
        $tbProjectCostMaster->setSortNo(1);
        $tbProjectCostMaster->setDeleteFlag(false);
        $tbProjectCostMaster->setHierarchyPath('');
        $manager->persist($tbProjectCostMaster);
        $manager->flush();
        $projectCostMasters[5] = $tbProjectCostMaster;
        //納品
        $tbProjectCostMaster = new TBProjectCostMaster();
        $tbProjectCostMaster->setTBProjectMasterProjectMasterId($projectMasters[0]);//案件3
        $tbProjectCostMaster->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($projectCostHierarchyMasters[0]);//root
        $tbProjectCostMaster->setName('納品');
        $tbProjectCostMaster->setCost(480);
        $tbProjectCostMaster->setSortNo(1);
        $tbProjectCostMaster->setDeleteFlag(false);
        $tbProjectCostMaster->setHierarchyPath('');
        $manager->persist($tbProjectCostMaster);
        $manager->flush();
        $projectCostMasters[6] = $tbProjectCostMaster;

        //製造工数
        //1
        $tbProductionCost = new TBProductionCost();
        $tbProductionCost->setTBProjectCostMasterProjectCostMasterId($projectCostMasters[0]);
        $tbProductionCost->setTBSystemUserSystemUserId($systemUsers[0]);
        $tbProductionCost->setWorkDate(new \DateTime("2013-08-1 0:0:0"));
        $tbProductionCost->setCost(480);
        $tbProductionCost->setNote('備考1');
        $tbProductionCost->setDeleteFlag(false);
        $manager->persist($tbProductionCost);
        $manager->flush();
        $productionCosts[0] = $tbProductionCost;
        //2
        $tbProductionCost = new TBProductionCost();
        $tbProductionCost->setTBProjectCostMasterProjectCostMasterId($projectCostMasters[0]);
        $tbProductionCost->setTBSystemUserSystemUserId($systemUsers[0]);
        $tbProductionCost->setWorkDate(new \DateTime("2013-08-2 0:0:0"));
        $tbProductionCost->setCost(120);
        $tbProductionCost->setNote('備考2');
        $tbProductionCost->setDeleteFlag(false);
        $manager->persist($tbProductionCost);
        $manager->flush();
        $productionCosts[1] = $tbProductionCost;
        //3
        $tbProductionCost = new TBProductionCost();
        $tbProductionCost->setTBProjectCostMasterProjectCostMasterId($projectCostMasters[2]);
        $tbProductionCost->setTBSystemUserSystemUserId($systemUsers[0]);
        $tbProductionCost->setWorkDate(new \DateTime("2013-08-2 0:0:0"));
        $tbProductionCost->setCost(300);
        $tbProductionCost->setNote('備考3');
        $tbProductionCost->setDeleteFlag(false);
        $manager->persist($tbProductionCost);
        $manager->flush();
        $productionCosts[2] = $tbProductionCost;
        //4
        $tbProductionCost = new TBProductionCost();
        $tbProductionCost->setTBProjectCostMasterProjectCostMasterId($projectCostMasters[2]);
        $tbProductionCost->setTBSystemUserSystemUserId($systemUsers[1]);
        $tbProductionCost->setWorkDate(new \DateTime("2013-08-2 0:0:0"));
        $tbProductionCost->setCost(480);
        $tbProductionCost->setNote('備考4');
        $tbProductionCost->setDeleteFlag(false);
        $manager->persist($tbProductionCost);
        $manager->flush();
        $productionCosts[2] = $tbProductionCost;



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