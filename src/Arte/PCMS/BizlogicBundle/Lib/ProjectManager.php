<?php
/**
 * プロジェクト管理クラス
 */

namespace Arte\PCMS\BizlogicBundle\Lib;


use Arte\PCMS\BizlogicBundle\Entity\TBCustomer;
use Arte\PCMS\BizlogicBundle\Entity\TBProductionCost;
use Arte\PCMS\BizlogicBundle\Entity\TBProjectCostHierarchyMaster;
use Arte\PCMS\BizlogicBundle\Entity\TBProjectCostMaster;
use Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster;
use Arte\PCMS\BizlogicBundle\Entity\TBSystemUser;
use Arte\PCMS\BizlogicBundle\Entity\VProjectView;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Config\Definition\Exception\Exception;

class ProjectManager {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * プロジェクト新規登録
     * @param ProjectManagerProject $project
     * @return ProjectManagerProject
     * @throws \Exception
     */
    public function createProject(ProjectManagerProject $project)
    {
        $tbProjectMaster = new TBProjectMaster();

        //子コストの名称が'\\'の場合はエラー

        $this->em->getConnection()->beginTransaction();
        try {

            //プロジェクト
            $tbProjectMaster->setName($project->getName());
            $tbProjectMaster->setStatus($project->getStatus());
            $tbProjectMaster->setExplanation($project->getExplanation());
            $tbProjectMaster->setPeriodStart($project->getPeriodStart());
            $tbProjectMaster->setPeriodEnd($project->getPeriodEnd());
            $tbProjectMaster->setEstimateFilePath($project->getEstimateFilePath());
            $tbProjectMaster->setScheduleFilePath($project->getScheduleFilePath());
            $tbProjectMaster->setTBCustomerCustomerId($project->getCustomer());
            $tbProjectMaster->setTBSystemUserManagerId($project->getManager());
            $tbProjectMaster->setDeleteFlag(false);
            $this->em->persist($tbProjectMaster);
            $this->em->flush();

            //rootコスト登録
            $tbProjectCostHierarchyMaster = new TBProjectCostHierarchyMaster();
            $tbProjectCostHierarchyMaster->setName('\\');
            $tbProjectCostHierarchyMaster->setPath('\\');
            $tbProjectCostHierarchyMaster->setSortNo(0);
            $tbProjectCostHierarchyMaster->setDeleteFlag(false);
            $tbProjectCostHierarchyMaster->setTBProjectMasterTBProjectMasterId($tbProjectMaster);
            $this->em->persist($tbProjectCostHierarchyMaster);
            $this->em->flush();

            //子コスト登録
            $this->createNewCosts($project->getCosts(), $tbProjectMaster, $tbProjectCostHierarchyMaster);

            $this->em->getConnection()->commit();

        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
        }

        //プロジェクト取得
        $project = $this->readProject($tbProjectMaster->getId());
        return $project;
    }

    /**
     * 子コスト登録
     * @param ArrayCollection $costs
     * @param TBProjectMaster $tbProject
     * @param TBProjectCostHierarchyMaster $tbProjectHierarchy
     */
    private function createNewCosts(ArrayCollection $costs, TBProjectMaster &$tbProject, TBProjectCostHierarchyMaster &$tbProjectHierarchy)
    {
        $sortNo = 1;
        foreach($costs as $key => $value)
        {
            /** @var $value ProjectManagerProjectCost */
            if($value->getGroupFlag()){

                $group = new TBProjectCostHierarchyMaster();
                $group->setName($value->getName());
                $group->setPath($tbProjectHierarchy->getPath().$tbProjectHierarchy->getId().'\\');
//                $group->setSortNo($value->getSortNo());
                $group->setSortNo($sortNo);
                $group->setDeleteFlag(false);
                $group->setTBProjectMasterTBProjectMasterId($tbProject);

                $this->em->persist($group);
                $this->em->flush();

                $this->createNewCosts($value->getChildCosts(), $tbProject, $group);

            }else{

                $costEntity = new TBProjectCostMaster();
                $costEntity->setName($value->getName());
                $costEntity->setCost($value->getCost());
//                $costEntity->setSortNo($value->getSortNo());
                $costEntity->setSortNo($sortNo);
                $costEntity->setDeleteFlag(false);
                $costEntity->setTBProjectMasterProjectMasterId($tbProject);
                $costEntity->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($tbProjectHierarchy);

                $this->em->persist($costEntity);
                $this->em->flush();

            }
            $sortNo++;
        }
    }

