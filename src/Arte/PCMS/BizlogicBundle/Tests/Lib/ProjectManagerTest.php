<?php

namespace Arte\PCMS\BizlogicBundle\Tests\Lib;

use Arte\PCMS\BizlogicBundle\Entity\TBProductionCost;
use Arte\PCMS\BizlogicBundle\Entity\TBProjectCostHierarchyMaster;
use Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster;
use Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster;
use Arte\PCMS\BizlogicBundle\Lib\ProjectManager;
use Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProductionCost;
use Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProject;
use Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;

class ProjectManagerTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var \Arte\PCMS\BizlogicBundle\Lib\ProjectManager
     */
    private $projectManager;

    /**
     * プロジェクト新規作成
     * @test
     * @group none
     */
    public function プロジェクト新規作成()
    {
        $customer = $this->em->getRepository('ArtePCMSBizlogicBundle:TBCustomer')->find(1);
        $manager = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);

        $newProject = new ProjectManagerProject();

//        $newProject->setId(10);
        $newProject->setName("新規登録テスト１");
        $newProject->setStatus(1);
        $newProject->setExplanation("新規登録テスト１説明\n新規登録テスト１説明");
        $newProject->setPeriodStart(new \DateTime("2013-08-1 0:0:0"));
        $newProject->setPeriodEnd(new \DateTime("2013-08-7 0:0:0"));
        $newProject->setEstimateFilePath("見積ファイルパス");
        $newProject->setScheduleFilePath("スケジュールファイルパス");
        $newProject->setCustomer($customer);
        $newProject->setManager($manager);

        //cost5
        $cost5 = new ProjectManagerProjectCost();
        $cost5->setName("コスト５");
        $cost5->setCost(5);
        $cost5->setGroupFlag(false);
        $cost5->setSortNo(1);

        //group7
        $group7 = new ProjectManagerProjectCost();
        $group7->setName("グループ７");
        $group7->setGroupFlag(true);
        $group7->setSortNo(1);
        $group7->addChildCosts($cost5);

        //group8
        $group8 = new ProjectManagerProjectCost();
        $group8->setName("グループ８");
        $group8->setGroupFlag(true);
        $group8->setSortNo(2);

        //cost3
        $cost3 = new ProjectManagerProjectCost();
        $cost3->setName("コスト３");
        $cost3->setCost(3);
        $cost3->setGroupFlag(false);
        $cost3->setSortNo(1);

        //cost4
        $cost4 = new ProjectManagerProjectCost();
        $cost4->setName("コスト４");
        $cost4->setCost(4);
        $cost4->setGroupFlag(false);
        $cost4->setSortNo(2);

        //group5
        $group5 = new ProjectManagerProjectCost();
        $group5->setName("グループ５");
        $group5->setGroupFlag(true);
        $group5->setSortNo(1);
        $group5->addChildCosts($cost3);
        $group5->addChildCosts($cost4);

        //cost2
        $cost2 = new ProjectManagerProjectCost();
        $cost2->setName("コスト２");
        $cost2->setCost(2);
        $cost2->setGroupFlag(false);
        $cost2->setSortNo(1);

        //group6
        $group6 = new ProjectManagerProjectCost();
        $group6->setName("グループ６");
        $group6->setGroupFlag(true);
        $group6->setSortNo(2);
        $group6->addChildCosts($group7);
        $group6->addChildCosts($group8);

        //group3
        $group3 = new ProjectManagerProjectCost();
        $group3->setName("グループ３");
        $group3->setGroupFlag(true);
        $group3->setSortNo(1);
        $group3->addChildCosts($group5);

        //group4
        $group4 = new ProjectManagerProjectCost();
        $group4->setName("グループ４");
        $group4->setGroupFlag(true);
        $group4->setSortNo(2);
        $group4->addChildCosts($cost2);
        $group4->addChildCosts($group6);

        //group2
        $group2 = new ProjectManagerProjectCost();
        $group2->setName("グループ２");
        $group2->setGroupFlag(true);
        $group2->setSortNo(1);
        $group2->addChildCosts($group3);
        $group2->addChildCosts($group4);

        //cost1
        $cost1 = new ProjectManagerProjectCost();
        $cost1->setName("コスト１");
        $cost1->setCost(1);
        $cost1->setGroupFlag(false);
        $cost1->setSortNo(2);

        //group1
        $group1 = new ProjectManagerProjectCost();
        $group1->setName("グループ１");
        $group1->setGroupFlag(true);
        $group1->setSortNo(1);
        $group1->addChildCosts($group2);
        $group1->addChildCosts($cost1);

        $newProject->addCosts($group1);

        //新規作成
        $project = $this->projectManager->createProject($newProject);
