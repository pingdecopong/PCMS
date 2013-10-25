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
use Arte\PCMS\BizlogicBundle\Entity\TBProjectUser;
use Arte\PCMS\BizlogicBundle\Entity\TBSystemUser;
use Arte\PCMS\BizlogicBundle\Entity\VProjectView;
//use Arte\PCMS\PublicBundle\Form\TBProductionCostSearchModel;
use Arte\PCMS\PublicBundle\Form\TBProductionCostSearchModel2;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\SecurityContext;

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
     * プロジェクト一覧取得
     * @param $pageNo
     * @param $pageSize
     * @param null $sortName
     * @param null $sortType
     * @param TBProjectMasterSearchModel $search
     * @param TBSystemUser $operateUser このユーザーが参加しているプロジェクトを取得する
     * @return array
     */
    public function getProjectList($pageNo, $pageSize, $sortName = null, $sortType = null, TBProjectMasterSearchModel $search = null, TBSystemUser $operateUser = null)
    {
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:VProjectView')
            ->createQueryBuilder('vp')
            ->leftJoin('vp.TBCustomerCustomerId', 'c')
            ->leftJoin('vp.TBSystemUserManagerId', 'u')
            ->select(array(
                'vp',
                'partial c.{id, Name}',
                'partial u.{id, DisplayName}',
            ))
        ;

        //参加ユーザー指定ありの場合
        if($operateUser != null){

            $queryBuilder = $queryBuilder->leftJoin('vp.VProjectUsersProjectMasterId', 'vpu')
                ->andWhere('vpu.SystemUserId = :SystemUserId')
                ->setParameter('SystemUserId', $operateUser->getId());

        }

        //検索
        if($search == null){
            $search = new TBProjectMasterSearchModel();
        }
        //Name
        $searchName = $search->getName();
        if(isset($searchName))
        {
            $queryBuilder = $queryBuilder->andWhere('vp.Name LIKE :Name')
                ->setParameter('Name', '%'.$searchName.'%');
        }
        //Status
        $searchStatus = $search->getStatus();
        if(isset($searchStatus))
        {
            $queryBuilder = $queryBuilder->andWhere('vp.Status = :Status')
                ->setParameter('Status', $searchStatus);
        }
        //PeriodStart
        $searchPeriodstart = $search->getPeriodstart();
        if(isset($searchPeriodstart))
        {
            $queryBuilder = $queryBuilder->andWhere('vp.PeriodEnd >= :Periodstart')
                ->setParameter('Periodstart', $searchPeriodstart);
        }
        //PeriodEnd
        $searchPeriodend = $search->getPeriodend();
        if(isset($searchPeriodend))
        {
            $queryBuilder = $queryBuilder->andWhere('vp.PeriodStart <= :Periodend')
                ->setParameter('Periodend', $searchPeriodend);
        }

        //relation 検索
        //TBCustomerCustomerId
        $searchTbcustomercustomerid = $search->getTbcustomercustomerid();
        if(isset($searchTbcustomercustomerid))
        {
            $queryBuilder = $queryBuilder->andWhere('vp.TBCustomerCustomerId = :Tbcustomercustomerid')
                ->setParameter('Tbcustomercustomerid', $searchTbcustomercustomerid);
        }
        //TBSystemUserManagerId
        $searchTbsystemusermanagerid = $search->getTbsystemusermanagerid();
        if(isset($searchTbsystemusermanagerid))
        {
            $queryBuilder = $queryBuilder->andWhere('vp.TBSystemUserManagerId = :Tbsystemusermanagerid')
                ->setParameter('Tbsystemusermanagerid', $searchTbsystemusermanagerid);
        }

        //全件数取得
        $queryBuilderCount = clone $queryBuilder;
        $queryBuilderCount = $queryBuilderCount->select('count(vp.id)');
        $queryCount = $queryBuilderCount->getQuery();
        $allCount = $queryCount->getSingleScalarResult();

        //ソート
        if($sortName != null && $sortType != null)
        {
            switch($sortName)
            {
                case 'Name'://案件名
                    $queryBuilder = $queryBuilder->orderBy('vp.Name', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('vp.id', 'DESC');
                    break;
                case 'CustomerName'://顧客
                    $queryBuilder = $queryBuilder->orderBy('c.Name', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('vp.id', 'DESC');
                    break;
                case 'PeriodStart'://開始日
                    $queryBuilder = $queryBuilder->orderBy('vp.PeriodStart', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('vp.id', 'DESC');
                    break;
                case 'PeriodEnd'://終了日
                    $queryBuilder = $queryBuilder->orderBy('vp.PeriodEnd', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('vp.id', 'DESC');
                    break;
                case 'ManagerName'://管理者
                    $queryBuilder = $queryBuilder->orderBy('u.DisplayName', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('vp.id', 'DESC');
                    break;
                case 'Status'://状態
                    $queryBuilder = $queryBuilder->orderBy('vp.Status', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('vp.id', 'DESC');
                    break;
                case 'ProjectTotalCost'://見積工数
                    $queryBuilder = $queryBuilder->orderBy('vp.ProjectTotalCost', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('vp.id', 'DESC');
                    break;
                case 'ProductionTotalCost'://実工数
                    $queryBuilder = $queryBuilder->orderBy('vp.ProductionTotalCost', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('vp.id', 'DESC');
                    break;


                default:
//                    $sortColumn = $pager->getColumn($sortName);
//                    $queryBuilder = $queryBuilder->orderBy('vp.'.$sortColumn['db_column_name'], $sortType);
                    break;
            }
        }

        $queryBuilder = $queryBuilder->setFirstResult($pageSize*($pageNo-1))
            ->setMaxResults($pageSize);

        //クエリー実行
        $entities = $queryBuilder->getQuery()->getResult();

        $result = array();
        $result['count'] = $allCount;
        $result['result'] = $entities;

        return $result;
    }

    public function getCostList($pageNo, $pageSize, $sortName = null, $sortType = null, TBProductionCostSearchModel $search = null, TBSystemUser $operateUser = null)
    {
        $this->em->clear();
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBProductionCost')
            ->createQueryBuilder('c')
            ->join('c.TBSystemUserSystemUserId', 'u')
            ->join('c.TBProjectCostMasterProjectCostMasterId', 'pc')
            ->join('pc.TBProjectMasterProjectMasterId', 'p')
            ->andWhere('u.DeleteFlag = false')
            ->andWhere('c.DeleteFlag = false')
            ->andWhere('pc.DeleteFlag = false')
            ->andWhere('p.DeleteFlag = false')
            ->select(array(
                'u',
                'c',
                'pc',
                'p',
//                'partial u.{id, DisplayName}',
//                'partial c.{id, Cost, WorkDate}',
//                'partial pc.{id, Name}',
//                'partial p.{id, Name, Status}',
            ))
        ;

        //ユーザー指定ありの場合
        if($operateUser != null){

            $queryBuilder = $queryBuilder->andWhere('u = :TBSystemUser')
                ->setParameter('TBSystemUser', $operateUser);

        }

        //検索
        if($search == null){
            $search = new TBProductionCostSearchModel();
        }
        //TBProjectMaster
        $searchTBProjectMaster = $search->getTBProjectMaster();
        if(isset($searchTBProjectMaster))
        {
            $queryBuilder = $queryBuilder->andWhere('p = :TBProjectMaster')
                ->setParameter('TBProjectMaster', $searchTBProjectMaster);
        }
        //Status
        $searchStatus = $search->getStatus();
        if(isset($searchStatus))
        {
            $queryBuilder = $queryBuilder->andWhere('p.Status = :Status')
                ->setParameter('Status', $searchStatus);
        }
        //PeriodStart
        $searchPeriodstart = $search->getPeriodstart();
        if(isset($searchPeriodstart))
        {
            $queryBuilder = $queryBuilder->andWhere('c.WorkDate >= :Periodstart')
                ->setParameter('Periodstart', $searchPeriodstart);
        }
        //PeriodEnd
        $searchPeriodend = $search->getPeriodend();
        if(isset($searchPeriodend))
        {
            $queryBuilder = $queryBuilder->andWhere('c.WorkDate <= :Periodend')
                ->setParameter('Periodend', $searchPeriodend);
        }
//        //TBSystemUser
//        $searchTBSystemUser = $search->getTBSystemUser();
//        if(isset($searchTBSystemUser))
//        {
//            $queryBuilder = $queryBuilder->andWhere('u = :TBSystemUser')
//                ->setParameter('TBSystemUser', $searchTBSystemUser);
//        }

        //全件数取得
        $queryBuilderCount = clone $queryBuilder;
        $queryBuilderCount = $queryBuilderCount->select('count(c.id)');
        $queryCount = $queryBuilderCount->getQuery();
        $allCount = $queryCount->getSingleScalarResult();

        //ソート
        if($sortName != null && $sortType != null && ($sortType == 'asc' || $sortType == 'desc'))
        {
            switch($sortName)
            {
                case 'TBProjectMaster'://案件名
                    $queryBuilder = $queryBuilder->orderBy('p.Name', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('c.id', 'DESC');
                    break;
                case 'ProjectCostName'://作業項目名
                    $queryBuilder = $queryBuilder->orderBy('pc.Name', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('c.id', 'DESC');
                    break;
                case 'Status'://状態
                    $queryBuilder = $queryBuilder->orderBy('p.Status', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('c.id', 'DESC');
                    break;
                case 'WorkDate'://作業日
                    $queryBuilder = $queryBuilder->orderBy('c.WorkDate', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('c.id', 'DESC');
                    break;
//                case 'TBSystemUser'://作業者
//                    $queryBuilder = $queryBuilder->orderBy('c.TBSystemUserSystemUserId', $sortType);
//                    $queryBuilder = $queryBuilder->addOrderBy('c.id', 'DESC');
//                    break;
                case 'Cost'://作業工数
                    $queryBuilder = $queryBuilder->orderBy('c.Cost', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('c.id', 'DESC');
                    break;

                default:
//                    $sortColumn = $pager->getColumn($sortName);
//                    $queryBuilder = $queryBuilder->orderBy('vp.'.$sortColumn['db_column_name'], $sortType);
                    break;
            }
        }

        $queryBuilder = $queryBuilder->setFirstResult($pageSize*($pageNo-1))
            ->setMaxResults($pageSize);

        //クエリー実行
        $entities = $queryBuilder->getQuery()->getResult();

        $result = array();
        $result['count'] = $allCount;
        $result['result'] = $entities;

        return $result;
    }

    public function getCostList2($id, $pageNo, $pageSize, $sortName = null, $sortType = null, TBProductionCostSearchModel2 $search = null, TBSystemUser $operateUser = null)
    {
        $this->em->clear();
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBProductionCost')
            ->createQueryBuilder('c')
            ->join('c.TBSystemUserSystemUserId', 'u')
            ->join('c.TBProjectCostMasterProjectCostMasterId', 'pc')
            ->join('pc.TBProjectMasterProjectMasterId', 'p')
            ->andWhere('u.DeleteFlag = false')
            ->andWhere('c.DeleteFlag = false')
            ->andWhere('pc.DeleteFlag = false')
            ->andWhere('p.DeleteFlag = false')
            ->andWhere('p.id = :ID')
            ->setParameter('ID', $id)
            ->select(array(
                'u',
                'c',
                'pc',
                'p',
//                'partial u.{id, DisplayName}',
//                'partial c.{id, Cost, WorkDate}',
//                'partial pc.{id, Name}',
//                'partial p.{id, Name, Status}',
            ))
        ;

        //検索
        if($search == null){
            $search = new TBProductionCostSearchModel();
        }
//        //TBProjectMaster
//        $searchTBProjectMaster = $search->getTBProjectMaster();
//        if(isset($searchTBProjectMaster))
//        {
//            $queryBuilder = $queryBuilder->andWhere('p = :TBProjectMaster')
//                ->setParameter('TBProjectMaster', $searchTBProjectMaster);
//        }
//        //Status
//        $searchStatus = $search->getStatus();
//        if(isset($searchStatus))
//        {
//            $queryBuilder = $queryBuilder->andWhere('p.Status = :Status')
//                ->setParameter('Status', $searchStatus);
//        }
        //PeriodStart
        $searchPeriodstart = $search->getPeriodstart();
        if(isset($searchPeriodstart))
        {
            $queryBuilder = $queryBuilder->andWhere('c.WorkDate >= :Periodstart')
                ->setParameter('Periodstart', $searchPeriodstart);
        }
        //PeriodEnd
        $searchPeriodend = $search->getPeriodend();
        if(isset($searchPeriodend))
        {
            $queryBuilder = $queryBuilder->andWhere('c.WorkDate <= :Periodend')
                ->setParameter('Periodend', $searchPeriodend);
        }
        //TBSystemUser
        $searchTBSystemUser = $search->getTBSystemUser();
        if(isset($searchTBSystemUser))
        {
            $queryBuilder = $queryBuilder->andWhere('u = :TBSystemUser')
                ->setParameter('TBSystemUser', $searchTBSystemUser);
        }

        //全件数取得
        $queryBuilderCount = clone $queryBuilder;
        $queryBuilderCount = $queryBuilderCount->select('count(c.id)');
        $queryCount = $queryBuilderCount->getQuery();
        $allCount = $queryCount->getSingleScalarResult();

        //ソート
        if($sortName != null && $sortType != null && ($sortType == 'asc' || $sortType == 'desc'))
        {
            switch($sortName)
            {
//                case 'TBProjectMaster'://案件名
//                    $queryBuilder = $queryBuilder->orderBy('p.Name', $sortType);
//                    $queryBuilder = $queryBuilder->addOrderBy('c.id', 'DESC');
//                    break;
                case 'ProjectCostName'://作業項目名
                    $queryBuilder = $queryBuilder->orderBy('pc.Name', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('c.id', 'DESC');
                    break;
//                case 'Status'://状態
//                    $queryBuilder = $queryBuilder->orderBy('p.Status', $sortType);
//                    $queryBuilder = $queryBuilder->addOrderBy('c.id', 'DESC');
//                    break;
                case 'WorkDate'://作業日
                    $queryBuilder = $queryBuilder->orderBy('c.WorkDate', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('c.id', 'DESC');
                    break;
                case 'TBSystemUser'://作業者
                    $queryBuilder = $queryBuilder->orderBy('c.TBSystemUserSystemUserId', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('c.id', 'DESC');
                    break;
                case 'Cost'://作業工数
                    $queryBuilder = $queryBuilder->orderBy('c.Cost', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('c.id', 'DESC');
                    break;

                default:
//                    $sortColumn = $pager->getColumn($sortName);
//                    $queryBuilder = $queryBuilder->orderBy('vp.'.$sortColumn['db_column_name'], $sortType);
                    break;
            }
        }

        $queryBuilder = $queryBuilder->setFirstResult($pageSize*($pageNo-1))
            ->setMaxResults($pageSize);

        //クエリー実行
        $entities = $queryBuilder->getQuery()->getResult();

        $result = array();
        $result['count'] = $allCount;
        $result['result'] = $entities;

        return $result;
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

            //管理者登録
            $tbProjectUser = new TBProjectUser();
            $tbProjectUser->setTBSystemUserSystemUserId($project->getManager());
            $tbProjectUser->setSystemUserId($project->getManager()->getId());
            $tbProjectUser->setTBProjectMasterProjectMasterId($tbProjectMaster);
            $tbProjectUser->setProjectMasterId($tbProjectMaster->getId());
            $tbProjectUser->setRoleNo(TBProjectUser::ROLE_MANAGER);
            $this->em->persist($tbProjectUser);
            $this->em->flush();

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
//            ->leftJoin('pcm.TBProductionCostsProjectCostMasterId', 'pc')

            ->leftJoin('pcm.TBProductionCostsProjectCostMasterId', 'pc', 'WITH', 'pc.DeleteFlag = FALSE')
        //$qb->expr()->eq('p.area_code', 55)

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
//            ->leftJoin('pcm.TBProductionCostsProjectCostMasterId', 'pc')
            ->leftJoin('pcm.TBProductionCostsProjectCostMasterId', 'pc', 'WITH', 'pc.DeleteFlag = FALSE')
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

    /**
     * プロジェクトに所属するユーザー一覧取得
     * @param $projectId
     * @return array
     */
    public function getProjectUsers($projectId)
    {
        //プロジェクトユーザー
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBProjectUser')
            ->createQueryBuilder('pu')
            ->Join('pu.TBSystemUserSystemUserId', 'u')
            ->select(array(
                'pu',
                'u',
            ))
            ->andWhere('pu.ProjectMasterId = :ProjectMasterId')
            ->andWhere('u.DeleteFlag = false')
            ->setParameter('ProjectMasterId', $projectId)
        ;
        $tbProjectUsers = $queryBuilder->getQuery()->getResult();

        return $tbProjectUsers;
    }

    /**
     * プロジェクトに所属するユーザーを設定
     * @param $projectId
     * @param $users $users['user']登録するTBSystemUser $users['role']ロール番号
     * @throws \Exception
     */
    public function createProjectUsers($projectId, $users)
    {
//        $ret = array();
        $this->em->getConnection()->beginTransaction();
        try {

            //プロジェクトユーザー
            /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
            $queryBuilder = $this->em
                ->getRepository('ArtePCMSBizlogicBundle:TBProjectMaster')
                ->createQueryBuilder('p')
                ->leftJoin('p.TBSystemUserManagerId', 'm')
                ->leftJoin('p.TBProjectUsersProjectMasterId', 'pu')
                ->leftJoin('pu.TBSystemUserSystemUserId', 'u')
                ->select(array(
                    'p',
                    'm',
                    'pu',
                    'u',
                ))
                ->andWhere('p.id = :id')
                ->andWhere('p.DeleteFlag = false')
                ->setParameter('id', $projectId)
            ;
            /** @var $tbProject TBProjectMaster */
            $tbProject = $queryBuilder->getQuery()->getSingleResult();

            //全削除
            foreach($tbProject->getTBProjectUsersProjectMasterId() as $value)
            {
                /** @var $value TBProjectUser */
//                //プロジェクトオーナーは削除しない
//                if($value->getTBSystemUserSystemUserId() == $tbProject->getTBSystemUserManagerId()){
//                    continue;
//                }
                $this->em->remove($value);
                $this->em->flush();
            }

//            //プロジェクトユーザー
//            $userIds = array_keys($users);
//            /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
//            $queryBuilder = $this->em
//                ->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')
//                ->createQueryBuilder('u')
//                ->select(array(
//                    'u',
//                ))
//                ->andWhere('u.DeleteFlag = false')
//                ->andWhere('u.id in (:ids)')
//                ->setParameter('ids', $userIds)
//            ;
//            $tbSystemUsers = $queryBuilder->getQuery()->getResult();

            //登録

            //プロジェクト管理者登録
            $tbProjectUser = new TBProjectUser();
            $tbProjectUser->setTBSystemUserSystemUserId($tbProject->getTBSystemUserManagerId());
            $tbProjectUser->setSystemUserId($tbProject->getTBSystemUserManagerId()->getId());
            $tbProjectUser->setTBProjectMasterProjectMasterId($tbProject);
            $tbProjectUser->setProjectMasterId($tbProject->getId());
            $tbProjectUser->setRoleNo(TBProjectUser::ROLE_MANAGER);//管理者権限
            $this->em->persist($tbProjectUser);
            $this->em->flush();

            foreach($users as $value)
            {
                /** @var $user TBSystemUser */
                $user = $value['user'];
                $role = $value['role'];
                //プロジェクトオーナーの場合はすでに登録されてあるので除外する
                if($user == $tbProject->getTBSystemUserManagerId()){
                    continue;
                }

                $tbProjectUser = new TBProjectUser();
                $tbProjectUser->setTBSystemUserSystemUserId($user);
                $tbProjectUser->setSystemUserId($user->getId());
                $tbProjectUser->setTBProjectMasterProjectMasterId($tbProject);
                $tbProjectUser->setProjectMasterId($tbProject->getId());
                $tbProjectUser->setRoleNo($role);
                $this->em->persist($tbProjectUser);
                $this->em->flush();

//                $ret[] = $tbProjectUser;
            }

            $this->em->getConnection()->commit();
        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
        }

//        return $ret;
    }

    /**
     * プロジェクトコストマスタ　取得
     * @param $projectCostId
     * @return mixed
     */
    public function getProjectCost($projectCostId)
    {
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBProjectCostMaster')
            ->createQueryBuilder('pcm')
            ->Join('pcm.TBProjectMasterProjectMasterId', 'pm')
            ->select(array(
                'pcm',
                'pm',
            ))
//            ->andWhere('pcm.ProjectMasterId = :ProjectCostId')
            ->andWhere('pcm.id = :ProjectCostId')
            ->andWhere('pcm.DeleteFlag = false')
            ->setParameter('ProjectCostId', $projectCostId)
        ;
        $tbProjectCostMaster = $queryBuilder->getQuery()->getSingleResult();

        return $tbProjectCostMaster;
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