    /**
     * プロジェクト編集
     * @param ProjectManagerProject $project
     * @throws \Exception
     */
    public function editProject(ProjectManagerProject $project)
    {




        $this->em->getConnection()->beginTransaction();
        try {

            //プロジェクト情報取得
            /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
            $queryBuilder =$this->em
                ->getRepository('ArtePCMSBizlogicBundle:TBProjectMaster')
                ->createQueryBuilder('p')
                ->leftJoin('p.TBCustomerCustomerId', 'c')
                ->leftJoin('p.TBSystemUserManagerId', 'u')
//                ->leftJoin('p.TBProjectCostMastersProjectMasterId', 'pcm')
                ->select(array(
                    'p',
                    'c',
                    'u',
//                    'pcm',
                ))
                ->andWhere('p.id = :id')
                ->andWhere('p.DeleteFlag = false')
//                ->andWhere('pcm.DeleteFlag = false')
                ->setParameter('id', $project->getId())
            ;
            /** @var $entity TBProjectMaster */
            $tbProjectMaster = $queryBuilder->getQuery()->getSingleResult();

            //工数詳細取得
            /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
            $queryBuilder = $this->em
                ->getRepository('ArtePCMSBizlogicBundle:TBProjectCostMaster')
                ->createQueryBuilder('pcm')
                ->innerJoin('pcm.TBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId', 'pchm')
//                ->leftJoin('pcm.TBProductionCostsProjectCostMasterId', 'pc')
                ->select(array(
//                    'coalesce(sum(pc.Cost), 0) as SumCost',
//                    'pcm as TBProjectCostMaster',
                    'pcm',
                    'pchm',
                ))
//                ->groupBy('pcm.id')
                ->andWhere('pcm.ProjectMasterId = :ProjectMasterId')
                ->andWhere('pcm.DeleteFlag = false')
//                ->andWhere('pc.DeleteFlag = :pcDeleteFlag OR pc.DeleteFlag IS NULL')
                ->setParameter('ProjectMasterId', $project->getId())
//                ->setParameter('pcmDeleteFlag', false)
//                ->setParameter('pcDeleteFlag', false)
            ;
//            $pcmEntities = $queryBuilder->getQuery()->getResult();
            $tbProjectCostMasters = $queryBuilder->getQuery()->getResult();

            //プロジェクト
            $tbProjectMaster->setName($project->getName());
            $tbProjectMaster->setStatus($project->getStatus());
            $tbProjectMaster->setExplanation($project->getExplanation());
            $tbProjectMaster->setPeriodStart($project->getPeriodStart());
            $tbProjectMaster->setPeriodEnd($project->getPeriodEnd());
            $tbProjectMaster->setEstimateFilePath($project->getEstimateFilePath());
            $tbProjectMaster->setScheduleFilePath($project->getScheduleFilePath());
            $tbProjectMaster->setTBCustomerCustomerId($project->getCustomer());
            $tbProjectMaster->setTBSystemUserManagerId($project->getManager());
            $tbProjectMaster->setDeleteFlag(false);
            $this->em->persist($tbProjectMaster);
            $this->em->flush();

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
//            $groupEntities = $queryBuilder->getQuery()->getResult();

            //rootグループ取得
            $tbProjectCostHierarchyMasterRoot = null;
            foreach($tbProjectCostHierarchyMasters as $value)
            {
                /* @var $value TBProjectCostHierarchyMaster */
                if($value->getPath() == '\\'){
                    $tbProjectCostHierarchyMasterRoot = $value;
                    break;
                }
            }

//            //全コスト確認
//            foreach($pcmEntities as $value)
//            {
//                /* @var $value TBProjectCostMaster */
//                $cost = $this->searchNotGroupProjectManagerProjectCost($project->getCosts(), $value->getId());
//                $value->setName($cost->getName());
//                $value->setCost($cost->getCost());
//                $value->setSortNo($cost->getSortNo());
//                $value->setTBProjectMasterProjectMasterId($tbProjectMaster);
//                $value->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($cost->getName());
//            }

            //ソート
//            foreach($project->getCosts() as $value)
//            {
//                $this->sortAllCosts($value);
//            }

            //ソート
            $this->sortAllCostsForRoot($project->getCosts());

            //子コスト編集
            $editTBbProjectCostMasters = array();
            $editTBProjectCostHierarchyMasters = array();
            $this->editCosts($project->getCosts(), $tbProjectMaster, $tbProjectCostMasters, $tbProjectCostHierarchyMasters, $tbProjectCostHierarchyMasterRoot,
                                $editTBbProjectCostMasters, $editTBProjectCostHierarchyMasters);

            //TBbProjectCostMaster削除チェックし削除リストを作成
            $deleteTBProjectCostMasters = array();
            foreach($tbProjectCostMasters as $value)
            {
                $existFlag = false;
                /** @var $value TBProjectCostMaster */
                foreach($editTBbProjectCostMasters as $valueDelete)
                {
                    /** @var $valueDelete TBProjectCostMaster */
                    if($value->getId() == $valueDelete->getId()){
                        $existFlag = true;
                    }
                }
                //
                if(!$existFlag){
                    $deleteTBProjectCostMasters[] = $value;
                }

            }
            //削除対象が１つでもある場合
            if(count($deleteTBProjectCostMasters) != 0)
            {
                $ids = array();
                foreach($deleteTBProjectCostMasters as $value)
                {
                    /** @var $value TBProjectCostMaster */
                    $ids[] = $value->getId();
                }

                //工数詳細取得
                /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
                $queryBuilder = $this->em
                    ->getRepository('ArtePCMSBizlogicBundle:TBProductionCost')
                    ->createQueryBuilder('pc')
                    ->select(array(
                        'pc.id',
                        'coalesce(count(pc.id), 0) as idcount',
                    ))
                    ->groupBy('pc.ProjectCostMasterId')
                    ->andWhere('pc.ProjectCostMasterId in (:ids)')
                    ->andWhere('pc.DeleteFlag = false')
                    ->setParameter('ids', $ids)
                ;
                $tbProductionCosts = $queryBuilder->getQuery()->getResult();

                //１つでも工数が登録されてある場合は失敗
                if(count($tbProductionCosts) != 0){
                    throw new ExistProductionCostException();
                }

                //削除
                foreach($deleteTBProjectCostMasters as $value)
                {
                    /** @var $value TBProjectCostMaster */
                    $value->setDeleteFlag(true);
                    $this->em->persist($value);
                    $this->em->flush();
                }
            }

            //TBProjectCostHierarchyMaster削除チェック
            foreach($tbProjectCostHierarchyMasters as $value)
            {
                if($value->getPath() == '\\'){
                    continue;
                }

                $existFlag = false;
                /** @var $value TBProjectCostHierarchyMaster */
                foreach($editTBProjectCostHierarchyMasters as $valueDelete)
                {
                    /** @var $valueDelete TBProjectCostHierarchyMaster */
                    if($value->getId() == $valueDelete->getId()){
                        $existFlag = true;
                    }
                }

                //存在しない場合は削除
                if(!$existFlag){
                    $value->setDeleteFlag(true);
                    $this->em->persist($value);
                    $this->em->flush();
                }
            }


//            //rootコスト登録
//            $tbProjectCostHierarchyMaster = new TBProjectCostHierarchyMaster();
//            $tbProjectCostHierarchyMaster->setName("root");
//            $tbProjectCostHierarchyMaster->setPath('\\');
//            $tbProjectCostHierarchyMaster->setSortNo(0);
//            $tbProjectCostHierarchyMaster->setTBProjectMasterTBProjectMasterId($tbProjectMaster);
//            $this->em->persist($tbProjectCostHierarchyMaster);
//            $this->em->flush();

//            //子コスト登録
//            $this->createNewCosts($project->getCosts(), $tbProjectMaster, $tbProjectCostHierarchyMaster);

            $this->em->getConnection()->commit();
//            $this->em->getConnection()->rollBack();

        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
        }


    }