//        $this->targetTBProjectMasterId = $project->getId();

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(15, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(14, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(7, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(3, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(0, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(4, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

    }

    /**
     * テスト用データ登録　編集テスト用コスト無し
     * @return TBProjectMaster
     * @throws \Exception
     */
    private function createTargetRawDataForEdit()
    {
        $this->em->getConnection()->beginTransaction();
        try {
            $customer = $this->em->getRepository('ArtePCMSBizlogicBundle:TBCustomer')->find(1);
            $manager = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);

            //案件マスタ
            $tbprojectMaster = new TBProjectMaster();
            $tbprojectMaster->setName('新規登録テスト１');
            $tbprojectMaster->setStatus(1);
            $tbprojectMaster->setExplanation("新規登録テスト１説明\n新規登録テスト１説明");
            $tbprojectMaster->setTBCustomerCustomerId($customer);
            $tbprojectMaster->setDeleteFlag(false);
            $tbprojectMaster->setPeriodStart(new \DateTime("2013-08-1 0:0:0"));
            $tbprojectMaster->setPeriodEnd(new \DateTime("2013-08-7 0:0:0"));
            $tbprojectMaster->setTBSystemUserManagerId($manager);
            $tbprojectMaster->setEstimateFilePath('見積ファイルパス');
            $tbprojectMaster->setScheduleFilePath('スケジュールファイルパス');
            $this->em->persist($tbprojectMaster);
            $this->em->flush();

            //root
            $tbprojectCostHierarchyMasterRoot = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterRoot->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterRoot->setName("\\");
            $tbprojectCostHierarchyMasterRoot->setSortNo(0);
            $tbprojectCostHierarchyMasterRoot->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterRoot->setPath("\\");
            $this->em->persist($tbprojectCostHierarchyMasterRoot);
            $this->em->flush();

            //グループ１
            $tbprojectCostHierarchyMasterGroup1 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup1->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup1->setName('グループ１');
            $tbprojectCostHierarchyMasterGroup1->setSortNo(1);
            $tbprojectCostHierarchyMasterGroup1->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup1->setPath($tbprojectCostHierarchyMasterRoot->getPath() . $tbprojectCostHierarchyMasterRoot->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup1);
            $this->em->flush();

            //グループ２
            $tbprojectCostHierarchyMasterGroup2 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup2->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup2->setName('グループ２');
            $tbprojectCostHierarchyMasterGroup2->setSortNo(1);
            $tbprojectCostHierarchyMasterGroup2->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup2->setPath($tbprojectCostHierarchyMasterGroup1->getPath() . $tbprojectCostHierarchyMasterGroup1->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup2);
            $this->em->flush();

            //グループ３
            $tbprojectCostHierarchyMasterGroup3 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup3->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup3->setName('グループ３');
            $tbprojectCostHierarchyMasterGroup3->setSortNo(1);
            $tbprojectCostHierarchyMasterGroup3->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup3->setPath($tbprojectCostHierarchyMasterGroup2->getPath() . $tbprojectCostHierarchyMasterGroup2->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup3);
            $this->em->flush();

            //グループ４
            $tbprojectCostHierarchyMasterGroup4 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup4->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup4->setName('グループ４');
            $tbprojectCostHierarchyMasterGroup4->setSortNo(2);
            $tbprojectCostHierarchyMasterGroup4->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup4->setPath($tbprojectCostHierarchyMasterGroup2->getPath() . $tbprojectCostHierarchyMasterGroup2->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup4);
            $this->em->flush();

            //グループ５
            $tbprojectCostHierarchyMasterGroup5 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup5->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup5->setName('グループ５');
            $tbprojectCostHierarchyMasterGroup5->setSortNo(1);
            $tbprojectCostHierarchyMasterGroup5->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup5->setPath($tbprojectCostHierarchyMasterGroup3->getPath() . $tbprojectCostHierarchyMasterGroup3->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup5);
            $this->em->flush();

            //グループ６
            $tbprojectCostHierarchyMasterGroup6 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup6->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup6->setName('グループ６');
            $tbprojectCostHierarchyMasterGroup6->setSortNo(2);
            $tbprojectCostHierarchyMasterGroup6->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup6->setPath($tbprojectCostHierarchyMasterGroup4->getPath() . $tbprojectCostHierarchyMasterGroup4->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup6);
            $this->em->flush();

            //グループ７
            $tbprojectCostHierarchyMasterGroup7 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup7->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup7->setName('グループ７');
            $tbprojectCostHierarchyMasterGroup7->setSortNo(1);
            $tbprojectCostHierarchyMasterGroup7->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup7->setPath($tbprojectCostHierarchyMasterGroup6->getPath() . $tbprojectCostHierarchyMasterGroup6->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup7);
            $this->em->flush();

            //グループ８
            $tbprojectCostHierarchyMasterGroup8 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup8->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup8->setName('グループ８');
            $tbprojectCostHierarchyMasterGroup8->setSortNo(2);
            $tbprojectCostHierarchyMasterGroup8->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup8->setPath($tbprojectCostHierarchyMasterGroup6->getPath() . $tbprojectCostHierarchyMasterGroup6->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup8);
            $this->em->flush();

            //コスト１
            $tbProjectCostMaster1 = new TBProjectCostMaster();
            $tbProjectCostMaster1->setTBProjectMasterProjectMasterId($tbprojectMaster);
            $tbProjectCostMaster1->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($tbprojectCostHierarchyMasterGroup1);
            $tbProjectCostMaster1->setName('コスト１');
            $tbProjectCostMaster1->setCost(1);
            $tbProjectCostMaster1->setSortNo(2);
            $tbProjectCostMaster1->setDeleteFlag(false);
            $tbProjectCostMaster1->setHierarchyPath('');
            $this->em->persist($tbProjectCostMaster1);
            $this->em->flush();

            //コスト２
            $tbProjectCostMaster2 = new TBProjectCostMaster();
            $tbProjectCostMaster2->setTBProjectMasterProjectMasterId($tbprojectMaster);
            $tbProjectCostMaster2->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($tbprojectCostHierarchyMasterGroup4);
            $tbProjectCostMaster2->setName('コスト２');
            $tbProjectCostMaster2->setCost(2);
            $tbProjectCostMaster2->setSortNo(1);
            $tbProjectCostMaster2->setDeleteFlag(false);
            $tbProjectCostMaster2->setHierarchyPath('');
            $this->em->persist($tbProjectCostMaster2);
            $this->em->flush();

            //コスト３
            $tbProjectCostMaster3 = new TBProjectCostMaster();
            $tbProjectCostMaster3->setTBProjectMasterProjectMasterId($tbprojectMaster);
            $tbProjectCostMaster3->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($tbprojectCostHierarchyMasterGroup5);
            $tbProjectCostMaster3->setName('コスト３');
            $tbProjectCostMaster3->setCost(3);
            $tbProjectCostMaster3->setSortNo(1);
            $tbProjectCostMaster3->setDeleteFlag(false);
            $tbProjectCostMaster3->setHierarchyPath('');
            $this->em->persist($tbProjectCostMaster3);
            $this->em->flush();

            //コスト４
            $tbProjectCostMaster4 = new TBProjectCostMaster();
            $tbProjectCostMaster4->setTBProjectMasterProjectMasterId($tbprojectMaster);
            $tbProjectCostMaster4->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($tbprojectCostHierarchyMasterGroup5);
            $tbProjectCostMaster4->setName('コスト４');
            $tbProjectCostMaster4->setCost(4);
            $tbProjectCostMaster4->setSortNo(2);
            $tbProjectCostMaster4->setDeleteFlag(false);
            $tbProjectCostMaster4->setHierarchyPath('');
            $this->em->persist($tbProjectCostMaster4);
            $this->em->flush();

            //コスト５
            $tbProjectCostMaster5 = new TBProjectCostMaster();
            $tbProjectCostMaster5->setTBProjectMasterProjectMasterId($tbprojectMaster);
            $tbProjectCostMaster5->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($tbprojectCostHierarchyMasterGroup7);
            $tbProjectCostMaster5->setName('コスト５');
            $tbProjectCostMaster5->setCost(5);
            $tbProjectCostMaster5->setSortNo(1);
            $tbProjectCostMaster5->setDeleteFlag(false);
            $tbProjectCostMaster5->setHierarchyPath('');
            $this->em->persist($tbProjectCostMaster5);
            $this->em->flush();

            $this->em->getConnection()->commit();

        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
        }

        $this->em->clear();

        return $tbprojectMaster;
    }

    /**
     * テスト用データ登録　編集テスト用コスト付き
     * @return TBProjectMaster
     * @throws \Exception
     */
    private function createTargetRawDataWithCostForEdit()
    {
        $this->em->getConnection()->beginTransaction();
        try {
            $customer = $this->em->getRepository('ArtePCMSBizlogicBundle:TBCustomer')->find(1);
            $manager = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);

            //案件マスタ
            $tbprojectMaster = new TBProjectMaster();
            $tbprojectMaster->setName('新規登録テスト１');
            $tbprojectMaster->setStatus(1);
            $tbprojectMaster->setExplanation("新規登録テスト１説明\n新規登録テスト１説明");
            $tbprojectMaster->setTBCustomerCustomerId($customer);
            $tbprojectMaster->setDeleteFlag(false);
            $tbprojectMaster->setPeriodStart(new \DateTime("2013-08-1 0:0:0"));
            $tbprojectMaster->setPeriodEnd(new \DateTime("2013-08-7 0:0:0"));
            $tbprojectMaster->setTBSystemUserManagerId($manager);
            $tbprojectMaster->setEstimateFilePath('見積ファイルパス');
            $tbprojectMaster->setScheduleFilePath('スケジュールファイルパス');
            $this->em->persist($tbprojectMaster);
            $this->em->flush();

            //root
            $tbprojectCostHierarchyMasterRoot = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterRoot->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterRoot->setName("\\");
            $tbprojectCostHierarchyMasterRoot->setSortNo(0);
            $tbprojectCostHierarchyMasterRoot->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterRoot->setPath("\\");
            $this->em->persist($tbprojectCostHierarchyMasterRoot);
            $this->em->flush();

            //グループ１
            $tbprojectCostHierarchyMasterGroup1 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup1->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup1->setName('グループ１');
            $tbprojectCostHierarchyMasterGroup1->setSortNo(1);
            $tbprojectCostHierarchyMasterGroup1->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup1->setPath($tbprojectCostHierarchyMasterRoot->getPath() . $tbprojectCostHierarchyMasterRoot->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup1);
            $this->em->flush();

            //グループ２
            $tbprojectCostHierarchyMasterGroup2 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup2->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup2->setName('グループ２');
            $tbprojectCostHierarchyMasterGroup2->setSortNo(1);
            $tbprojectCostHierarchyMasterGroup2->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup2->setPath($tbprojectCostHierarchyMasterGroup1->getPath() . $tbprojectCostHierarchyMasterGroup1->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup2);
            $this->em->flush();

            //グループ３
            $tbprojectCostHierarchyMasterGroup3 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup3->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup3->setName('グループ３');
            $tbprojectCostHierarchyMasterGroup3->setSortNo(1);
            $tbprojectCostHierarchyMasterGroup3->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup3->setPath($tbprojectCostHierarchyMasterGroup2->getPath() . $tbprojectCostHierarchyMasterGroup2->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup3);
            $this->em->flush();

            //グループ４
            $tbprojectCostHierarchyMasterGroup4 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup4->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup4->setName('グループ４');
            $tbprojectCostHierarchyMasterGroup4->setSortNo(2);
            $tbprojectCostHierarchyMasterGroup4->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup4->setPath($tbprojectCostHierarchyMasterGroup2->getPath() . $tbprojectCostHierarchyMasterGroup2->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup4);
            $this->em->flush();

            //グループ５
            $tbprojectCostHierarchyMasterGroup5 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup5->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup5->setName('グループ５');
            $tbprojectCostHierarchyMasterGroup5->setSortNo(1);
            $tbprojectCostHierarchyMasterGroup5->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup5->setPath($tbprojectCostHierarchyMasterGroup3->getPath() . $tbprojectCostHierarchyMasterGroup3->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup5);
            $this->em->flush();

            //グループ６
            $tbprojectCostHierarchyMasterGroup6 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup6->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup6->setName('グループ６');
            $tbprojectCostHierarchyMasterGroup6->setSortNo(2);
            $tbprojectCostHierarchyMasterGroup6->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup6->setPath($tbprojectCostHierarchyMasterGroup4->getPath() . $tbprojectCostHierarchyMasterGroup4->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup6);
            $this->em->flush();

            //グループ７
            $tbprojectCostHierarchyMasterGroup7 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup7->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup7->setName('グループ７');
            $tbprojectCostHierarchyMasterGroup7->setSortNo(1);
            $tbprojectCostHierarchyMasterGroup7->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup7->setPath($tbprojectCostHierarchyMasterGroup6->getPath() . $tbprojectCostHierarchyMasterGroup6->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup7);
            $this->em->flush();

            //グループ８
            $tbprojectCostHierarchyMasterGroup8 = new TBProjectCostHierarchyMaster();
            $tbprojectCostHierarchyMasterGroup8->setTBProjectMasterTBProjectMasterId($tbprojectMaster);
            $tbprojectCostHierarchyMasterGroup8->setName('グループ８');
            $tbprojectCostHierarchyMasterGroup8->setSortNo(2);
            $tbprojectCostHierarchyMasterGroup8->setDeleteFlag(false);
            $tbprojectCostHierarchyMasterGroup8->setPath($tbprojectCostHierarchyMasterGroup6->getPath() . $tbprojectCostHierarchyMasterGroup6->getId().'\\');
            $this->em->persist($tbprojectCostHierarchyMasterGroup8);
            $this->em->flush();

            //コスト１
            $tbProjectCostMaster1 = new TBProjectCostMaster();
            $tbProjectCostMaster1->setTBProjectMasterProjectMasterId($tbprojectMaster);
            $tbProjectCostMaster1->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($tbprojectCostHierarchyMasterGroup1);
            $tbProjectCostMaster1->setName('コスト１');
            $tbProjectCostMaster1->setCost(1);
            $tbProjectCostMaster1->setSortNo(2);
            $tbProjectCostMaster1->setDeleteFlag(false);
            $tbProjectCostMaster1->setHierarchyPath('');
            $this->em->persist($tbProjectCostMaster1);
            $this->em->flush();

            //コスト２
            $tbProjectCostMaster2 = new TBProjectCostMaster();
            $tbProjectCostMaster2->setTBProjectMasterProjectMasterId($tbprojectMaster);
            $tbProjectCostMaster2->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($tbprojectCostHierarchyMasterGroup4);
            $tbProjectCostMaster2->setName('コスト２');
            $tbProjectCostMaster2->setCost(2);
            $tbProjectCostMaster2->setSortNo(1);
            $tbProjectCostMaster2->setDeleteFlag(false);
            $tbProjectCostMaster2->setHierarchyPath('');
            $this->em->persist($tbProjectCostMaster2);
            $this->em->flush();

            //コスト３
            $tbProjectCostMaster3 = new TBProjectCostMaster();
            $tbProjectCostMaster3->setTBProjectMasterProjectMasterId($tbprojectMaster);
            $tbProjectCostMaster3->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($tbprojectCostHierarchyMasterGroup5);
            $tbProjectCostMaster3->setName('コスト３');
            $tbProjectCostMaster3->setCost(3);
            $tbProjectCostMaster3->setSortNo(1);
            $tbProjectCostMaster3->setDeleteFlag(false);
            $tbProjectCostMaster3->setHierarchyPath('');
            $this->em->persist($tbProjectCostMaster3);
            $this->em->flush();

            //コスト４
            $tbProjectCostMaster4 = new TBProjectCostMaster();
            $tbProjectCostMaster4->setTBProjectMasterProjectMasterId($tbprojectMaster);
            $tbProjectCostMaster4->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($tbprojectCostHierarchyMasterGroup5);
            $tbProjectCostMaster4->setName('コスト４');
            $tbProjectCostMaster4->setCost(4);
            $tbProjectCostMaster4->setSortNo(2);
            $tbProjectCostMaster4->setDeleteFlag(false);
            $tbProjectCostMaster4->setHierarchyPath('');
            $this->em->persist($tbProjectCostMaster4);
            $this->em->flush();

            //コスト５
            $tbProjectCostMaster5 = new TBProjectCostMaster();
            $tbProjectCostMaster5->setTBProjectMasterProjectMasterId($tbprojectMaster);
            $tbProjectCostMaster5->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($tbprojectCostHierarchyMasterGroup7);
            $tbProjectCostMaster5->setName('コスト５');
            $tbProjectCostMaster5->setCost(5);
            $tbProjectCostMaster5->setSortNo(1);
            $tbProjectCostMaster5->setDeleteFlag(false);
            $tbProjectCostMaster5->setHierarchyPath('');
            $this->em->persist($tbProjectCostMaster5);
            $this->em->flush();




            //実工数登録
            $user5 = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(5);
            $user6 = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(6);

            //実工数２
            $tbProductionCost2 = new TBProductionCost();
            $tbProductionCost2->setTBProjectCostMasterProjectCostMasterId($tbProjectCostMaster2);
            $tbProductionCost2->setTBSystemUserSystemUserId($user5);
            $tbProductionCost2->setWorkDate(new \DateTime("2013-08-1 0:0:0"));
            $tbProductionCost2->setCost(12);
            $tbProductionCost2->setNote('実工数２');
            $tbProductionCost2->setDeleteFlag(false);
            $this->em->persist($tbProductionCost2);
            $this->em->flush();

            //実工数３－１
            $tbProductionCost31 = new TBProductionCost();
            $tbProductionCost31->setTBProjectCostMasterProjectCostMasterId($tbProjectCostMaster3);
            $tbProductionCost31->setTBSystemUserSystemUserId($user5);
            $tbProductionCost31->setWorkDate(new \DateTime("2013-08-2 0:0:0"));
            $tbProductionCost31->setCost(11);
            $tbProductionCost31->setNote('実工数３－１');
            $tbProductionCost31->setDeleteFlag(false);
            $this->em->persist($tbProductionCost31);
            $this->em->flush();

            //実工数３－２
            $tbProductionCost32 = new TBProductionCost();
            $tbProductionCost32->setTBProjectCostMasterProjectCostMasterId($tbProjectCostMaster3);
            $tbProductionCost32->setTBSystemUserSystemUserId($user6);
            $tbProductionCost32->setWorkDate(new \DateTime("2013-08-3 0:0:0"));
            $tbProductionCost32->setCost(2);
            $tbProductionCost32->setNote('実工数３－２');
            $tbProductionCost32->setDeleteFlag(false);
            $this->em->persist($tbProductionCost32);
            $this->em->flush();

            //実工数５
            $tbProductionCost5 = new TBProductionCost();
            $tbProductionCost5->setTBProjectCostMasterProjectCostMasterId($tbProjectCostMaster5);
            $tbProductionCost5->setTBSystemUserSystemUserId($user5);
            $tbProductionCost5->setWorkDate(new \DateTime("2013-08-4 0:0:0"));
            $tbProductionCost5->setCost(15);
            $tbProductionCost5->setNote('実工数５');
            $tbProductionCost5->setDeleteFlag(false);
            $this->em->persist($tbProductionCost5);
            $this->em->flush();

            $this->em->getConnection()->commit();

        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
        }

        $this->em->clear();

        return $tbprojectMaster;
    }
    /**
     * テスト用データ登録　編集テスト用
     */
    private function createTargetDataForEdit()
    {
        $customer = $this->em->getRepository('ArtePCMSBizlogicBundle:TBCustomer')->find(1);
        $manager = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);

        $newProject = new ProjectManagerProject();

        $newProject->setName("新規登録テスト１");
        $newProject->setStatus(1);
        $newProject->setExplanation("新規登録テスト１説明\n新規登録テスト１説明");
        $newProject->setPeriodStart(new \DateTime("2013-08-1 0:0:0"));
        $newProject->setPeriodEnd(new \DateTime("2013-08-7 0:0:0"));
        $newProject->setEstimateFilePath("見積ファイルパス");
        $newProject->setScheduleFilePath("スケジュールファイルパス");
        $newProject->setCustomer($customer);
        $newProject->setManager($manager);

        //cost5
        $cost5 = new ProjectManagerProjectCost();
        $cost5->setName("コスト５");
        $cost5->setCost(5);
        $cost5->setGroupFlag(false);
        $cost5->setSortNo(1);

        //group7
        $group7 = new ProjectManagerProjectCost();
        $group7->setName("グループ７");
        $group7->setGroupFlag(true);
        $group7->setSortNo(1);
        $group7->addChildCosts($cost5);

        //group8
        $group8 = new ProjectManagerProjectCost();
        $group8->setName("グループ８");
        $group8->setGroupFlag(true);
        $group8->setSortNo(2);

        //cost3
        $cost3 = new ProjectManagerProjectCost();
        $cost3->setName("コスト３");
        $cost3->setCost(3);
        $cost3->setGroupFlag(false);
        $cost3->setSortNo(1);

        //cost4
        $cost4 = new ProjectManagerProjectCost();
        $cost4->setName("コスト４");
        $cost4->setCost(4);
        $cost4->setGroupFlag(false);
        $cost4->setSortNo(2);

        //group5
        $group5 = new ProjectManagerProjectCost();
        $group5->setName("グループ５");
        $group5->setGroupFlag(true);
        $group5->setSortNo(1);
        $group5->addChildCosts($cost3);
        $group5->addChildCosts($cost4);

        //cost2
        $cost2 = new ProjectManagerProjectCost();
        $cost2->setName("コスト２");
        $cost2->setCost(2);
        $cost2->setGroupFlag(false);
        $cost2->setSortNo(1);

        //group6
        $group6 = new ProjectManagerProjectCost();
        $group6->setName("グループ６");
        $group6->setGroupFlag(true);
        $group6->setSortNo(2);
        $group6->addChildCosts($group7);
        $group6->addChildCosts($group8);

        //group3
        $group3 = new ProjectManagerProjectCost();
        $group3->setName("グループ３");
        $group3->setGroupFlag(true);
        $group3->setSortNo(1);
        $group3->addChildCosts($group5);

        //group4
        $group4 = new ProjectManagerProjectCost();
        $group4->setName("グループ４");
        $group4->setGroupFlag(true);
        $group4->setSortNo(2);
        $group4->addChildCosts($cost2);
        $group4->addChildCosts($group6);

        //group2
        $group2 = new ProjectManagerProjectCost();
        $group2->setName("グループ２");
        $group2->setGroupFlag(true);
        $group2->setSortNo(1);
        $group2->addChildCosts($group3);
        $group2->addChildCosts($group4);

        //cost1
        $cost1 = new ProjectManagerProjectCost();
        $cost1->setName("コスト１");
        $cost1->setCost(1);
        $cost1->setGroupFlag(false);
        $cost1->setSortNo(2);

        //group1
        $group1 = new ProjectManagerProjectCost();
        $group1->setName("グループ１");
        $group1->setGroupFlag(true);
        $group1->setSortNo(1);
        $group1->addChildCosts($group2);
        $group1->addChildCosts($cost1);

        $newProject->addCosts($group1);

        //新規作成
        $project = $this->projectManager->createProject($newProject);

        return $project;
    }

    private function searchGroupName($groups, $name)
    {
        foreach($groups as $value)
        {
            /** @var $value \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
            if($value->getName() == $name && $value->getGroupFlag() == true){
                return $value;
            }
        }
    }

    private function searchCostName($costs, $name)
    {
        foreach($costs as $value)
        {
            /** @var $value \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
            if($value->getName() == $name && $value->getGroupFlag() == false){
                return $value;
            }
        }
    }

    /**
     * プロジェクト取得　プロジェクト情報取得
     * @test
     * @group none
     */
    function プロジェクト取得_プロジェクト情報取得()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals(15, $project->getProjectTotalCost());
        $this->assertEquals(0, $project->getProductionTotalCost());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(15, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(14, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(7, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

    }


    /**
     * プロジェクト新規作成　新規作成１
     * @test
     * @group none
     */
    function プロジェクト新規作成_新規作成１()
    {
        $customer = $this->em->getRepository('ArtePCMSBizlogicBundle:TBCustomer')->find(1);
        $manager = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);

        $newProject = new ProjectManagerProject();

        $newProject->setName("新規登録テスト１");
        $newProject->setStatus(1);
        $newProject->setExplanation("新規登録テスト１説明\n新規登録テスト１説明");
        $newProject->setPeriodStart(new \DateTime("2013-08-1 0:0:0"));
        $newProject->setPeriodEnd(new \DateTime("2013-08-7 0:0:0"));
        $newProject->setEstimateFilePath("見積ファイルパス");
        $newProject->setScheduleFilePath("スケジュールファイルパス");
        $newProject->setCustomer($customer);
        $newProject->setManager($manager);

        //cost5
        $cost5 = new ProjectManagerProjectCost();
        $cost5->setName("コスト５");
        $cost5->setCost(5);
        $cost5->setGroupFlag(false);
        $cost5->setSortNo(1);

        //group7
        $group7 = new ProjectManagerProjectCost();
        $group7->setName("グループ７");
        $group7->setGroupFlag(true);
        $group7->setSortNo(1);
        $group7->addChildCosts($cost5);

        //group8
        $group8 = new ProjectManagerProjectCost();
        $group8->setName("グループ８");
        $group8->setGroupFlag(true);
        $group8->setSortNo(2);

        //cost3
        $cost3 = new ProjectManagerProjectCost();
        $cost3->setName("コスト３");
        $cost3->setCost(3);
        $cost3->setGroupFlag(false);
        $cost3->setSortNo(1);

        //cost4
        $cost4 = new ProjectManagerProjectCost();
        $cost4->setName("コスト４");
        $cost4->setCost(4);
        $cost4->setGroupFlag(false);
        $cost4->setSortNo(2);

        //group5
        $group5 = new ProjectManagerProjectCost();
        $group5->setName("グループ５");
        $group5->setGroupFlag(true);
        $group5->setSortNo(1);
        $group5->addChildCosts($cost3);
        $group5->addChildCosts($cost4);

        //cost2
        $cost2 = new ProjectManagerProjectCost();
        $cost2->setName("コスト２");
        $cost2->setCost(2);
        $cost2->setGroupFlag(false);
        $cost2->setSortNo(1);

        //group6
        $group6 = new ProjectManagerProjectCost();
        $group6->setName("グループ６");
        $group6->setGroupFlag(true);
        $group6->setSortNo(2);
        $group6->addChildCosts($group7);
        $group6->addChildCosts($group8);

        //group3
        $group3 = new ProjectManagerProjectCost();
        $group3->setName("グループ３");
        $group3->setGroupFlag(true);
        $group3->setSortNo(1);
        $group3->addChildCosts($group5);

        //group4
        $group4 = new ProjectManagerProjectCost();
        $group4->setName("グループ４");
        $group4->setGroupFlag(true);
        $group4->setSortNo(2);
        $group4->addChildCosts($cost2);
        $group4->addChildCosts($group6);

        //group2
        $group2 = new ProjectManagerProjectCost();
        $group2->setName("グループ２");
        $group2->setGroupFlag(true);
        $group2->setSortNo(1);
        $group2->addChildCosts($group3);
        $group2->addChildCosts($group4);

        //cost1
        $cost1 = new ProjectManagerProjectCost();
        $cost1->setName("コスト１");
        $cost1->setCost(1);
        $cost1->setGroupFlag(false);
        $cost1->setSortNo(2);

        //group1
        $group1 = new ProjectManagerProjectCost();
        $group1->setName("グループ１");
        $group1->setGroupFlag(true);
        $group1->setSortNo(1);
        $group1->addChildCosts($group2);
        $group1->addChildCosts($cost1);

        $newProject->addCosts($group1);

        //新規作成
        $project = $this->projectManager->createProject($newProject);



        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(15, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(14, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(7, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

    }




    /**
     * プロジェクト編集　プロジェクト情報編集
     * @test
     * @group none
     */
    function プロジェクト編集_プロジェクト情報のみ編集()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());
        $customer = $this->em->getRepository('ArtePCMSBizlogicBundle:TBCustomer')->find(3);
        $manager = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(3);

        $project->setName("編集テスト１");
        $project->setStatus(2);
        $project->setExplanation("編集テスト１説明\n編集テスト１説明");
        $project->setPeriodStart(new \DateTime("2013-08-8 0:0:0"));
        $project->setPeriodEnd(new \DateTime("2013-08-15 0:0:0"));
        $project->setEstimateFilePath("編集テスト１見積ファイルパス");
        $project->setScheduleFilePath("編集テスト１スケジュールファイルパス");
        $project->setCustomer($customer);
        $project->setManager($manager);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('編集テスト１', $project->getName());
        $this->assertEquals(2, $project->getStatus());
        $this->assertEquals("編集テスト１説明\n編集テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-8 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-15 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('編集テスト１見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('編集テスト１スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客3', $project->getCustomer()->getName());
        $this->assertEquals('てすと003', $project->getManager()->getDisplayName());

//        $checkRoots = $project->getCosts();
//        $this->assertEquals(0, $checkRoots->count());

        $checkRoots = $project->getCosts();
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(15, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(14, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(7, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

    }

    /**
     * プロジェクト編集　コスト修正１
     * @test
     * @group none
     */
    function プロジェクト編集_コスト修正１()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');
        $editCost1 = $this->searchCostName($editGroup1->getChildCosts(), 'コスト１');
        $editCost1->setCost(101);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

//        $checkRoots = $project->getCosts();
//        $this->assertEquals(0, $checkRoots->count());

        $checkRoots = $project->getCosts();
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(115, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(14, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(101, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(7, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(3, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(0, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(4, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

    }

    /**
     * プロジェクト編集　コスト修正２
     * @test
     * @group none
     */
    function プロジェクト編集_コスト修正２()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');
        $editCost1 = $this->searchCostName($editGroup1->getChildCosts(), 'コスト１');
        $editCost1->setCost(101);

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');
        $editCost2 = $this->searchCostName($editGroup4->getChildCosts(), 'コスト２');
        $editCost2->setCost(102);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(215, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(114, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(101, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(107, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(3, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(0, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(4, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(102, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

    }

    /**
     * プロジェクト編集　コスト修正３
     * @test
     * @group none
     */
    function プロジェクト編集_コスト修正３()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');
        $editCost1 = $this->searchCostName($editGroup1->getChildCosts(), 'コスト１');
        $editCost1->setCost(101);

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');
        $editCost2 = $this->searchCostName($editGroup4->getChildCosts(), 'コスト２');
        $editCost2->setCost(102);

        $editGroup3 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ３');
        $editGroup5 = $this->searchGroupName($editGroup3->getChildCosts(), 'グループ５');
        $editCost3 = $this->searchCostName($editGroup5->getChildCosts(), 'コスト３');
        $editCost3->setCost(103);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(315, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(214, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(101, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(107, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(107, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(107, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(103, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(0, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(4, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(102, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

    }

    /**
     * プロジェクト編集　コスト修正４
     * @test
     * @group none
     */
    function プロジェクト編集_コスト修正４()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');
        $editCost1 = $this->searchCostName($editGroup1->getChildCosts(), 'コスト１');
        $editCost1->setCost(101);

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');
        $editCost2 = $this->searchCostName($editGroup4->getChildCosts(), 'コスト２');
        $editCost2->setCost(102);

        $editGroup3 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ３');
        $editGroup5 = $this->searchGroupName($editGroup3->getChildCosts(), 'グループ５');
        $editCost3 = $this->searchCostName($editGroup5->getChildCosts(), 'コスト３');
        $editCost3->setCost(103);

        $editCost4 = $this->searchCostName($editGroup5->getChildCosts(), 'コスト４');
        $editCost4->setCost(104);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(415, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(314, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(101, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(207, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(107, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(207, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(103, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(0, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(104, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(102, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

    }

    /**
     * プロジェクト編集　コスト修正５
     * @test
     * @group none
     */
    function プロジェクト編集_コスト修正５()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');
        $editCost1 = $this->searchCostName($editGroup1->getChildCosts(), 'コスト１');
        $editCost1->setCost(101);

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');
        $editCost2 = $this->searchCostName($editGroup4->getChildCosts(), 'コスト２');
        $editCost2->setCost(102);

        $editGroup3 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ３');
        $editGroup5 = $this->searchGroupName($editGroup3->getChildCosts(), 'グループ５');
        $editCost3 = $this->searchCostName($editGroup5->getChildCosts(), 'コスト３');
        $editCost3->setCost(103);

        $editCost4 = $this->searchCostName($editGroup5->getChildCosts(), 'コスト４');
        $editCost4->setCost(104);

        $editGroup6 = $this->searchGroupName($editGroup4->getChildCosts(), 'グループ６');
        $editGroup7 = $this->searchGroupName($editGroup6->getChildCosts(), 'グループ７');
        $editCost5 = $this->searchCostName($editGroup7->getChildCosts(), 'コスト５');
        $editCost5->setCost(105);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(515, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(414, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(101, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(207, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(207, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(207, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(103, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(0, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(104, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(102, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(105, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(105, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(105, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

    }

    /**
     * プロジェクト編集　コスト名称修正
     * @test
     * @group none
     */
    function プロジェクト編集_コスト名称修正()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');

        $editGroup6 = $this->searchGroupName($editGroup4->getChildCosts(), 'グループ６');
        $editGroup7 = $this->searchGroupName($editGroup6->getChildCosts(), 'グループ７');
        $editCost5 = $this->searchCostName($editGroup7->getChildCosts(), 'コスト５');
        $editCost5->setName('コスト５編集');

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(15, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(14, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(7, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(3, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(0, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(4, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５編集', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

    }

    /**
     * プロジェクト編集　コスト全削除
     * @test
     * @group none
     */
    function プロジェクト編集_コスト全削除()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editRoots->clear();

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        $this->assertEquals(0, $checkRoots->count());

    }

    /**
     * プロジェクト編集　ツリー構造修正1_2
     * @test
     * @group none
     */
    function プロジェクト編集_ツリー構造修正1_2()
    {
        //テスト用データ登録
//        $project = $this->createTargetDataForEdit();
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');

        $editGroup6 = $this->searchGroupName($editGroup4->getChildCosts(), 'グループ６');
        $editGroup8 = $this->searchGroupName($editGroup6->getChildCosts(), 'グループ８');

        //cost6
        $cost6 = new ProjectManagerProjectCost();
        $cost6->setName("コスト６");
        $cost6->setCost(6);
        $cost6->setGroupFlag(false);
        $cost6->setSortNo(1);
        $editGroup8->getChildCosts()->add($cost6);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        $this->assertEquals(1, $checkRoots->count());
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(21, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        $this->assertEquals(2, $checkGroup1Costs->count());
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(20, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        $this->assertEquals(2, $checkGroup2Costs->count());
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(13, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        $this->assertEquals(1, $checkGroup3Costs->count());
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        $this->assertEquals(2, $checkGroup5Costs->count());
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(3, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(0, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(4, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        $this->assertEquals(2, $checkGroup4Costs->count());
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(11, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        $this->assertEquals(2, $checkGroup6Costs->count());
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(6, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        $this->assertEquals(1, $checkGroup7Costs->count());
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

        $checkGroup8Costs = $checkGroup8->getChildCosts();
        $this->assertEquals(1, $checkGroup8Costs->count());
        /** @var $checkCost6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost6 = $checkGroup8Costs[0];

        $this->assertEquals('コスト６', $checkCost6->getName());
        $this->assertEquals(6, $checkCost6->getCost());
        $this->assertEquals(1, $checkCost6->getSortNo());
        $this->assertEquals(0, $checkCost6->getNowCost());
        $this->assertEquals(false, $checkCost6->getGroupFlag());

    }

    /**
     * プロジェクト編集　ツリー構造修正1_2　実工数あり
     * @test
     * @group none
     */
    function プロジェクト編集_ツリー構造修正1_2実工数あり()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataWithCostForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');

        $editGroup6 = $this->searchGroupName($editGroup4->getChildCosts(), 'グループ６');
        $editGroup8 = $this->searchGroupName($editGroup6->getChildCosts(), 'グループ８');

        //cost6
        $cost6 = new ProjectManagerProjectCost();
        $cost6->setName("コスト６");
        $cost6->setCost(6);
        $cost6->setGroupFlag(false);
        $cost6->setSortNo(1);
        $editGroup8->getChildCosts()->add($cost6);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        $this->assertEquals(1, $checkRoots->count());
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(21, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(40, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        $this->assertEquals(2, $checkGroup1Costs->count());
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(20, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(40, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        $this->assertEquals(2, $checkGroup2Costs->count());
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(13, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(13, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(27, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        $this->assertEquals(1, $checkGroup3Costs->count());
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(13, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        $this->assertEquals(2, $checkGroup5Costs->count());
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(3, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(13, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(4, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        $this->assertEquals(2, $checkGroup4Costs->count());
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(12, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(11, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(15, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        $this->assertEquals(2, $checkGroup6Costs->count());
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(15, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(6, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        $this->assertEquals(1, $checkGroup7Costs->count());
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(15, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

        $checkGroup8Costs = $checkGroup8->getChildCosts();
        $this->assertEquals(1, $checkGroup8Costs->count());
        /** @var $checkCost6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost6 = $checkGroup8Costs[0];

        $this->assertEquals('コスト６', $checkCost6->getName());
        $this->assertEquals(6, $checkCost6->getCost());
        $this->assertEquals(1, $checkCost6->getSortNo());
        $this->assertEquals(0, $checkCost6->getNowCost());
        $this->assertEquals(false, $checkCost6->getGroupFlag());

    }

    /**
     * プロジェクト編集　ツリー構造修正1_3
     * @test
     * @group none
     */
    function プロジェクト編集_ツリー構造修正1_3()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');

        $editGroup6 = $this->searchGroupName($editGroup4->getChildCosts(), 'グループ６');
        $editGroup8 = $this->searchGroupName($editGroup6->getChildCosts(), 'グループ８');

        //group9
        $group9 = new ProjectManagerProjectCost();
        $group9->setName("グループ９");
        $group9->setGroupFlag(true);
        $group9->setSortNo(1);
        $editGroup8->getChildCosts()->add($group9);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        $this->assertEquals(1, $checkRoots->count());
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(15, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        $this->assertEquals(2, $checkGroup1Costs->count());
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(14, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        $this->assertEquals(2, $checkGroup2Costs->count());
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(7, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        $this->assertEquals(1, $checkGroup3Costs->count());
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        $this->assertEquals(2, $checkGroup5Costs->count());
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(3, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(0, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(4, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        $this->assertEquals(2, $checkGroup4Costs->count());
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        $this->assertEquals(2, $checkGroup6Costs->count());
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        $this->assertEquals(1, $checkGroup7Costs->count());
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

        $checkGroup8Costs = $checkGroup8->getChildCosts();
        $this->assertEquals(1, $checkGroup8Costs->count());
        /** @var $checkGroup9 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup9 = $checkGroup8Costs[0];

        $this->assertEquals('グループ９', $checkGroup9->getName());
        $this->assertEquals(0, $checkGroup9->getCost());
        $this->assertEquals(1, $checkGroup9->getSortNo());
        $this->assertEquals(0, $checkGroup9->getNowCost());
        $this->assertEquals(true, $checkGroup9->getGroupFlag());

    }

    /**
     * プロジェクト編集　ツリー構造修正1_3　実工数あり
     * @test
     * @group none
     */
    function プロジェクト編集_ツリー構造修正1_3実工数あり()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataWithCostForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');

        $editGroup6 = $this->searchGroupName($editGroup4->getChildCosts(), 'グループ６');
        $editGroup8 = $this->searchGroupName($editGroup6->getChildCosts(), 'グループ８');

        //group9
        $group9 = new ProjectManagerProjectCost();
        $group9->setName("グループ９");
        $group9->setGroupFlag(true);
        $group9->setSortNo(1);
        $editGroup8->getChildCosts()->add($group9);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        $this->assertEquals(1, $checkRoots->count());
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(15, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(40, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        $this->assertEquals(2, $checkGroup1Costs->count());
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(14, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(40, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        $this->assertEquals(2, $checkGroup2Costs->count());
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(13, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(7, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(27, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        $this->assertEquals(1, $checkGroup3Costs->count());
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(13, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        $this->assertEquals(2, $checkGroup5Costs->count());
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(3, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(13, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(4, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        $this->assertEquals(2, $checkGroup4Costs->count());
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(12, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(15, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        $this->assertEquals(2, $checkGroup6Costs->count());
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(15, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        $this->assertEquals(1, $checkGroup7Costs->count());
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(15, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

        $checkGroup8Costs = $checkGroup8->getChildCosts();
        $this->assertEquals(1, $checkGroup8Costs->count());
        /** @var $checkGroup9 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup9 = $checkGroup8Costs[0];

        $this->assertEquals('グループ９', $checkGroup9->getName());
        $this->assertEquals(0, $checkGroup9->getCost());
        $this->assertEquals(1, $checkGroup9->getSortNo());
        $this->assertEquals(0, $checkGroup9->getNowCost());
        $this->assertEquals(true, $checkGroup9->getGroupFlag());

    }

    /**
     * プロジェクト編集　ツリー構造修正2_1
     * @test
     * @group none
     */
    function プロジェクト編集_ツリー構造修正2_1()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');

        $editGroup6 = $this->searchGroupName($editGroup4->getChildCosts(), 'グループ６');
        $editGroup7 = $this->searchGroupName($editGroup6->getChildCosts(), 'グループ７');

        //delete cost5
        $editGroup7->getChildCosts()->clear();

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        $this->assertEquals(1, $checkRoots->count());
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(10, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        $this->assertEquals(2, $checkGroup1Costs->count());
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(9, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        $this->assertEquals(2, $checkGroup2Costs->count());
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(2, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        $this->assertEquals(1, $checkGroup3Costs->count());
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        $this->assertEquals(2, $checkGroup5Costs->count());
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(3, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(0, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(4, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        $this->assertEquals(2, $checkGroup4Costs->count());
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(0, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        $this->assertEquals(2, $checkGroup6Costs->count());
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(0, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        $this->assertEquals(0, $checkGroup7Costs->count());

        $checkGroup8Costs = $checkGroup8->getChildCosts();
        $this->assertEquals(0, $checkGroup8Costs->count());


    }

    /**
     * プロジェクト編集　ツリー構造修正2_1　実工数あり
     * @test
     * @expectedException \Arte\PCMS\BizlogicBundle\Lib\ExistProductionCostException
     * @group none
     */
    function プロジェクト編集_ツリー構造修正2_1実工数あり()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataWithCostForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');

        $editGroup6 = $this->searchGroupName($editGroup4->getChildCosts(), 'グループ６');
        $editGroup7 = $this->searchGroupName($editGroup6->getChildCosts(), 'グループ７');

        //delete cost5
        $editGroup7->getChildCosts()->clear();

        //編集
        $this->projectManager->editProject($project);

    }

    /**
     * プロジェクト編集　ツリー構造修正2_3
     * @test
     * @group none
     */
    function プロジェクト編集_ツリー構造修正2_3()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');

        $editGroup6 = $this->searchGroupName($editGroup4->getChildCosts(), 'グループ６');
        $editGroup7 = $this->searchGroupName($editGroup6->getChildCosts(), 'グループ７');

        //delete cost5
        $editGroup7->getChildCosts()->clear();

        //group9
        $group9 = new ProjectManagerProjectCost();
        $group9->setName("グループ９");
        $group9->setGroupFlag(true);
        $group9->setSortNo(1);
        $editGroup7->getChildCosts()->add($group9);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        $this->assertEquals(1, $checkRoots->count());
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(10, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        $this->assertEquals(2, $checkGroup1Costs->count());
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(9, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        $this->assertEquals(2, $checkGroup2Costs->count());
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(2, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        $this->assertEquals(1, $checkGroup3Costs->count());
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(0, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        $this->assertEquals(2, $checkGroup5Costs->count());
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(3, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(0, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(4, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        $this->assertEquals(2, $checkGroup4Costs->count());
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(0, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        $this->assertEquals(2, $checkGroup6Costs->count());
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(0, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        $this->assertEquals(1, $checkGroup7Costs->count());
        /** @var $checkGroup9 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup9 = $checkGroup7Costs[0];

        $this->assertEquals('グループ９', $checkGroup9->getName());
        $this->assertEquals(0, $checkGroup9->getCost());
        $this->assertEquals(1, $checkGroup9->getSortNo());
        $this->assertEquals(0, $checkGroup9->getNowCost());
        $this->assertEquals(true, $checkGroup9->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup8Costs = $checkGroup8->getChildCosts();
        $this->assertEquals(0, $checkGroup8Costs->count());


    }

    /**
     * プロジェクト編集　ツリー構造修正2_3　実工数あり
     * @test
     * @expectedException \Arte\PCMS\BizlogicBundle\Lib\ExistProductionCostException
     * @group none
     */
    function プロジェクト編集_ツリー構造修正2_3実工数あり()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataWithCostForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');

        $editGroup6 = $this->searchGroupName($editGroup4->getChildCosts(), 'グループ６');
        $editGroup7 = $this->searchGroupName($editGroup6->getChildCosts(), 'グループ７');

        //delete cost5
        $editGroup7->getChildCosts()->clear();

        //group9
        $group9 = new ProjectManagerProjectCost();
        $group9->setName("グループ９");
        $group9->setGroupFlag(true);
        $group9->setSortNo(1);
        $editGroup7->getChildCosts()->add($group9);

        //編集
        $this->projectManager->editProject($project);

    }

    /**
     * プロジェクト編集　ツリー構造修正3_1
     * @test
     * @group none
     */
    function プロジェクト編集_ツリー構造修正3_1()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup3 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ３');

        //delete group5
        $editGroup3->getChildCosts()->clear();

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        $this->assertEquals(1, $checkRoots->count());
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(8, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        $this->assertEquals(2, $checkGroup1Costs->count());
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(7, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        $this->assertEquals(2, $checkGroup2Costs->count());
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(0, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(7, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        $this->assertEquals(0, $checkGroup3Costs->count());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        $this->assertEquals(2, $checkGroup4Costs->count());
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        $this->assertEquals(2, $checkGroup6Costs->count());
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        $this->assertEquals(1, $checkGroup7Costs->count());
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup8Costs = $checkGroup8->getChildCosts();
        $this->assertEquals(0, $checkGroup8Costs->count());


    }

    /**
     * プロジェクト編集　ツリー構造修正3_1　実工数あり
     * @test
     * @expectedException \Arte\PCMS\BizlogicBundle\Lib\ExistProductionCostException
     * @group none
     */
    function プロジェクト編集_ツリー構造修正3_1実工数あり()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataWithCostForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup3 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ３');

        //delete group5
        $editGroup3->getChildCosts()->clear();

        //編集
        $this->projectManager->editProject($project);


    }

    /**
     * プロジェクト編集　ツリー構造修正3_2
     * @test
     * @group none
     */
    function プロジェクト編集_ツリー構造修正3_2()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup3 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ３');

        //delete group5
        $editGroup3->getChildCosts()->clear();

        //cost6
        $cost6 = new ProjectManagerProjectCost();
        $cost6->setName("コスト６");
        $cost6->setCost(6);
        $cost6->setGroupFlag(false);
        $cost6->setSortNo(1);
        $editGroup3->getChildCosts()->add($cost6);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        $this->assertEquals(1, $checkRoots->count());
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(14, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(0, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        $this->assertEquals(2, $checkGroup1Costs->count());
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(13, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(0, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        $this->assertEquals(2, $checkGroup2Costs->count());
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(6, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(0, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(7, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(0, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        $this->assertEquals(1, $checkGroup3Costs->count());
        /** @var $checkCost6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost6 = $checkGroup3Costs[0];

        $this->assertEquals('コスト６', $checkCost6->getName());
        $this->assertEquals(6, $checkCost6->getCost());
        $this->assertEquals(1, $checkCost6->getSortNo());
        $this->assertEquals(0, $checkCost6->getNowCost());
        $this->assertEquals(false, $checkCost6->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        $this->assertEquals(2, $checkGroup4Costs->count());
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(0, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(0, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        $this->assertEquals(2, $checkGroup6Costs->count());
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(0, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        $this->assertEquals(1, $checkGroup7Costs->count());
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(0, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup8Costs = $checkGroup8->getChildCosts();
        $this->assertEquals(0, $checkGroup8Costs->count());


    }

    /**
     * プロジェクト編集　ツリー構造修正3_2　実工数あり
     * @test
     * @expectedException \Arte\PCMS\BizlogicBundle\Lib\ExistProductionCostException
     * @group none
     */
    function プロジェクト編集_ツリー構造修正3_2実工数あり()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataWithCostForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup3 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ３');

        //delete group5
        $editGroup3->getChildCosts()->clear();

        //cost6
        $cost6 = new ProjectManagerProjectCost();
        $cost6->setName("コスト６");
        $cost6->setCost(6);
        $cost6->setGroupFlag(false);
        $cost6->setSortNo(1);
        $editGroup3->getChildCosts()->add($cost6);

        //編集
        $this->projectManager->editProject($project);

    }

    /**
     * プロジェクト編集　ソート編集３
     * @test
     * @group none
     */
    function プロジェクト編集_ソート編集３()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataWithCostForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup3 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ３');
        $editGroup5 = $this->searchGroupName($editGroup3->getChildCosts(), 'グループ５');
        $editCost3 = $this->searchCostName($editGroup5->getChildCosts(), 'コスト３');
        $editCost4 = $this->searchCostName($editGroup5->getChildCosts(), 'コスト４');

        $editCost3->setSortNo(2);
        $editCost4->setSortNo(1);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        $this->assertEquals(1, $checkRoots->count());
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(15, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(40, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        $this->assertEquals(2, $checkGroup1Costs->count());
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(14, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(40, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        $this->assertEquals(2, $checkGroup2Costs->count());
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(13, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(7, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(27, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        $this->assertEquals(1, $checkGroup3Costs->count());
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(13, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        $this->assertEquals(2, $checkGroup5Costs->count());
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(3, $checkCost3->getCost());
        $this->assertEquals(2, $checkCost3->getSortNo());
        $this->assertEquals(13, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(4, $checkCost4->getCost());
        $this->assertEquals(1, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        $this->assertEquals(2, $checkGroup4Costs->count());
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(12, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(15, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        $this->assertEquals(2, $checkGroup6Costs->count());
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(1, $checkGroup7->getSortNo());
        $this->assertEquals(15, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(2, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        $this->assertEquals(1, $checkGroup7Costs->count());
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(15, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

        $checkGroup8Costs = $checkGroup8->getChildCosts();
        $this->assertEquals(0, $checkGroup8Costs->count());

    }

    /**
     * プロジェクト編集　ソート編集５
     * @test
     * @group none
     */
    function プロジェクト編集_ソート編集５()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataWithCostForEdit();

        //
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');

        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup4 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ４');
        $editGroup5 = $this->searchGroupName($editGroup4->getChildCosts(), 'グループ６');
        $editGroup6 = $this->searchGroupName($editGroup5->getChildCosts(), 'グループ７');
        $editGroup7 = $this->searchGroupName($editGroup5->getChildCosts(), 'グループ８');

        $editGroup6->setSortNo(2);
        $editGroup7->setSortNo(1);

        //編集
        $this->projectManager->editProject($project);
        //取得
        $project = $this->projectManager->readProject($project->getId());

        //確認
        $this->assertEquals('新規登録テスト１', $project->getName());
        $this->assertEquals(1, $project->getStatus());
        $this->assertEquals("新規登録テスト１説明\n新規登録テスト１説明", $project->getExplanation());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $project->getPeriodStart());
        $this->assertEquals(new \DateTime("2013-08-7 0:0:0"), $project->getPeriodEnd());
        $this->assertEquals('見積ファイルパス', $project->getEstimateFilePath());
        $this->assertEquals('スケジュールファイルパス', $project->getScheduleFilePath());
        $this->assertEquals('顧客1', $project->getCustomer()->getName());
        $this->assertEquals('てすと001', $project->getManager()->getDisplayName());

        $checkRoots = $project->getCosts();
        $this->assertEquals(1, $checkRoots->count());
        /** @var $checkGroup1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup1 = $checkRoots[0];

        $this->assertEquals('グループ１', $checkGroup1->getName());
        $this->assertEquals(15, $checkGroup1->getCost());
        $this->assertEquals(1, $checkGroup1->getSortNo());
        $this->assertEquals(40, $checkGroup1->getNowCost());
        $this->assertEquals(true, $checkGroup1->getGroupFlag());

        $checkGroup1Costs = $checkGroup1->getChildCosts();
        $this->assertEquals(2, $checkGroup1Costs->count());
        /** @var $checkGroup2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup2 = $checkGroup1Costs[0];
        /** @var $checkCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost1 = $checkGroup1Costs[1];

        $this->assertEquals('グループ２', $checkGroup2->getName());
        $this->assertEquals(14, $checkGroup2->getCost());
        $this->assertEquals(1, $checkGroup2->getSortNo());
        $this->assertEquals(40, $checkGroup2->getNowCost());
        $this->assertEquals(true, $checkGroup2->getGroupFlag());

        $this->assertEquals('コスト１', $checkCost1->getName());
        $this->assertEquals(1, $checkCost1->getCost());
        $this->assertEquals(2, $checkCost1->getSortNo());
        $this->assertEquals(0, $checkCost1->getNowCost());
        $this->assertEquals(false, $checkCost1->getGroupFlag());

        $checkGroup2Costs = $checkGroup2->getChildCosts();
        $this->assertEquals(2, $checkGroup2Costs->count());
        /** @var $checkGroup3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup3 = $checkGroup2Costs[0];
        /** @var $checkGroup4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup4 = $checkGroup2Costs[1];

        $this->assertEquals('グループ３', $checkGroup3->getName());
        $this->assertEquals(7, $checkGroup3->getCost());
        $this->assertEquals(1, $checkGroup3->getSortNo());
        $this->assertEquals(13, $checkGroup3->getNowCost());
        $this->assertEquals(true, $checkGroup3->getGroupFlag());

        $this->assertEquals('グループ４', $checkGroup4->getName());
        $this->assertEquals(7, $checkGroup4->getCost());
        $this->assertEquals(2, $checkGroup4->getSortNo());
        $this->assertEquals(27, $checkGroup4->getNowCost());
        $this->assertEquals(true, $checkGroup4->getGroupFlag());

        $checkGroup3Costs = $checkGroup3->getChildCosts();
        $this->assertEquals(1, $checkGroup3Costs->count());
        /** @var $checkGroup5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup5 = $checkGroup3Costs[0];

        $this->assertEquals('グループ５', $checkGroup5->getName());
        $this->assertEquals(7, $checkGroup5->getCost());
        $this->assertEquals(1, $checkGroup5->getSortNo());
        $this->assertEquals(13, $checkGroup5->getNowCost());
        $this->assertEquals(true, $checkGroup5->getGroupFlag());

        $checkGroup5Costs = $checkGroup5->getChildCosts();
        $this->assertEquals(2, $checkGroup5Costs->count());
        /** @var $checkCost3 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost3 = $checkGroup5Costs[0];
        /** @var $checkCost4 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost4 = $checkGroup5Costs[1];

        $this->assertEquals('コスト３', $checkCost3->getName());
        $this->assertEquals(3, $checkCost3->getCost());
        $this->assertEquals(1, $checkCost3->getSortNo());
        $this->assertEquals(13, $checkCost3->getNowCost());
        $this->assertEquals(false, $checkCost3->getGroupFlag());

        $this->assertEquals('コスト４', $checkCost4->getName());
        $this->assertEquals(4, $checkCost4->getCost());
        $this->assertEquals(2, $checkCost4->getSortNo());
        $this->assertEquals(0, $checkCost4->getNowCost());
        $this->assertEquals(false, $checkCost4->getGroupFlag());

        $checkGroup4Costs = $checkGroup4->getChildCosts();
        $this->assertEquals(2, $checkGroup4Costs->count());
        /** @var $checkCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost2 = $checkGroup4Costs[0];
        /** @var $checkGroup6 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup6 = $checkGroup4Costs[1];

        $this->assertEquals('コスト２', $checkCost2->getName());
        $this->assertEquals(2, $checkCost2->getCost());
        $this->assertEquals(1, $checkCost2->getSortNo());
        $this->assertEquals(12, $checkCost2->getNowCost());
        $this->assertEquals(false, $checkCost2->getGroupFlag());

        $this->assertEquals('グループ６', $checkGroup6->getName());
        $this->assertEquals(5, $checkGroup6->getCost());
        $this->assertEquals(2, $checkGroup6->getSortNo());
        $this->assertEquals(15, $checkGroup6->getNowCost());
        $this->assertEquals(true, $checkGroup6->getGroupFlag());

        $checkGroup6Costs = $checkGroup6->getChildCosts();
        $this->assertEquals(2, $checkGroup6Costs->count());
        /** @var $checkGroup7 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup8 = $checkGroup6Costs[0];
        /** @var $checkGroup8 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkGroup7 = $checkGroup6Costs[1];

        $this->assertEquals('グループ７', $checkGroup7->getName());
        $this->assertEquals(5, $checkGroup7->getCost());
        $this->assertEquals(2, $checkGroup7->getSortNo());
        $this->assertEquals(15, $checkGroup7->getNowCost());
        $this->assertEquals(true, $checkGroup7->getGroupFlag());

        $this->assertEquals('グループ８', $checkGroup8->getName());
        $this->assertEquals(0, $checkGroup8->getCost());
        $this->assertEquals(1, $checkGroup8->getSortNo());
        $this->assertEquals(0, $checkGroup8->getNowCost());
        $this->assertEquals(true, $checkGroup8->getGroupFlag());

        $checkGroup7Costs = $checkGroup7->getChildCosts();
        $this->assertEquals(1, $checkGroup7Costs->count());
        /** @var $checkCost5 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
        $checkCost5 = $checkGroup7Costs[0];

        $this->assertEquals('コスト５', $checkCost5->getName());
        $this->assertEquals(5, $checkCost5->getCost());
        $this->assertEquals(1, $checkCost5->getSortNo());
        $this->assertEquals(15, $checkCost5->getNowCost());
        $this->assertEquals(false, $checkCost5->getGroupFlag());

        $checkGroup8Costs = $checkGroup8->getChildCosts();
        $this->assertEquals(0, $checkGroup8Costs->count());

    }

    /**
     * プロジェクト削除　削除１
     * @test
     * @expectedException \Doctrine\ORM\NoResultException
     * @group none
     */
    function プロジェクト削除_削除１()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //取得
        $project = $this->projectManager->readProject($project->getId());

        //削除
        $this->projectManager->deleteProject($project);

        $this->projectManager->readProject($project->getId());
    }

    /**
     * プロジェクト削除　DB確認１
     * @test
     * @group none
     */
    function プロジェクト削除_DB確認１()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //取得
        $project = $this->projectManager->readProject($project->getId());

        //削除
        $this->projectManager->deleteProject($project);

        //db check
        //プロジェクト情報取得
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder =$this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBProjectMaster')
            ->createQueryBuilder('p')
            ->select(array(
                'p',
            ))
            ->andWhere('p.id = :id')
            ->andWhere('p.DeleteFlag = false')
            ->setParameter('id', $project->getId())
        ;
        /** @var $entity TBProjectMaster */
        $tbProjectMaster = $queryBuilder->getQuery()->getResult();
        $this->assertEquals(0, count($tbProjectMaster));

        //工数詳細取得
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBProjectCostMaster')
            ->createQueryBuilder('pcm')
            ->select(array(
                'pcm',
            ))
            ->andWhere('pcm.ProjectMasterId = :ProjectMasterId')
            ->andWhere('pcm.DeleteFlag = false')
            ->setParameter('ProjectMasterId', $project->getId())
        ;
        $tbProjectCostMasters = $queryBuilder->getQuery()->getResult();
        $this->assertEquals(0, count($tbProjectCostMasters));

        //グループ取得
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBProjectCostHierarchyMaster')
            ->createQueryBuilder('pchm')
            ->select(array(
                'pchm',
            ))
            ->andWhere('pchm.TBProjectMasterId = :TBProjectMasterId')
            ->andWhere('pchm.DeleteFlag = false')
            ->setParameter('TBProjectMasterId', $project->getId())
        ;
        $tbProjectCostHierarchyMasters = $queryBuilder->getQuery()->getResult();
        $this->assertEquals(0, count($tbProjectCostHierarchyMasters));

    }

    /**
     * 実工数一覧取得１
     * @test
     * @group none
     */
    function 実工数一覧取得１()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataWithCostForEdit();

        //取得
        $project = $this->projectManager->readProject($project->getId());

        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');
        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup3 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ３');
        $editGroup5 = $this->searchGroupName($editGroup3->getChildCosts(), 'グループ５');
        $editCost3 = $this->searchCostName($editGroup5->getChildCosts(), 'コスト３');

        //
        $costs = $this->projectManager->getProductionCosts($editCost3->getId());

        //
        $this->assertEquals(2, count($costs));

        /** @var $productionCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProductionCost */
        $productionCost1 = $costs[0];
        $this->assertEquals('コスト３', $productionCost1->getProjectCost()->getName());
        $this->assertEquals('てすと005', $productionCost1->getWorker()->getName());
        $this->assertEquals(11, $productionCost1->getCost());
        $this->assertEquals(new \DateTime("2013-08-2 0:0:0"), $productionCost1->getWorkDate());
        $this->assertEquals('実工数３－１', $productionCost1->getNote());

        /** @var $productionCost2 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProductionCost */
        $productionCost2 = $costs[1];
        $this->assertEquals('コスト３', $productionCost2->getProjectCost()->getName());
        $this->assertEquals('てすと006', $productionCost2->getWorker()->getName());
        $this->assertEquals(2, $productionCost2->getCost());
        $this->assertEquals(new \DateTime("2013-08-3 0:0:0"), $productionCost2->getWorkDate());
        $this->assertEquals('実工数３－２', $productionCost2->getNote());

    }

    /**
     * 実工数取得３
     * @test
     * @group none
     */
    function 実工数取得３()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataWithCostForEdit();

        //対応コストID取得
        $project = $this->projectManager->readProject($project->getId());
        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');
        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup3 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ３');
        $editGroup5 = $this->searchGroupName($editGroup3->getChildCosts(), 'グループ５');
        $editCost3 = $this->searchCostName($editGroup5->getChildCosts(), 'コスト３');
        $costs = $this->projectManager->getProductionCosts($editCost3->getId());
        $productionCost1Id = $costs[0]->getId();
        $this->em->clear();//emクリアー

        //コスト取得
        /** @var $productionCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProductionCost */
        $productionCost1 = $this->projectManager->getProductionCost($productionCost1Id);

        //確認
        $this->assertEquals('コスト３', $productionCost1->getProjectCost()->getName());
        $this->assertEquals('てすと005', $productionCost1->getWorker()->getName());
        $this->assertEquals(11, $productionCost1->getCost());
        $this->assertEquals(new \DateTime("2013-08-2 0:0:0"), $productionCost1->getWorkDate());
        $this->assertEquals('実工数３－１', $productionCost1->getNote());

    }

    /**
     * 実工数登録　コスト１
     * @test
     * @group none
     */
    function 実工数登録_コスト１()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataForEdit();

        //取得
        $project = $this->projectManager->readProject($project->getId());
        $worker = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);
        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');
        $editCost1 = $this->searchCostName($editGroup1->getChildCosts(), 'コスト１');

        //工数新規登録
        $productionCost = new ProjectManagerProductionCost();
        $productionCost->setProjectCost($editCost1);
        $productionCost->setWorker($worker);
        $productionCost->setCost(11);
        $productionCost->setWorkDate(new \DateTime("2013-08-1 0:0:0"));
        $productionCost->setNote("工数登録１\n工数登録１");
        $cost = $this->projectManager->createProductionCost($productionCost);

        //DBに登録されてあるか確認
        $this->em->clear();
        /* @var $tbProductionCost TBProductionCost */
        $tbProductionCost = $this->em->getRepository('ArtePCMSBizlogicBundle:TBProductionCost')->find($cost->getId());
        $tbProjectCostMaster =  $tbProductionCost->getTBProjectCostMasterProjectCostMasterId();
        $tbSystemUser = $tbProductionCost->getTBSystemUserSystemUserId();

        $this->assertEquals('コスト１', $tbProjectCostMaster->getName());
        $this->assertEquals('てすと001', $tbSystemUser->getName());
        $this->assertEquals(11, $tbProductionCost->getCost());
        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $tbProductionCost->getWorkDate());
        $this->assertEquals("工数登録１\n工数登録１", $tbProductionCost->getNote());
        $this->assertEquals(false, $tbProductionCost->getDeleteFlag());

    }

    /**
     * 実工数編集　コスト３_１
     * @test
     * @group debug
     */
    function 実工数編集_コスト３_１()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataWithCostForEdit();

        //対応コストID取得

        $project = $this->projectManager->readProject($project->getId());
        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');
        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup3 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ３');
        $editGroup5 = $this->searchGroupName($editGroup3->getChildCosts(), 'グループ５');
        $editCost3 = $this->searchCostName($editGroup5->getChildCosts(), 'コスト３');
        $editCost4 = $this->searchCostName($editGroup5->getChildCosts(), 'コスト４');
        $costs = $this->projectManager->getProductionCosts($editCost3->getId());
        $productionCost1Id = $costs[0]->getId();
        $this->em->clear();//emクリアー

        //コスト取得
        /** @var $productionCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProductionCost */
        $productionCost1 = $this->projectManager->getProductionCost($productionCost1Id);

        $worker = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(4);
        $productionCost1->setProjectCost($editCost4);
        $productionCost1->setWorker($worker);
        $productionCost1->setCost(21);
        $productionCost1->setWorkDate(new \DateTime("2013-08-2 0:0:0"));
        $productionCost1->setNote("工数編集３－１\n工数編集３－１");
        $productionCost1 = $this->projectManager->editProductionCost($productionCost1);

        //DBに登録されてあるか確認
        $this->em->clear();
        /* @var $tbProductionCost TBProductionCost */
        $tbProductionCost = $this->em->getRepository('ArtePCMSBizlogicBundle:TBProductionCost')->find($productionCost1->getId());
        $tbProjectCostMaster =  $tbProductionCost->getTBProjectCostMasterProjectCostMasterId();
        $tbSystemUser = $tbProductionCost->getTBSystemUserSystemUserId();

        $this->assertEquals('コスト４', $tbProjectCostMaster->getName());
        $this->assertEquals('てすと004', $tbSystemUser->getName());
        $this->assertEquals(21, $tbProductionCost->getCost());
        $this->assertEquals(new \DateTime("2013-08-2 0:0:0"), $tbProductionCost->getWorkDate());
        $this->assertEquals("工数編集３－１\n工数編集３－１", $tbProductionCost->getNote());
        $this->assertEquals(false, $tbProductionCost->getDeleteFlag());

    }

    /**
     * 実工数削除　コスト３_１
     * @test
     * @group none
     */
    function 実工数削除_コスト３_１()
    {
        //テスト用データ登録
        $project = $this->createTargetRawDataWithCostForEdit();

        //対応コストID取得
        $project = $this->projectManager->readProject($project->getId());
        $editRoots = $project->getCosts();
        $editGroup1 = $this->searchGroupName($editRoots, 'グループ１');
        $editGroup2 = $this->searchGroupName($editGroup1->getChildCosts(), 'グループ２');
        $editGroup3 = $this->searchGroupName($editGroup2->getChildCosts(), 'グループ３');
        $editGroup5 = $this->searchGroupName($editGroup3->getChildCosts(), 'グループ５');
        $editCost3 = $this->searchCostName($editGroup5->getChildCosts(), 'コスト３');
        $costs = $this->projectManager->getProductionCosts($editCost3->getId());
        $productionCost1Id = $costs[0]->getId();
        $this->em->clear();//emクリアー

        //コスト取得
        /** @var $productionCost1 \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProductionCost */
        $productionCost1 = $this->projectManager->getProductionCost($productionCost1Id);
        $this->em->clear();//emクリアー

        //コスト削除
        $this->projectManager->deleteProductionCost($productionCost1->getId());

        //DBに登録されてあるか確認
        $this->em->clear();
        /* @var $tbProductionCost TBProductionCost */
        $tbProductionCost = $this->em->getRepository('ArtePCMSBizlogicBundle:TBProductionCost')->find($productionCost1->getId());
        $tbProjectCostMaster =  $tbProductionCost->getTBProjectCostMasterProjectCostMasterId();
        $tbSystemUser = $tbProductionCost->getTBSystemUserSystemUserId();

        $this->assertEquals(true, $tbProductionCost->getDeleteFlag());

    }

//    /**
//     * プロジェクト情報取得
//     */
//    public function test1readProject()
//    {
//        $data = $this->projectManager->readProject(1);
//        $this->assertEquals('案件3', $data->getName());
//        $this->assertEquals(1, $data->getStatus());
//        $this->assertEquals('案件3説明', $data->getExplanation());
//        $this->assertEquals(new \DateTime("2013-08-1 0:0:0"), $data->getPeriodStart());
//        $this->assertEquals(new \DateTime("2013-08-5 0:0:0"), $data->getPeriodEnd());
//        $this->assertEquals('estimate3', $data->getEstimateFilePath());
//        $this->assertEquals('schedule3', $data->getScheduleFilePath());
//        $this->assertEquals('顧客1', $data->getCustomer()->getName());
//        $this->assertEquals('てすと001', $data->getManager()->getName());
//
//        $costs = $data->getCosts();
//        /** @var $cost \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
//        $cost = $costs[0];
//        $this->assertEquals('納品', $cost->getName());
//        $this->assertEquals(480, $cost->getCost());
//        $this->assertEquals(1, $cost->getSortNo());
//        $this->assertEquals(0, $cost->getNowCost());
//        $this->assertEquals(false, $cost->getGroupFlag());
//
//        /** @var $cost \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
//        $cost = $costs[1];
//        $this->assertEquals('製造', $cost->getName());
//        $this->assertEquals(10080, $cost->getCost());
//        $this->assertEquals(2, $cost->getSortNo());
//        $this->assertEquals(780, $cost->getNowCost());
//        $this->assertEquals(true, $cost->getGroupFlag());
//
//        $childCosts1 = $cost->getChildCosts();
//
//        //製造＞一般側
//        /** @var $cost \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
//        $cost = $childCosts1[0];
//        $this->assertEquals('一般側', $cost->getName());
//        $this->assertEquals(5280, $cost->getCost());
//        $this->assertEquals(1, $cost->getSortNo());
//        $this->assertEquals(0, $cost->getNowCost());
//        $this->assertEquals(true, $cost->getGroupFlag());
//
//        $childCosts2 = $cost->getChildCosts();
//
//        //製造＞一般側＞申請管理
//        /** @var $cost \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
//        $cost = $childCosts2[0];
//        $this->assertEquals('申請管理', $cost->getName());
//        $this->assertEquals(4320, $cost->getCost());
//        $this->assertEquals(1, $cost->getSortNo());
//        $this->assertEquals(0, $cost->getNowCost());
//        $this->assertEquals(false, $cost->getGroupFlag());
//
//        //製造＞一般側＞ログイン
//        /** @var $cost \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
//        $cost = $childCosts2[1];
//        $this->assertEquals('ログイン', $cost->getName());
//        $this->assertEquals(960, $cost->getCost());
//        $this->assertEquals(2, $cost->getSortNo());
//        $this->assertEquals(0, $cost->getNowCost());
//        $this->assertEquals(false, $cost->getGroupFlag());
//
//        //製造＞管理側
//        /** @var $cost \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
//        $cost = $childCosts1[1];
//        $this->assertEquals('管理側', $cost->getName());
//        $this->assertEquals(4800, $cost->getCost());
//        $this->assertEquals(2, $cost->getSortNo());
//        $this->assertEquals(780, $cost->getNowCost());
//        $this->assertEquals(true, $cost->getGroupFlag());
//
//        $childCosts2 = $cost->getChildCosts();
//
//        //製造＞管理側＞ユーザー管理
//        /** @var $cost \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
//        $cost = $childCosts2[0];
//        $this->assertEquals('ユーザー管理', $cost->getName());
//        $this->assertEquals(2880, $cost->getCost());
//        $this->assertEquals(1, $cost->getSortNo());
//        $this->assertEquals(780, $cost->getNowCost());
//        $this->assertEquals(false, $cost->getGroupFlag());
//
//        //製造＞一般側＞顧客管理
//        /** @var $cost \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
//        $cost = $childCosts2[1];
//        $this->assertEquals('顧客管理', $cost->getName());
//        $this->assertEquals(1920, $cost->getCost());
//        $this->assertEquals(2, $cost->getSortNo());
//        $this->assertEquals(0, $cost->getNowCost());
//        $this->assertEquals(false, $cost->getGroupFlag());
//
//
//
//        //設計
//        /** @var $cost \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
//        $cost = $costs[2];
//        $this->assertEquals('設計', $cost->getName());
//        $this->assertEquals(2400, $cost->getCost());
//        $this->assertEquals(3, $cost->getSortNo());
//        $this->assertEquals(0, $cost->getNowCost());
//        $this->assertEquals(false, $cost->getGroupFlag());
//
//        //要件定義
//        /** @var $cost \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost */
//        $cost = $costs[3];
//        $this->assertEquals('要件定義', $cost->getName());
//        $this->assertEquals(1440, $cost->getCost());
//        $this->assertEquals(4, $cost->getSortNo());
//        $this->assertEquals(600, $cost->getNowCost());
//        $this->assertEquals(false, $cost->getGroupFlag());
//
//    }

    public static function setUpBeforeClass()
    {
//        fwrite(STDOUT, __METHOD__ . "\n");
    }

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->projectManager = new ProjectManager($this->em);

/*
        $rootPath = $kernel->getRootDir();
        exec('php '.$rootPath.'/console doctrine:database:drop --force');
        exec('php '.$rootPath.'/console doctrine:database:create');
        //import
        $bundle = $kernel->getBundle('ArtePCMSBizlogicBundle');
        $bundlePath = $bundle->getPath();
        $dbDumpFilePath = $bundlePath . '/Resources/sql/dumpfile.sql';
        $sql = file_get_contents($dbDumpFilePath);
        $this->em->getConnection()->query($sql);
*/

/*
        //doctrine:database:drop --force
        $app = new Application($kernel);
        $arguments = array(
            'command' => 'doctrine:database:drop',
            '--force' => true,
        );
        $input = new ArrayInput($arguments);
//        $output = new ConsoleOutput();
        $output = new NullOutput();
        $app->run($input, $output);

        //doctrine:database:drop --force
        $arguments = array(
            'command' => 'doctrine:database:create',
        );
        $input = new ArrayInput($arguments);
        $app->run($input, $output);

        //import
        $bundle = $kernel->getBundle('ArtePCMSBizlogicBundle');
        $bundlePath = $bundle->getPath();
        $dbDumpFilePath = $bundlePath . '/Resources/sql/dumpfile.sql';
        $sql = file_get_contents($dbDumpFilePath);
        $this->em->getConnection()->query($sql);
*/

////        $command = $app->find('doctrine');
//        $arguments = array(
//            'command' => 'doctrine:database:create',
////            'name'    => 'Fabien',
////            '--yell'  => true,
//        );
//
//        $input = new ArrayInput($arguments);
//
//        $output = new NullOutput();
//        $returnCode = $command->run($input, $output);

//        $app->setAutoExit(false);
//        $input = new StringInput("doctrine:database:create");
//        $output = new BufferedOutput();
//
//        $error = $app->run($input, $output);

//        //init db
//        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
//        $a = $em->getConnection()->query('show tables;');

        //init target id
//        $this->targetTBCustomerId = 46;
//        $this->targetTBSystemUserManagerId = 151;
    }

    protected function tearDown()
    {
    }

    public static function tearDownAfterClass()
    {
    }
}
