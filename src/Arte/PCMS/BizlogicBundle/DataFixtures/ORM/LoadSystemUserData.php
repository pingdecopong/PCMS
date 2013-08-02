<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hirashimafumitake
 * Date: 2013/07/16
 * Time: 5:37
 * To change this template use File | Settings | File Templates.
 */

namespace Arte\PCMS\BizlogicBundle\DataFixtures\ORM;


use Arte\PCMS\BizlogicBundle\Entity\TBCustomer;
use Arte\PCMS\BizlogicBundle\Entity\TBDepartment;
use Arte\PCMS\BizlogicBundle\Entity\TBProductionCost;
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

        ///1
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

        //TBProductionCost
        $productionCosts = array();
        ///1
        $tbProductionCost = new TBProductionCost();
        $tbProductionCost->setTBProjectCostMasterProjectCostMasterId($projectcostmasters[0]);
        $tbProductionCost->setCost(30);
        $tbProductionCost->setNote('備考1');
        $manager->persist($tbProductionCost);
        $manager->flush();
        $productionCosts[0] = $tbProductionCost;

        ///2
        $tbProductionCost = new TBProductionCost();
        $tbProductionCost->setTBProjectCostMasterProjectCostMasterId($projectcostmasters[0]);
        $tbProductionCost->setCost(15);
        $tbProductionCost->setNote('備考2');
        $manager->persist($tbProductionCost);
        $manager->flush();
        $productionCosts[1] = $tbProductionCost;

        ///3
        $tbProductionCost = new TBProductionCost();
        $tbProductionCost->setTBProjectCostMasterProjectCostMasterId($projectcostmasters[1]);
        $tbProductionCost->setCost(60);
        $tbProductionCost->setNote('備考3');
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