    /**
     * プロジェクトを削除する
     * @param ProjectManagerProject $project
     * @throws \Exception
     */
    public function deleteProject(ProjectManagerProject $project)
    {
        $project->getCosts()->clear();

        $this->em->getConnection()->beginTransaction();
        try {

            //プロジェクト情報取得
            /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
            $queryBuilder =$this->em
                ->getRepository('ArtePCMSBizlogicBundle:TBProjectMaster')
                ->createQueryBuilder('p')
                ->leftJoin('p.TBCustomerCustomerId', 'c')
                ->leftJoin('p.TBSystemUserManagerId', 'u')
                ->select(array(
                    'p',
                    'c',
                    'u',
                ))
                ->andWhere('p.id = :id')
                ->andWhere('p.DeleteFlag = false')
                ->setParameter('id', $project->getId())
            ;
            /** @var $entity TBProjectMaster */
            $tbProjectMaster = $queryBuilder->getQuery()->getSingleResult();

            //工数詳細取得
            /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
            $queryBuilder = $this->em
                ->getRepository('ArtePCMSBizlogicBundle:TBProjectCostMaster')
                ->createQueryBuilder('pcm')
                ->innerJoin('pcm.TBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId', 'pchm')
                ->select(array(
                    'pcm',
                    'pchm',
                ))
                ->andWhere('pcm.ProjectMasterId = :ProjectMasterId')
                ->andWhere('pcm.DeleteFlag = false')
                ->setParameter('ProjectMasterId', $project->getId())
            ;
            $tbProjectCostMasters = $queryBuilder->getQuery()->getResult();

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

            //rootグループ取得
            $tbProjectCostHierarchyMasterRoot = null;
            foreach($tbProjectCostHierarchyMasters as $value)
            {
                /* @var $value TBProjectCostHierarchyMaster */
                if($value->getPath() == '\\'){
                    $tbProjectCostHierarchyMasterRoot = $value;
                    break;
                }
            }

//            //ソート
//            $this->sortAllCostsForRoot($project->getCosts());
//
//            //子コスト編集
//            $editTBbProjectCostMasters = array();
//            $editTBProjectCostHierarchyMasters = array();
//            $this->editCosts($project->getCosts(), $tbProjectMaster, $tbProjectCostMasters, $tbProjectCostHierarchyMasters, $tbProjectCostHierarchyMasterRoot,
//                $editTBbProjectCostMasters, $editTBProjectCostHierarchyMasters);

//            //TBbProjectCostMaster削除チェックし削除リストを作成
//            $deleteTBProjectCostMasters = array();
//            foreach($tbProjectCostMasters as $value)
//            {
//                $existFlag = false;
//                /** @var $value TBProjectCostMaster */
//                foreach($editTBbProjectCostMasters as $valueDelete)
//                {
//                    /** @var $valueDelete TBProjectCostMaster */
//                    if($value->getId() == $valueDelete->getId()){
//                        $existFlag = true;
//                    }
//                }
//                //
//                if(!$existFlag){
//                    $deleteTBProjectCostMasters[] = $value;
//                }
//
//            }
            //削除対象が１つでもある場合
            if(count($tbProjectCostMasters) != 0)
            {
                $ids = array();
                foreach($tbProjectCostMasters as $value)
                {
                    /** @var $value TBProjectCostMaster */
                    $ids[] = $value->getId();
                }

                //工数詳細取得
                /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
                $queryBuilder = $this->em
                    ->getRepository('ArtePCMSBizlogicBundle:TBProductionCost')
                    ->createQueryBuilder('pc')
                    ->select(array(
                        'pc.id',
                        'coalesce(count(pc.id), 0) as idcount',
                    ))
                    ->groupBy('pc.ProjectCostMasterId')
                    ->andWhere('pc.ProjectCostMasterId in (:ids)')
                    ->andWhere('pc.DeleteFlag = false')
                    ->setParameter('ids', $ids)
                ;
                $tbProductionCosts = $queryBuilder->getQuery()->getResult();

                //１つでも工数が登録されてある場合は失敗
                if(count($tbProductionCosts) != 0){
                    throw new ExistProductionCostException();
                }

                //削除
                foreach($tbProjectCostMasters as $value)
                {
                    /** @var $value TBProjectCostMaster */
                    $value->setDeleteFlag(true);
                    $this->em->persist($value);
                    $this->em->flush();
                }
            }

            //TBProjectCostHierarchyMaster削除チェック
            foreach($tbProjectCostHierarchyMasters as $value)
            {
                $value->setDeleteFlag(true);
                $this->em->persist($value);
                $this->em->flush();
            }

            //プロジェクト
            $tbProjectMaster->setDeleteFlag(true);
            $this->em->persist($tbProjectMaster);
            $this->em->flush();

            $this->em->getConnection()->commit();
//            $this->em->getConnection()->rollBack();

        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
        }
    }

    /**
     * @param ArrayCollection $costs ProjectManagerProjectCostのリスト
     * @param TBProjectMaster $tbProject 元のプロジェクト情報が格納されてあるTBProjectMaster
     * @param $tbProjectCostMasters TBProjectCostMasterのリスト
     * @param $tbProjectCostHierarchyMasters TBProjectCostHierarchyMasterのリスト
     * @param TBProjectCostHierarchyMaster $tbProjectHierarchy 所属しているグループのTBProjectCostHierarchyMaster
     * @param $editTBbProjectCostMasters 編集済みのTBProjectCostMasterのリスト
     * @param $editTBProjectCostHierarchyMasters 編集済みのTBProjectCostHierarchyMasterのリスト
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    private function editCosts(ArrayCollection $costs, TBProjectMaster &$tbProject, $tbProjectCostMasters, $tbProjectCostHierarchyMasters, TBProjectCostHierarchyMaster &$tbProjectHierarchy,
                               &$editTBbProjectCostMasters, &$editTBProjectCostHierarchyMasters)
    {
        $sortNo = 1;
        foreach($costs as $value)
        {
            /** @var $value ProjectManagerProjectCost */
            if($value->getGroupFlag()){

                $group = new TBProjectCostHierarchyMaster();
                if($value->getId() != ""){
                    $group = $this->searchTBProjectCostHierarchyMasterFromId($tbProjectCostHierarchyMasters, $value->getId());
                    if($group == null){
                        throw new EntityNotFoundException();
                    }
                    $editTBProjectCostHierarchyMasters[] = $group;
                }

//                $group = new TBProjectCostHierarchyMaster();
                $group->setName($value->getName());
                $group->setPath($tbProjectHierarchy->getPath().$tbProjectHierarchy->getId().'\\');
//                $group->setSortNo($value->getSortNo());
                $group->setSortNo($sortNo);
                $group->setDeleteFlag(false);
                $group->setTBProjectMasterTBProjectMasterId($tbProject);

                $this->em->persist($group);
                $this->em->flush();

                $this->editCosts($value->getChildCosts(), $tbProject, $tbProjectCostMasters, $tbProjectCostHierarchyMasters, $group, $editTBbProjectCostMasters, $editTBProjectCostHierarchyMasters);

            }else{

                //UPDATEかINSERTか判別
                $costEntity = new TBProjectCostMaster();
                if($value->getId() != ""){
                    $costEntity = $this->searchTBProjectCostMasterFromId($tbProjectCostMasters, $value->getId());
                    if($costEntity == null){
                        throw new EntityNotFoundException();
                    }
                    $editTBbProjectCostMasters[] = $costEntity;
                }

//                $costEntity = new TBProjectCostMaster();
                $costEntity->setName($value->getName());
                $costEntity->setCost($value->getCost());
//                $costEntity->setSortNo($value->getSortNo());
                $costEntity->setSortNo($sortNo);
                $costEntity->setDeleteFlag(false);
                $costEntity->setTBProjectMasterProjectMasterId($tbProject);
                $costEntity->setTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId($tbProjectHierarchy);

                $this->em->persist($costEntity);
                $this->em->flush();

            }
            $sortNo++;
        }
    }

    /**
     * TBProjectCostMasterをIDから検索
     * @param $tbProjectCostMasters
     * @param $id
     * @return TBProjectCostMaster|null
     */
    protected function searchTBProjectCostMasterFromId($tbProjectCostMasters, $id)
    {
        foreach($tbProjectCostMasters as $value)
        {
            /** @var $value TBProjectCostMaster */
            if($value->getId() == $id){
                return $value;
            }
        }
        return null;
    }

    /**
     * TBProjectCostHierarchyMasterをIDから検索
     * @param $tbProjectCostHierarchyMasters
     * @param $id
     * @return TBProjectCostHierarchyMaster|null
     */
    protected function searchTBProjectCostHierarchyMasterFromId($tbProjectCostHierarchyMasters, $id)
    {
        foreach($tbProjectCostHierarchyMasters as $value)
        {
            /** @var $value TBProjectCostHierarchyMaster */
            if($value->getId() == $id){
                return $value;
            }
        }
        return null;
    }

    /**
     * プロジェクト取得
     * @param $projectId
     * @return ProjectManagerProject
     */
    public function readProject($projectId)
    {
        //プロジェクト情報取得
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder =$this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBProjectMaster')
            ->createQueryBuilder('p')
            ->leftJoin('p.TBCustomerCustomerId', 'c')
            ->leftJoin('p.TBSystemUserManagerId', 'u')
//            ->select(array(
//                'p',
//                'partial c.{id, Name}',
//                'partial u.{id, DisplayName}',
//            ))
            ->select(array(
                'p',
                'c',
                'u',
            ))
            ->andWhere('p.id = :id')
            ->andWhere('p.DeleteFlag = :DeleteFlag')
            ->setParameter('id', $projectId)
            ->setParameter('DeleteFlag', false)
        ;
        /** @var $entity TBProjectMaster */
        $entity = $queryBuilder->getQuery()->getSingleResult();


        //実工数取得
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:VProjectView')
            ->createQueryBuilder('vp')
//            ->select(array(
//                'partial vp.{id, ProjectTotalCost, ProductionTotalCost}',
//            ))
            ->select(array(
                'vp',
            ))
            ->andWhere('vp.id = :id')
            ->setParameter('id', $projectId)
        ;
        /** @var $vprojectEntity VProjectView */
        $vprojectEntity = $queryBuilder->getQuery()->getSingleResult();

        $project = new ProjectManagerProject();
        $project->setId($entity->getId());
        $project->setName($entity->getName());
        $project->setStatus($entity->getStatus());
        $project->setExplanation($entity->getExplanation());
        $project->setPeriodStart($entity->getPeriodStart());
        $project->setPeriodEnd($entity->getPeriodEnd());
        $project->setEstimateFilePath($entity->getEstimateFilePath());
        $project->setScheduleFilePath($entity->getScheduleFilePath());
        $project->setProjectTotalCost($vprojectEntity->getProjectTotalCost());
        $project->setProductionTotalCost($vprojectEntity->getProductionTotalCost());
        $project->setCustomer($entity->getTBCustomerCustomerId());
        $project->setManager($entity->getTBSystemUserManagerId());



        //工数詳細取得
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBProjectCostMaster')
            ->createQueryBuilder('pcm')
            ->innerJoin('pcm.TBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId', 'pchm')
            ->leftJoin('pcm.TBProductionCostsProjectCostMasterId', 'pc')
//            ->select(array(
//                'partial pcm.{id, Name, Cost}',
//                'sum(pc.Cost)',
//            ))
            ->select(array(
                'coalesce(sum(pc.Cost), 0) as SumCost',
                'pcm as TBProjectCostMaster',
                'pchm'
            ))
            ->groupBy('pcm.id')
            ->andWhere('pcm.ProjectMasterId = :ProjectMasterId')
            ->andWhere('pcm.DeleteFlag = :pcmDeleteFlag')
            ->andWhere('pchm.DeleteFlag = false')
            ->andWhere('pc.DeleteFlag = :pcDeleteFlag OR pc.DeleteFlag IS NULL')
            ->setParameter('ProjectMasterId', $projectId)
            ->setParameter('pcmDeleteFlag', false)
            ->setParameter('pcDeleteFlag', false)
        ;
        $pcmEntities = $queryBuilder->getQuery()->getResult();

        //グループ取得
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBProjectCostHierarchyMaster')
            ->createQueryBuilder('pchm')
            ->select(array(
                'pchm',
            ))
            ->andWhere('pchm.DeleteFlag = false')
            ->andWhere('pchm.TBProjectMasterId = :TBProjectMasterId')
            ->setParameter('TBProjectMasterId', $projectId)
        ;
        $groupEntities = $queryBuilder->getQuery()->getResult();
        $this->sortGroupEntities($groupEntities);

        //グループとコストのペアを作成
        $groupCostPairs = array();
        foreach($groupEntities as $groupValue)
        {
            /* @var $groupValue TBProjectCostHierarchyMaster */

            //グループ作成
            $groupCost = new ProjectManagerProjectCost();
            $groupCost->setId($groupValue->getId());//TBProjectCostHierarchyMasterのID
            $groupCost->setName($groupValue->getName());
            $groupCost->setSortNo($groupValue->getSortNo());
            $groupCost->setGroupFlag(true);
            $costSum = 0;
            $nowCostSum = 0;

            foreach($pcmEntities as $costValue)
            {
                /* @var $costValue TBProjectCostMaster */
                /* @var $tbProjectCostMaster TBProjectCostMaster */
                $tbProjectCostMaster = $costValue['TBProjectCostMaster'];
                $sumCost = $costValue['SumCost'];
                if($tbProjectCostMaster->getTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId()->getId() == $groupValue->getId())
                {
                    //リーフ作成
                    $liefCost = new ProjectManagerProjectCost();
                    $liefCost->setId($tbProjectCostMaster->getId());//TBProjectCostMasterのID
                    $liefCost->setName($tbProjectCostMaster->getName());
                    $liefCost->setCost($tbProjectCostMaster->getCost());
                    $liefCost->setNowCost($sumCost);
                    $liefCost->setSortNo($tbProjectCostMaster->getSortNo());
                    $liefCost->setGroupFlag(false);

                    $costSum += $tbProjectCostMaster->getCost();
                    $nowCostSum += $sumCost;

                    $groupCost->getChildCosts()->add($liefCost);
                }

                $groupCost->setCost($costSum);
                $groupCost->setNowCost($nowCostSum);

            }
            $groupCostPairs[] = $groupCost;

        }

        //グループとグループのペアを作成
        $groupGroupPairs = array();
        foreach($groupEntities as $groupValue)
        {
            /* @var $groupValue TBProjectCostHierarchyMaster */

            $childPath = $groupValue->getPath().$groupValue->getId().'\\';
            $childPath = str_replace('\\', '\\\\', $childPath);

            $pairs = array();
            foreach($groupEntities as $groupValue2)
            {
                /* @var $groupValue2 TBProjectCostHierarchyMaster */
                if(preg_match('/'.$childPath.'$/', $groupValue2->getPath()))
                {
                    $pairs[] = $groupValue2->getId();
                }
            }

            if(count($pairs) != 0)
            {
                $groupGroupPairs[$groupValue->getId()] = $pairs;
            }

        }

        //グループ同士を連結
        foreach($groupGroupPairs as $key => $values)
        {
            /* @var $parent ProjectManagerProjectCost */
            $parent = $this->searchGroupProjectManagerProjectCost($groupCostPairs, $key);
            $costSum = $parent->getCost();
            $nowCostSum = $parent->getNowCost();
            foreach($values as $value)
            {
                /* @var $child ProjectManagerProjectCost */
                $child = $this->searchGroupProjectManagerProjectCost($groupCostPairs, $value);

                $costSum += $child->getCost();
                $nowCostSum += $child->getNowCost();

                $parent->getChildCosts()->add($child);
            }
            $parent->setCost($costSum);
            $parent->setNowCost($nowCostSum);
        }

        //rootグループ取得
        $rootGroupCost = null;
        foreach($groupCostPairs as $value)
        {
            if($value->getName() == '\\'){
                $rootGroupCost = $value;
            }
        }

        //ソート
        $this->sortAllCosts($rootGroupCost);

        //プロジェクトにコストをすべて登録
        foreach($rootGroupCost->getChildCosts() as $value)
        {
            $project->addCosts($value);
        }

        return $project;
    }

    /**
     * ProjectManagerProjectCostの配列から指定したIDのグループを取得する
     * @param $costs
     * @param $id
     * @return ProjectManagerProjectCost|null
     */
    protected function searchGroupProjectManagerProjectCost($costs, $id)
    {
        foreach($costs as $value)
        {
            /* @var $value ProjectManagerProjectCost */
            if($value->getGroupFlag() == true && $value->getId() == $id)
            {
                return $value;
            }
        }
        return null;
    }

    /**
     * ProjectManagerProjectCostの配列からグループではない指定したIDのコストを取得する
     * @param $costs
     * @param $id
     * @return ProjectManagerProjectCost|null
     */
    protected function searchNotGroupProjectManagerProjectCost($costs, $id)
    {
        foreach($costs as $value)
        {
            /* @var $value ProjectManagerProjectCost */
            if($value->getGroupFlag() == false && $value->getId() == $id)
            {
                return $value;
            }
        }
        return null;
    }

    /**
     * バブルソートをおこなう
     * @param $groupEntities
     */
    protected function sortGroupEntities(&$groupEntities)
    {
        $groupEntityCount = count($groupEntities);
        for($i=0; $i<$groupEntityCount; $i++)
        {
            for($k=1; $k<$groupEntityCount-$i; $k++)
            {
//                $a = substr_count($groupEntities[$k]->getPath(), '\\');
//                $b = substr_count($groupEntities[$k-1]->getPath(), '\\');
                if(substr_count($groupEntities[$k]->getPath(), '\\') > substr_count($groupEntities[$k-1]->getPath(), '\\')){
                    $temp = $groupEntities[$k-1];
                    $groupEntities[$k-1] = $groupEntities[$k];
                    $groupEntities[$k] = $temp;
                }
            }
        }
    }

    /**
     * プロジェクト直下のコスト一覧からソートを開始する
     * @param $projectCost
     */
    protected function sortAllCostsForRoot($projectCost)
    {
        //ソート処理
        $childCostArray = $projectCost->toArray();
        $childCostArrayCount = count($childCostArray);
        for($i=0; $i<$childCostArrayCount; $i++)
        {
            for($k=1; $k<$childCostArrayCount-$i; $k++)
            {
                if($childCostArray[$k]->getSortNo() < $childCostArray[$k-1]->getSortNo()){
                    $temp = $childCostArray[$k-1];
                    $childCostArray[$k-1] = $childCostArray[$k];
                    $childCostArray[$k] = $temp;
                }
            }
        }
        $projectCost->clear();
        foreach($childCostArray as $value)
        {
            $projectCost->add($value);
        }

        //子コストがある場合は中もソートする
        foreach($projectCost as $value)
        {
            /* @var $value ProjectManagerProjectCost */
            if($value->getGroupFlag())
            {
                $this->sortAllCosts($value);
            }
        }
    }

    /**
     * 子コストを全てソートする
     * @param ProjectManagerProjectCost $projectCost
     */
    protected function sortAllCosts(ProjectManagerProjectCost $projectCost)
    {

        //ソート処理
        $childCostArray = $projectCost->getChildCosts()->toArray();
        $childCostArrayCount = count($childCostArray);
        for($i=0; $i<$childCostArrayCount; $i++)
        {
            for($k=1; $k<$childCostArrayCount-$i; $k++)
            {
                if($childCostArray[$k]->getSortNo() < $childCostArray[$k-1]->getSortNo()){
                    $temp = $childCostArray[$k-1];
                    $childCostArray[$k-1] = $childCostArray[$k];
                    $childCostArray[$k] = $temp;
                }
            }
        }
        $projectCost->getChildCosts()->clear();
        foreach($childCostArray as $value)
        {
            $projectCost->getChildCosts()->add($value);
        }

        //子コストがある場合は中もソートする
        foreach($projectCost->getChildCosts() as $value)
        {
            /* @var $value ProjectManagerProjectCost */
            if($value->getGroupFlag())
            {
                $this->sortAllCosts($value);
            }
        }
    }

    protected function sortChildCosts($groupData)
    {
        foreach($groupData as $key => $value)
        {
            /* @var $value ProjectManagerProjectCost */
//            $parent = $value;
            $childPath = $key . $value->getId() . '\\';
            if(isset($groupData[$childPath]))
            {
                $children = $groupData[$childPath];
                $value->getChildCosts()->add($children);
            }
        }

        return $groupData["\\"];
    }

//    protected function sortChildCosts($groupMaster, $groupData, $path)
//    {
//        foreach($groupMaster as $value)
//        {
//            /* @var $value TBProjectCostHierarchyMaster */
//            if($value->getPath() === $path){
//
//                /* @var $parent ProjectManagerProjectCost */
//                $parent = $groupData[$path];
//                $childPath = $path . $value->getId();
//                $children = $groupData[$childPath];
//                $parent->getChildCosts()->add($children);
//
//            }
//        }
//    }

    protected function getCostsFromPath($allCosts, $path)
    {
        $ret = array();
        foreach($allCosts as $value)
        {
            /* @var $tbProjectCostMaster TBProjectCostMaster */
            $tbProjectCostMaster = $value['TBProjectCostMaster'];
            $sumCost = $value['SumCost'];
            if($tbProjectCostMaster->getTBProjectCostHierarchyMasterTBProjectCostHierarchyMasterId()->getPath() === $path)
            {
                $projectCost = new ProjectManagerProjectCost();
                $projectCost->setId($tbProjectCostMaster->getId());
                $projectCost->setName($tbProjectCostMaster->getName());
                $projectCost->setCost($tbProjectCostMaster->getCost());
                $projectCost->setSortNo($tbProjectCostMaster->getSortNo());
                $projectCost->setNowCost($sumCost);
                $projectCost->setGroupFlag(false);

                $ret[] = $projectCost;
            }
        }
        return $ret;
    }

    /**
     * コスト登録
     * @param ProjectManagerProductionCost $cost
     * @return TBProductionCost
     * @throws \Exception
     */
    public function createProductionCost(ProjectManagerProductionCost $cost)
    {
        $this->em->getConnection()->beginTransaction();
        try {

            //工数詳細取得
            /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
            $queryBuilder = $this->em
                ->getRepository('ArtePCMSBizlogicBundle:TBProjectCostMaster')
                ->createQueryBuilder('pcm')
                ->select(array(
                    'pcm',
                ))
                ->andWhere('pcm.id = :id')
                ->andWhere('pcm.DeleteFlag = false')
                ->setParameter('id', $cost->getProjectCost()->getId())
            ;
            $tbProjectCostMasters = $queryBuilder->getQuery()->getSingleResult();

            //
            $tbProductionCost = new TBProductionCost();
            $tbProductionCost->setTBSystemUserSystemUserId($cost->getWorker());
            $tbProductionCost->setTBProjectCostMasterProjectCostMasterId($tbProjectCostMasters);
            $tbProductionCost->setWorkDate($cost->getWorkDate());
            $tbProductionCost->setCost($cost->getCost());
            $tbProductionCost->setNote($cost->getNote());
            $tbProductionCost->setDeleteFlag(false);

            $this->em->persist($tbProductionCost);
            $this->em->flush();


            $this->em->getConnection()->commit();
        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
        }

        return $tbProductionCost;
    }

    /**
     * コスト一覧取得
     * @param $id
     * @return array
     */
    public function getProductionCosts($id)
    {
        //工数詳細取得
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBProjectCostMaster')
            ->createQueryBuilder('pcm')
            ->leftJoin('pcm.TBProductionCostsProjectCostMasterId', 'pc')
            ->leftJoin('pc.TBSystemUserSystemUserId', 'u')
            ->select(array(
                'pcm',
                'pc',
                'u',
            ))
            ->andWhere('pcm.id = :id')
            ->andWhere('pcm.DeleteFlag = false')
            ->andWhere('pc.DeleteFlag = false OR pc.DeleteFlag IS NULL')
            ->andWhere('u.DeleteFlag = false OR u.DeleteFlag IS NULL')
            ->setParameter('id', $id)
        ;
        /* @var $tbProjectCostMaster TBProjectCostMaster */
        $tbProjectCostMaster = $queryBuilder->getQuery()->getSingleResult();

        $projectCost = new ProjectManagerProjectCost();
        $projectCost->setId($tbProjectCostMaster->getId());
        $projectCost->setName($tbProjectCostMaster->getName());
        $projectCost->setCost($tbProjectCostMaster->getCost());
        $projectCost->setSortNo($tbProjectCostMaster->getSortNo());
        $projectCost->setGroupFlag(false);

        $projectCosts = array();
        foreach($tbProjectCostMaster->getTBProductionCostsProjectCostMasterId() as $value)
        {
            /** @var $value TBProductionCost */

            $productionCost = new ProjectManagerProductionCost();
            $productionCost->setId($value->getId());
            $productionCost->setProjectCost($projectCost);
            $productionCost->setWorker($value->getTBSystemUserSystemUserId());
            $productionCost->setCost($value->getCost());
            $productionCost->setWorkDate($value->getWorkDate());
            $productionCost->setNote($value->getNote());

            $projectCosts[] = $productionCost;
        }

        return $projectCosts;
    }

    /**
     * コスト取得
     * @param $id
     * @return ProjectManagerProductionCost
     */
    public function getProductionCost($id)
    {
        //工数詳細取得
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBProductionCost')
            ->createQueryBuilder('pc')
            ->leftJoin('pc.TBSystemUserSystemUserId', 'u')
            ->leftJoin('pc.TBProjectCostMasterProjectCostMasterId', 'pcm')
            ->select(array(
                'pc',
                'u',
                'pcm',
            ))
            ->andWhere('pc.id = :id')
            ->andWhere('pc.DeleteFlag = false')
            ->andWhere('u.DeleteFlag = false')
            ->setParameter('id', $id)
        ;
        /* @var $tbProductionCost TBProductionCost */
        $tbProductionCost = $queryBuilder->getQuery()->getSingleResult();

        $projectCost = new ProjectManagerProjectCost();
        $projectCost->setId($tbProductionCost->getTBProjectCostMasterProjectCostMasterId()->getId());
        $projectCost->setName($tbProductionCost->getTBProjectCostMasterProjectCostMasterId()->getName());
        $projectCost->setCost($tbProductionCost->getTBProjectCostMasterProjectCostMasterId()->getCost());
        $projectCost->setSortNo($tbProductionCost->getTBProjectCostMasterProjectCostMasterId()->getSortNo());
        $projectCost->setGroupFlag(false);

        $productionCost = new ProjectManagerProductionCost();
        $productionCost->setId($tbProductionCost->getId());
        $productionCost->setProjectCost($projectCost);
        $productionCost->setWorker($tbProductionCost->getTBSystemUserSystemUserId());
        $productionCost->setCost($tbProductionCost->getCost());
        $productionCost->setWorkDate($tbProductionCost->getWorkDate());
        $productionCost->setNote($tbProductionCost->getNote());

        return $productionCost;

    }

    /**
     * コスト変更
     * @param ProjectManagerProductionCost $cost
     * @return mixed
     * @throws \Exception
     */
    public function editProductionCost(ProjectManagerProductionCost $cost)
    {
        $this->em->getConnection()->beginTransaction();
        try {

            //工数詳細取得
            /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
            $queryBuilder = $this->em
                ->getRepository('ArtePCMSBizlogicBundle:TBProjectCostMaster')
                ->createQueryBuilder('pcm')
                ->select(array(
                    'pcm',
                ))
                ->andWhere('pcm.id = :id')
                ->andWhere('pcm.DeleteFlag = false')
                ->setParameter('id', $cost->getProjectCost()->getId())
            ;
            $tbProjectCostMasters = $queryBuilder->getQuery()->getSingleResult();

            //工数詳細取得
            /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
            $queryBuilder = $this->em
                ->getRepository('ArtePCMSBizlogicBundle:TBProductionCost')
                ->createQueryBuilder('pc')
                ->select(array(
                    'pc',
                ))
                ->andWhere('pc.id = :id')
                ->andWhere('pc.DeleteFlag = false')
                ->setParameter('id', $cost->getId())
            ;
            $tbProductionCost = $queryBuilder->getQuery()->getSingleResult();

            //
            $tbProductionCost->setTBSystemUserSystemUserId($cost->getWorker());
            $tbProductionCost->setTBProjectCostMasterProjectCostMasterId($tbProjectCostMasters);
            $tbProductionCost->setWorkDate($cost->getWorkDate());
            $tbProductionCost->setCost($cost->getCost());
            $tbProductionCost->setNote($cost->getNote());
            $tbProductionCost->setDeleteFlag(false);

            $this->em->persist($tbProductionCost);
            $this->em->flush();


            $this->em->getConnection()->commit();
        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
        }

        return $tbProductionCost;
    }

    /**
     * コスト削除
     * @param $id
     * @throws \Exception
     */
    public function deleteProductionCost($id)
    {
        $this->em->getConnection()->beginTransaction();
        try {

            //工数詳細取得
            /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
            $queryBuilder = $this->em
                ->getRepository('ArtePCMSBizlogicBundle:TBProductionCost')
                ->createQueryBuilder('pc')
                ->select(array(
                    'pc',
                ))
                ->andWhere('pc.id = :id')
                ->andWhere('pc.DeleteFlag = false')
                ->setParameter('id', $id)
            ;
            $tbProductionCost = $queryBuilder->getQuery()->getSingleResult();

            //
            $tbProductionCost->setDeleteFlag(true);

            $this->em->persist($tbProductionCost);
            $this->em->flush();


            $this->em->getConnection()->commit();
        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
        }
    }
}

class ProjectManagerProject {
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string　案件名
     */
    private $Name;

    /**
     * @var integer　状態
     */
    private $Status;

    /**
     * @var string　説明
     */
    private $Explanation;

    /**
     * @var \DateTime　開始日
     */
    private $PeriodStart;

    /**
     * @var \DateTime　終了日
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
     */
    private $Customer;

    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser　管理者
     */
    private $Manager;

    /**
     * @var integer
     */
    private $projectTotalCost;

    /**
     * @var integer
     */
    private $productionTotalCost;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection　コスト
     */
    private $costs;

    function __construct()
    {
        $this->costs = new ArrayCollection();
    }

    /**
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBCustomer $Customer
     */
    public function setCustomer(TBCustomer $Customer)
    {
        $this->Customer = $Customer;
    }

    /**
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBCustomer
     */
    public function getCustomer()
    {
        return $this->Customer;
    }

    /**
     * @param string $EstimateFilePath
     */
    public function setEstimateFilePath($EstimateFilePath)
    {
        $this->EstimateFilePath = $EstimateFilePath;
    }

    /**
     * @return string
     */
    public function getEstimateFilePath()
    {
        return $this->EstimateFilePath;
    }

    /**
     * @param string $Explanation
     */
    public function setExplanation($Explanation)
    {
        $this->Explanation = $Explanation;
    }

    /**
     * @return string
     */
    public function getExplanation()
    {
        return $this->Explanation;
    }

    /**
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $Manager
     */
    public function setManager(TBSystemUser $Manager)
    {
        $this->Manager = $Manager;
    }

    /**
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser
     */
    public function getManager()
    {
        return $this->Manager;
    }

    /**
     * @param string $Name
     */
    public function setName($Name)
    {
        $this->Name = $Name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

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
     * @param string $ScheduleFilePath
     */
    public function setScheduleFilePath($ScheduleFilePath)
    {
        $this->ScheduleFilePath = $ScheduleFilePath;
    }

    /**
     * @return string
     */
    public function getScheduleFilePath()
    {
        return $this->ScheduleFilePath;
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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

//    /**
//     * @param \Doctrine\Common\Collections\ArrayCollection $costs
//     */
//    public function setCosts($costs)
//    {
//        $this->costs = $costs;
//    }


    /**
     * @param ProjectManagerProjectCost $cost
     */
    public function addCosts(ProjectManagerProjectCost $cost)
    {
        $this->costs->add($cost);
    }

    /**
     * @param ProjectManagerProjectCost $cost
     */
    public function removeCosts(ProjectManagerProjectCost $cost)
    {
        $this->costs->removeElement($cost);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCosts()
    {
        return $this->costs;
    }

    /**
     * @param int $productionTotalCost
     */
    public function setProductionTotalCost($productionTotalCost)
    {
        $this->productionTotalCost = $productionTotalCost;
    }

    /**
     * @return int
     */
    public function getProductionTotalCost()
    {
        return $this->productionTotalCost;
    }

    /**
     * @param int $projectTotalCost
     */
    public function setProjectTotalCost($projectTotalCost)
    {
        $this->projectTotalCost = $projectTotalCost;
    }

    /**
     * @return int
     */
    public function getProjectTotalCost()
    {
        return $this->projectTotalCost;
    }

}

class ProjectManagerProjectCost {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string　名称
     */
    private $Name;

    /**
     * @var integer　コスト
     */
    private $Cost;

    /**
     * @var integer　現在のコスト
     */
    private $nowCost;

    /**
     * @var integer　ソート番号
     */
    private $SortNo;

    /**
     * @var bool グループフラグ
     */
    private $GroupFlag;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection 子コスト
     */
    private $childCosts;

    function __construct()
    {
        $this->childCosts = new ArrayCollection();
    }

    /**
     * @param int $Cost
     */
    public function setCost($Cost)
    {
        $this->Cost = $Cost;
    }

    /**
     * @return int
     */
    public function getCost()
    {
        return $this->Cost;
    }

    /**
     * @param boolean $GroupFlag
     */
    public function setGroupFlag($GroupFlag)
    {
        $this->GroupFlag = $GroupFlag;
    }

    /**
     * @return boolean
     */
    public function getGroupFlag()
    {
        return $this->GroupFlag;
    }

    /**
     * @param string $Name
     */
    public function setName($Name)
    {
        $this->Name = $Name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

//    /**
//     * @param \Doctrine\Common\Collections\ArrayCollection $childCosts
//     */
//    public function setChildCosts($childCosts)
//    {
//        $this->childCosts = $childCosts;
//    }

    /**
     * @param ProjectManagerProjectCost $childCost
     */
    public function addChildCosts(ProjectManagerProjectCost $childCost)
    {
        $this->childCosts->add($childCost);
    }

    /**
     * @param ProjectManagerProjectCost $childCost
     */
    public function removeChildCosts(ProjectManagerProjectCost $childCost)
    {
        $this->childCosts->removeElement($childCost);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildCosts()
    {
        return $this->childCosts;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $SortNo
     */
    public function setSortNo($SortNo)
    {
        $this->SortNo = $SortNo;
    }

    /**
     * @return int
     */
    public function getSortNo()
    {
        return $this->SortNo;
    }

    /**
     * @param int $nowCost
     */
    public function setNowCost($nowCost)
    {
        $this->nowCost = $nowCost;
    }

    /**
     * @return int
     */
    public function getNowCost()
    {
        return $this->nowCost;
    }

}

class ProjectManagerProductionCost {
    private $id;
    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser　作業者
     */
    private $worker;
    /**
     * @var ProjectManagerProjectCost　対象作業
     */
    private $projectCost;

    private $workDate;
    private $cost;
    private $note;

    /**
     * @param mixed $cost
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost $projectCost
     */
    public function setProjectCost($projectCost)
    {
        $this->projectCost = $projectCost;
    }

    /**
     * @return \Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost
     */
    public function getProjectCost()
    {
        return $this->projectCost;
    }

    /**
     * @param mixed $workDate
     */
    public function setWorkDate($workDate)
    {
        $this->workDate = $workDate;
    }

    /**
     * @return mixed
     */
    public function getWorkDate()
    {
        return $this->workDate;
    }

    /**
     * @param \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $worker
     */
    public function setWorker($worker)
    {
        $this->worker = $worker;
    }

    /**
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser
     */
    public function getWorker()
    {
        return $this->worker;
    }


}

class ExistProductionCostException extends \Exception
{
    public function __construct()
    {
        parent::__construct('すでに実工数が登録されています。すでに工数が登録されてある場合は削除できません。実工数を別の工数に付け替えて修正してください。');
    }
}