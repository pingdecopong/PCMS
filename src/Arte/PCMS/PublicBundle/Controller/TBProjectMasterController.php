<?php

namespace Arte\PCMS\PublicBundle\Controller;

use Arte\PCMS\BizlogicBundle\Lib\ProjectManager;
use Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProject;
use Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost;
use Arte\PCMS\PublicBundle\Form\TBProjectMasterFormModel;
use Arte\PCMS\PublicBundle\Form\TBProjectMasterFormType;
use Arte\PCMS\PublicBundle\Form\TBProjectMasterSubEndFormModel;
use Arte\PCMS\PublicBundle\Form\TBProjectMasterSubFormModel;
use Arte\PCMS\PublicBundle\Form\TestMainFormModel;
use Arte\PCMS\PublicBundle\Form\TestMainFormType;
use Arte\PCMS\PublicBundle\Form\TestSubFormModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\AST\Join;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use pingdecopong\PagerBundle\Pager\Pager;
use pingdecopong\PDPGeneratorBundle\Lib\SubFormModel;
use pingdecopong\PDPGeneratorBundle\Lib\SubFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster;
use Arte\PCMS\PublicBundle\Form\TBProjectMasterSearchType;

/**
 * TBProjectMaster controller.
 *
 * @Route("/project")
 */
class TBProjectMasterController extends Controller
{

    /**
     * Lists all TBProjectMaster entities.
     *
     * @Route("/", name="project")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $formFactory = $this->get('form.factory');
        $validator = $this->get('validator');
        $pager = new Pager($formFactory, $validator);

        //pager setting
        $pager
            ->addColumn('pn', array(
                'label' => '案件名',
                'sort_enable' => true,
                'db_column_name' => 'Name',
            ))
            ->addColumn('cn', array(
                'label' => '顧客',
                'sort_enable' => true,
//                'db_column_name' => 'CustomerId',
            ))
            ->addColumn('ps', array(
                'label' => '開始日',
                'sort_enable' => true,
                'db_column_name' => 'PeriodStart',
            ))
            ->addColumn('pe', array(
                'label' => '終了日',
                'sort_enable' => true,
                'db_column_name' => 'PeriodEnd',
            ))
            ->addColumn('mn', array(
                'label' => '管理者',
                'sort_enable' => true,
//                'db_column_name' => 'ManagerId',
            ))
            ->addColumn('st', array(
                'label' => '状態',
                'sort_enable' => true,
                'db_column_name' => 'Status',
            ))
            ->addColumn('et', array(
                'label' => '見積工数',
                'sort_enable' => true,
                'db_column_name' => 'ProductionTotalCost',
            ))
            ->addColumn('pct', array(
                'label' => '実工数',
                'sort_enable' => true,
                'db_column_name' => 'ProductionTotalCost',
            ))
        ;

        /* @var $form \Symfony\Component\Form\Form */
        $form = $formFactory->createNamedBuilder('f', 'form', null, array('csrf_protection' => false))
            ->add($pager->getFormBuilder())
            ->add('search', new TBProjectMasterSearchType())
            ->getForm();
        $form->bind($request);

        //pager
        $formView = $form->createView();
        $pager->setAllFormView($formView);
        $pager->setPagerFormView($formView[$pager->getFormName()]);
        $pager->setLinkRouteName($request->get('_route'));

        if($request->isMethod('POST') && $form->isValid())
        {
            $queryAllData = $pager->getAllFormQueryStrings();
            $queryPagerData = $pager->getPagerFormQueryKeyStrings();
            $queryAllData[$queryPagerData['pageNo']] = 1;

            return $this->redirect($this->generateUrl($request->get('_route'), $queryAllData));
        }

        if(($request->isMethod('GET') && !$form->isValid()) || !$pager->isValid())
        {
            return $this->redirect($this->generateUrl($request->get('_route')));
        }


        //db
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->getDoctrine()
            ->getRepository('ArtePCMSBizlogicBundle:VProjectView')
            ->createQueryBuilder('vp')
            ->leftJoin('vp.TBCustomerCustomerId', 'c')
            ->leftJoin('vp.TBSystemUserManagerId', 'u')
//            ->leftJoin('p.TBProjectCostMastersProjectMasterId', 'pcm', Join::)
//            ->leftJoin($queryBuilderx->getDQL(), 'pcm')
//            ->leftJoin('p.TBProjectCostMastersProjectMasterId', 'pcm')
//            ->select(array('p', 'c', 'u', 'SUM(pcm.Cost) as aaa'))
//            ->select(array('vp', 'c', 'u'))
//            ->select('vp, partial c.{id, Name}, partial u.{id, DisplayName}')
            ->select(array(
                'vp',
                'partial c.{id, Name}',
                'partial u.{id, DisplayName}',
            ))
//            ->groupBy('p.id')
//            ->andWhere('p.DeleteFlag = :DeleteFlag')
//            ->setParameter('DeleteFlag', false)
        ;

        //検索
        $data = $form->getData();
        /** @var $searchParam TBProjectMaster */
        $searchParam = $data['search'];
        //Name
        $searchName = $searchParam->getName();
        if(isset($searchName) && $form['search']['Name']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('vp.Name LIKE :Name')
                ->setParameter('Name', '%'.$searchName.'%');
        }
        //Status
        $searchStatus = $searchParam->getStatus();
        if(isset($searchStatus) && $form['search']['Status']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('vp.Status = :Status')
                ->setParameter('Status', $searchStatus);
        }
        //PeriodStart
        $searchPeriodstart = $searchParam->getPeriodstart();
        if(isset($searchPeriodstart) && $form['search']['PeriodStart']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('vp.PeriodEnd >= :Periodstart')
                ->setParameter('Periodstart', $searchPeriodstart);
        }
        //PeriodEnd
        $searchPeriodend = $searchParam->getPeriodend();
        if(isset($searchPeriodend) && $form['search']['PeriodEnd']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('vp.PeriodStart <= :Periodend')
                ->setParameter('Periodend', $searchPeriodend);
        }

        //relation 検索
        //TBCustomerCustomerId
        $searchTbcustomercustomerid = $searchParam->getTbcustomercustomerid();
        if(isset($searchTbcustomercustomerid) && $form['search']['TBCustomerCustomerId']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('vp.TBCustomerCustomerId = :Tbcustomercustomerid')
                ->setParameter('Tbcustomercustomerid', $searchTbcustomercustomerid);
        }
        //TBSystemUserManagerId
        $searchTbsystemusermanagerid = $searchParam->getTbsystemusermanagerid();
        if(isset($searchTbsystemusermanagerid) && $form['search']['TBSystemUserManagerId']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('vp.TBSystemUserManagerId = :Tbsystemusermanagerid')
                ->setParameter('Tbsystemusermanagerid', $searchTbsystemusermanagerid);
        }

        //全件数取得
        $queryBuilderCount = clone $queryBuilder;
        $queryBuilderCount = $queryBuilderCount->select('count(vp.id)');
        $queryCount = $queryBuilderCount->getQuery();
        $allCount = $queryCount->getSingleScalarResult();
        $pager->setAllCount($allCount);

        //ソート
        $pageSortName = $pager->getSortName();
        $pageSortType = $pager->getSortType();
        if($pageSortName != null && $pageSortType != null)
        {
            switch($pageSortName)
            {
                case 'cn'://顧客
                    $queryBuilder = $queryBuilder->orderBy('c.Name', $pageSortType);
                    break;
                case 'mn'://管理者
                    $queryBuilder = $queryBuilder->orderBy('u.DisplayName', $pageSortType);
                    break;
                default:
                    $sortColumn = $pager->getColumn($pageSortName);
                    $queryBuilder = $queryBuilder->orderBy('vp.'.$sortColumn['db_column_name'], $pageSortType);
                    break;
            }
        }

        //ページング
        $pageSize = $pager->getPageSize();
        $pageNo = $pager->getPageNo();
        if($pager->getMaxPageNum() < $pageNo){
            return $this->redirect($request->get('_route'));
        }
        $queryBuilder = $queryBuilder->setFirstResult($pageSize*($pageNo-1))
            ->setMaxResults($pageSize);

        //クエリー実行
        $entities = $queryBuilder->getQuery()->getResult();
/*
        //projectCostMaster cost count
        $ids = array();
        foreach($entities as $entity)
        {
            $ids[] = $entity->getId();
        }
        $queryBuilder = $this->getDoctrine()
            ->getRepository('ArtePCMSBizlogicBundle:TBProjectMaster')
            ->createQueryBuilder('p')
            ->leftJoin('p.TBProjectCostMastersProjectMasterId', 'pcm')
            ->select(array('p.id', 'SUM(pcm.Cost) as pcm_total'))
            ->andWhere('p.DeleteFlag = :DeleteFlag')
            ->andWhere('pcm.DeleteFlag = :PCMDeleteFlag')
            ->andWhere('p.id IN (:ids)')
            ->groupBy('p.id')
            ->setParameter('DeleteFlag', false)
            ->setParameter('PCMDeleteFlag', false)
            ->setParameter('ids', $ids);
        $pcmTotals = $queryBuilder->getQuery()->getScalarResult();

        //productionCost count
        $queryBuilder = $this->getDoctrine()
            ->getRepository('ArtePCMSBizlogicBundle:TBProjectCostMaster')
            ->createQueryBuilder('pcm')
            ->leftJoin('pcm.TBProductionCostsProjectCostMasterId', 'pc')
            ->select(array('pcm.ProjectMasterId', 'SUM(pc.Cost) as pc_total'))
            ->andWhere('pcm.DeleteFlag = :DeleteFlag')
//            ->andWhere('pc.DeleteFlag = :PCDeleteFlag')
            ->andWhere('pcm.ProjectMasterId IN (:ids)')
            ->groupBy('pcm.ProjectMasterId')
            ->setParameter('DeleteFlag', false)
//            ->setParameter('PCDeleteFlag', false)
            ->setParameter('ids', $ids);
        $pcTotals = $queryBuilder->getQuery()->getScalarResult();

        //
        $pcmTotalEntities = array();
        $pcTotalEntities = array();
        foreach($entities as $entity)
        {
            $id = $entity->getId();

            foreach($pcmTotals as $pcmTotal)
            {
                if($pcmTotal['id'] == $id){
                    $pcmTotalEntities[$id] = $pcmTotal['pcm_total'];
                }
            }
            foreach($pcTotals as $pcTotal)
            {
                if($pcTotal['ProjectMasterId'] == $id){
                    $pcTotalEntities[$id] = $pcTotal['pc_total'];
                }
            }
        }
*/
/*
        //native test
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster', 'pm');
        $rsm->addFieldResult('pm', 'id', 'id');
        $rsm->addFieldResult('pm', 'Name', 'name');

        $sql = "SELECT id, name FROM TBProjectMaster";
        $em = $this->getDoctrine()->getManager();
        $query = $em->createNativeQuery($sql, $rsm);
        $TBProjectMasters = $query->getResult();
*/

        //returnURL生成
        $returnUrlQueryDataArray = $pager->getAllFormQueryStrings();
        $returnUrlQueryString = urlencode(http_build_query($returnUrlQueryDataArray));

        return array(
            'form' => $formView,
            'pager' => $pager->createView(),
            'entities' => $entities,
            'returnUrlParam' => $returnUrlQueryString,
//            'pcTotal' => $pcTotalEntities,
//            'pcmTotal' => $pcmTotalEntities,
        );
    }

    /**
     * TBProjectMaster新規作成
     *
     * @Route("/new2", name="project_new2")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function new2Action()
    {
        $request = $this->getRequest();

        /** @var  $formFactory \Symfony\Component\Form\FormFactory */
        $formFactory = $this->get('form.factory');

//        $formModel = new TBProjectMasterFormModel();
//        $formType = new TBProjectMasterFormType();
//        $form = $this->createForm($formType, $formModel);

        $formModel = new TBProjectMasterFormModel();
        $formType = new TBProjectMasterFormType();
        $subFormModel = new SubFormModel();
        $subFormType = new SubFormType();

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder =$formFactory->createBuilder('form', null, array('cascade_validation' => true));
        $mainFormBuilder = $formFactory->createBuilder($formType, $formModel, array('cascade_validation' => true));
        $subFormBuilder = $formFactory->createBuilder($subFormType, $subFormModel, array('cascade_validation' => true));
        $form = $builder->add($mainFormBuilder)
            ->add($subFormBuilder)
            ->getForm();


        if($request->isMethod('POST'))
        {
            $form->bind($request);
        }

        return array(
            'form' => $form->createView(),
        );
    }
    /**
     * TBProjectMaster新規作成
     *
     * @Route("/new", name="project_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction()
    {

        $request = $this->getRequest();
        $formFactory = $this->get('form.factory');

        $formModel = new TBProjectMasterFormModel();
        $formType = new TBProjectMasterFormType();
        $subFormModel = new SubFormModel();
        $subFormType = new SubFormType();
        $subFormModel->setReturnAddress($request->get('ret'));

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder =$formFactory->createBuilder('form', null, array('cascade_validation' => true));
        $mainFormBuilder = $formFactory->createBuilder($formType, $formModel);
        $subFormBuilder = $formFactory->createBuilder($subFormType, $subFormModel);
//        $form = $builder->add($mainFormBuilder)
//            ->add($subFormBuilder)
//            ->getForm();
        $form = $builder->add($mainFormBuilder)
            ->add($subFormBuilder)
            ->add('AddSubForm', 'submit', array(
                'label' => '項目追加'
            ))
            ->add('Confirm', 'submit', array(
                'label' => '確認'
            ))
            ->add('Return', 'submit', array(
                'label' => '戻る'
            ))
            ->add('Submit', 'submit', array(
                'label' => '登録'
            ))
            ->getForm();

        if($request->isMethod('POST'))
        {
            $form->bind($request);
            $data = $form->getData();

            //button_action
//            $buttonAction = $subFormModel->getButtonAction();
//$form->get('AddSubForm')->isClicked()
//            if($buttonAction == "confirm" || $buttonAction == "submit")
            if($form->get('Confirm')->isClicked() || $form->get('Submit')->isClicked())
            {
                if($form->isValid())
                {
                    if($form->get('Confirm')->isClicked())
                    {
                        //確認画面
                        $builder->setAttribute('freeze', true);
                        $confirmForm = $builder->getForm();

                        return array(
                            'mode' => "confirm",
                            'entity' => $formModel,
                            'form' => $confirmForm->createView(),
                            'returnUrlParam' => urldecode($subFormModel->getReturnAddress()),
                        );

                    }else if($form->get('Submit')->isClicked())
                    {
                        //登録実行
                        try{
//                            $em = $this->getDoctrine()->getManager();
//                            $em->persist($formModel);
//                            $em->flush();

                            $em = $this->getDoctrine()->getManager();
                            $projectManager = new ProjectManager($em);
                            $newProject = new ProjectManagerProject();

                            $newProject->setName($formModel->getName());
                            $newProject->setStatus($formModel->getStatus());
                            $newProject->setExplanation($formModel->getExplanation());
                            $newProject->setPeriodStart($formModel->getPeriodStart());
                            $newProject->setPeriodEnd($formModel->getPeriodEnd());
                            $newProject->setEstimateFilePath($formModel->getEstimateFilePath());
                            $newProject->setScheduleFilePath($formModel->getScheduleFilePath());
                            $newProject->setCustomer($formModel->getTBCustomerCustomerId());
                            $newProject->setManager($formModel->getTBSystemUserManagerId());

                            $this->exchangeFormModelToDataModel($formModel->getSubForms(), $newProject->getCosts());
//                            $this->exchangeFormModelToDataModel($formModel->getSubForms(), $newProject);

                            $newProject = $projectManager->createProject($newProject);

                        }catch (\Exception $e){
                            throw $e;
                        }
                            return $this->redirect($this->generateUrl('project_show', array('id' => $newProject->getId(), 'ret' => $subFormModel->getReturnAddress())));
                    }
                }
            }else
            {
                //項目追加
                if($form->get('AddSubForm')->isClicked())
                {
                    $projectMasterSubFormModel = new TBProjectMasterSubFormModel();
                    $projectMasterSubFormModel->setGroupFlag(false);
                    $formModel->addSubForm($projectMasterSubFormModel);
                }else{

                    $projectMasterSubForms = $form->get('TBProjectMaster')->get('SubForms');
                    /** @var  $subForms \Symfony\Component\Form\Form*/
                    $subForms = $projectMasterSubForms->all();
                    $subFormModels = $projectMasterSubForms->getData();

//                    $sortCount = 0;
//                    $this->setSubFormSortNo($projectMasterSubForms, $sortCount);
                    $this->checkProjectSubForm($projectMasterSubForms, $projectMasterSubForms);
                }



                //移動上
//                foreach($subForms as $value)
//                {
//                    /** @var  $value \Symfony\Component\Form\Form*/
//                    if($value->get('Up')->isClicked())
//                    {
//                        $index = $subFormModels->indexOf($value->getData());
//                        if($index != 0)
//                        {
//                            $temp = $subFormModels->get($index-1);
//                            $subFormModels->set($index-1, $value->getData());
//                            $subFormModels->set($index, $temp);
//                        }
//                    }
//                }

//                if($data['other_action'] == 'add')
//                {
//                    $projectMasterSubForm = new TBProjectMasterSubFormModel();
//                    $formModel->getProjectMasterSubs()->add($projectMasterSubForm);
//                }
                //ドロップダウンなどのポストバック
                $builder->setAttribute('novalidation', true);
                $form = $builder->getForm();
            }
        }

//        $formView = $form->createView();
//        $formView->children['Return']->setRendered(true);
//        $formView->children['Submit']->setRendered(true);

        return array(
            'mode' => "input",
            'validate' => false,
            'entity' => $formModel,
            'form' => $form->createView(),
            'returnUrlParam' => urldecode($subFormModel->getReturnAddress()),
        );
    }

    private function exchangeFormModelToDataModel(ArrayCollection $formSubModels, ArrayCollection $costs)
    {
        foreach($formSubModels as $value)
        {
            /** @var $value TBProjectMasterSubFormModel*/
            $cost = new ProjectManagerProjectCost();
            $cost->setId($value->getId());
            $cost->setName($value->getName());

            if($value->getGroupFlag()){
                $this->exchangeFormModelToDataModel($value->getSubForms(), $cost->getChildCosts());
                $cost->setGroupFlag(true);
            }else{
                $cost->setCost($value->getCost());
                $cost->setGroupFlag(false);
            }

            $costs->add($cost);
        }
    }

//    private function setSubFormLast($forms, $form)
//    {
//        $subForms = $forms->all();
//        $subFormModels = $forms->getData();
//        $subFormsReverse = array_reverse($subForms);
//        foreach($subFormsReverse as $value)
//        {
//            if($value->getData()->getSortNo() == $cnt)
//            {
//                $form = $value;
//            }
//            if($value->has('SubForms'))
//            {
//                $this->setSubFormLast($value->get('SubForms'), $form);
//            }
//        }
//    }

//    private function searchSubForm($forms, $cnt, &$form)
//    {
//        $subForms = $forms->all();
//        $subFormModels = $forms->getData();
//        foreach($subForms as $value)
//        {
//            if($value->getData()->getSortNo() == $cnt)
//            {
//                $form = $value;
//            }
//            if($value->has('SubForms'))
//            {
//                $this->searchSubForm($value->get('SubForms'), $cnt, $form);
//            }
//        }
//    }
//    private function setSubFormSortNo($forms, &$cnt)
//    {
//        $subForms = $forms->all();
//        $subFormModels = $forms->getData();
//        foreach($subForms as $value)
//        {
//            $cnt++;
//            $value->getData()->setSortNo($cnt);
//            if($value->has('SubForms'))
//            {
//                $this->setSubFormSortNo($value->get('SubForms'), $cnt);
//            }
//        }
//    }

    private function searchLastSubForm($form, $setForm)
    {
        if($form->getGroupFlag())
        {
            $lastForm = $form->getSubForms()->last();

            if($lastForm == false){
                $form->getSubForms()->add($setForm);
            }else{

                if($lastForm->getGroupFlag()){
                    $this->searchLastSubForm($lastForm, $setForm);
                }else{
                    $form->getSubForms()->add($setForm);
                }

            }
        }
    }

    private function searchFirstSubForm($form, $setForm)
    {
        if($form->getGroupFlag())
        {
            $firstForm = $form->getSubForms()->first();

            if($firstForm == false){
                $form->getSubForms()->add($setForm);
            }else{

//                if($firstForm->getGroupFlag()){
//                    $this->searchFirstSubForm($firstForm, $setForm);
//                }else{
                    $temp = clone $form->getSubForms();//コピー
                    $form->getSubForms()->clear();//クリア

                    $form->getSubForms()->add($setForm);
                    foreach($temp as $value)
                    {
                        $form->getSubForms()->add($value);
                    }
//                }

            }
        }
    }

    private function checkProjectSubForm($rootForm, $forms)
    {
        $subForms = $forms->all();
        $subFormModels = $forms->getData();
        foreach($subForms as $value)
        {
            /** @var  $value \Symfony\Component\Form\Form*/
            if($value->get('Add')->isClicked())
            {
                $subForm = new TBProjectMasterSubFormModel();
                $value->getData()->addSubForm($subForm);
            }

            if($value->get('Group')->isClicked())
            {
                $flag = $value->getData()->getGroupFlag();

                if($flag){

                    $index = $value->getParent()->getParent()->getData()->getSubForms()->indexOf($value->getData());
                    $temp = clone $value->getParent()->getParent()->getData()->getSubForms();//コピー
                    $value->getParent()->getParent()->getData()->getSubForms()->clear();//クリア
                    foreach($temp as $key => $loopValue)
                    {
                        if($key == $index){
                            //挿入
//                            $loopValue->getSubForms()->clear();//クリア
                            $value->getParent()->getParent()->getData()->getSubForms()->add($loopValue);
                            foreach($value->getData()->getSubForms() as $loopValue2)
                            {
                                $value->getParent()->getParent()->getData()->getSubForms()->add($loopValue2);
                            }
                            $value->getData()->getSubForms()->clear();//クリア

                        }else{
                            $value->getParent()->getParent()->getData()->getSubForms()->add($loopValue);
                        }

                    }

                }else{

                }
                $value->getData()->setGroupFlag(!$flag);
                $value->getData()->setCost("");

//                $flag = $value->getData()->getGroupFlag();
//                $value->getData()->setGroupFlag(!$flag);
//                $value->getData()->setCost("");
//
//                //子のリストを親のリストに移動
//                foreach($value->getData()->getSubForms() as $valueSub)
//                {
//                    $value->getParent()->getParent()->getData()->addSubForm($valueSub);
//                }
//                $value->getData()->getSubForms()->clear();

            }

            if($value->get('Down')->isClicked())
            {
                $last = $value->getParent()->getParent()->getData()->getSubForms()->last();
                if($last === $value->getData())
                {
                    $parent = $value->getParent()->getParent();//親フォーム取得
                    //rootの場合は処理しない
                    if($parent->getData() instanceof TBProjectMasterFormModel){
                        break;
                    }

                    $indexParent = $parent->getParent()->getParent()->getData()->getSubForms()->indexOf($parent->getData());//親でのインデックス番号を取得
                    $temp = clone $parent->getParent()->getParent()->getData()->getSubForms();//コピー
                    $parent->getParent()->getParent()->getData()->getSubForms()->clear();//クリア
                    foreach($temp as $key => $loopValue)
                    {
                        $parent->getParent()->getParent()->getData()->getSubForms()->add($loopValue);
                        if($key == $indexParent){
                            //挿入
                            $parent->getParent()->getParent()->getData()->getSubForms()->add($value->getData());
                        }
                    }
                    $value->getParent()->getParent()->getData()->getSubForms()->removeElement($value->getData());
                }else{

                    $index = $value->getParent()->getParent()->getData()->getSubForms()->indexOf($value->getData());
                    $arrayKeys = $value->getParent()->getParent()->getData()->getSubForms()->getKeys();
                    $keyNo = array_search($index, $arrayKeys);
                    $temp = $value->getParent()->getParent()->getData()->getSubForms()->get($arrayKeys[$keyNo+1]);//一つ下のデータを取得

                    //同じ階層の１つ下のリストがグループの場合
                    if($temp->getGroupFlag())
                    {
                        $this->searchFirstSubForm($temp, $value->getData());
                        $value->getParent()->getParent()->getData()->getSubForms()->removeElement($value->getData());

                    }else{
                        $value->getParent()->getParent()->getData()->getSubForms()->set($arrayKeys[$keyNo+1], $value->getData());
                        $value->getParent()->getParent()->getData()->getSubForms()->set($arrayKeys[$keyNo], $temp);
                    }

                }

//                $count = $value->getParent()->getParent()->getData()->getSubForms()->count();
//                if($count == 1)
//                {
//
//                }
            }

            if($value->get('Up')->isClicked())
            {
                $count = $value->getParent()->getParent()->getData()->getSubForms()->count();
//                $index = $value->getParent()->getParent()->getData()->getSubForms()->indexOf($value->getData());

                $index = $value->getParent()->getParent()->getData()->getSubForms()->indexOf($value->getData());
                $arrayKeys = $value->getParent()->getParent()->getData()->getSubForms()->getKeys();
                $keyNo = array_search($index, $arrayKeys);

//                if($count == 1)
                if($keyNo == 0)
                {
                    $parent = $value->getParent()->getParent();//親フォーム取得
                    //rootの場合は処理しない
                    if($parent->getData() instanceof TBProjectMasterFormModel){
                        break;
                    }

                    $indexParent = $parent->getParent()->getParent()->getData()->getSubForms()->indexOf($parent->getData());//親でのインデックス番号を取得
                    $temp = clone $parent->getParent()->getParent()->getData()->getSubForms();//コピー
                    $parent->getParent()->getParent()->getData()->getSubForms()->clear();//クリア
                    foreach($temp as $key => $loopValue)
                    {
                        if($key == $indexParent){
                            //挿入
                            $parent->getParent()->getParent()->getData()->getSubForms()->add($value->getData());
                        }
                        $parent->getParent()->getParent()->getData()->getSubForms()->add($loopValue);

                    }
                    $value->getParent()->getParent()->getData()->getSubForms()->removeElement($value->getData());
                }else{

//                    $parent = $value->getParent()->getParent();//親フォーム取得
//                    //rootの場合は処理しない
//                    if($parent->getData() instanceof TBProjectMasterFormModel){
//                        break;
//                    }

                    $index = $value->getParent()->getParent()->getData()->getSubForms()->indexOf($value->getData());
                    $arrayKeys = $value->getParent()->getParent()->getData()->getSubForms()->getKeys();
                    $keyNo = array_search($index, $arrayKeys);

                    if($keyNo == 0)
                    {

                    }

//                    $keyTarget = $arrayKeys[$keyNo-1];
                    $temp = $value->getParent()->getParent()->getData()->getSubForms()->get($arrayKeys[$keyNo-1]);//一つ上のデータを取得
                    //同じ階層の１つ上のリストがグループの場合
                    if($temp->getGroupFlag())
                    {
                        $this->searchLastSubForm($temp, $value->getData());
                        $value->getParent()->getParent()->getData()->getSubForms()->removeElement($value->getData());

                    }else{
                        $value->getParent()->getParent()->getData()->getSubForms()->set($arrayKeys[$keyNo-1], $value->getData());
                        $value->getParent()->getParent()->getData()->getSubForms()->set($arrayKeys[$keyNo], $temp);
                    }

                }
            }
/*
            if($value->get('Up')->isClicked())
            {
                $index = $value->getParent()->getParent()->getData()->getSubForms()->indexOf($value->getData());
                if($index == 0)
                {
                    $parent = $value->getParent()->getParent();
                    //rootの場合は処理しない
                    if($parent->getData() instanceof TBProjectMasterFormModel){
                        break;
                    }
                    $index = $parent->getParent()->getParent()->getData()->getSubForms()->indexOf($parent->getData());
                    if($index == 0)
                    {
                        $temp = clone $parent->getParent()->getParent()->getData()->getSubForms();
                        $parent->getParent()->getParent()->getData()->getSubForms()->clear();
                        $parent->getParent()->getParent()->getData()->getSubForms()->add($value->getData());
                        foreach($temp as $loopValue)
                        {
                            $parent->getParent()->getParent()->getData()->getSubForms()->add($loopValue);
                        }
                        $value->getParent()->getParent()->getData()->getSubForms()->removeElement($value->getData());
                    }else{
                        $temp = clone $parent->getParent()->getParent()->getData()->getSubForms();
                        $parent->getParent()->getParent()->getData()->getSubForms()->clear();
                        foreach($temp as $key => $loopValue)
                        {
                            if($key == $index){
                                $parent->getParent()->getParent()->getData()->getSubForms()->add($value->getData());
                            }
                            $parent->getParent()->getParent()->getData()->getSubForms()->add($loopValue);

                        }
                        $value->getParent()->getParent()->getData()->getSubForms()->removeElement($value->getData());


//                        $temp = $parent->getParent()->getParent()->getData()->getSubForms()->get($index-1);
//                        $parent->getParent()->getParent()->getData()->getSubForms()->set($index-1, $value->getData());
//                        $parent->getParent()->getParent()->getData()->getSubForms()->set($index, $temp);
                    }


                }else{
                    $temp = $value->getParent()->getParent()->getData()->getSubForms()->get($index-1);
                    //同じ階層の１つ上のリストがグループの場合
                    if($temp->getGroupFlag())
                    {
                        $lastForm = $temp->getSubForms()->last();

                        if($lastForm == false){
                            //リストが無い
                            $temp->getSubForms()->add($value->getData());
                        }else if($lastForm->getGroupFlag()){
                            //リストの最後がグループの場合
                            $lastForm->getSubForms()->add($value->getData());
                        }else{
                            $temp->getSubForms()->add($value->getData());
                        }

//                        //リストが無い　又は、リストの最後がグループの場合
//                        if($lastForm == false || $lastForm->getGroupFlag())
//                        {
//                            $lastForm->getSubForms()->add($value->getData());
//                        }else{
//                            $temp->getSubForms()->add($value->getData());
//                        }

                        $value->getParent()->getParent()->getData()->getSubForms()->removeElement($value->getData());

                    }else{
                        $value->getParent()->getParent()->getData()->getSubForms()->set($index-1, $value->getData());
                        $value->getParent()->getParent()->getData()->getSubForms()->set($index, $temp);
                    }
                }



//                $sortNo = $value->getData()->getSortNo();
//                if($sortNo != 1)
//                {
//                    $targetForm = null;
//                    $this->searchSubForm($rootForm, $sortNo-1, $targetForm);
//
//                    if($targetForm->getParent()->getParent()->getData() === $value->getParent()->getParent()->getData())
//                    {
//                        $index = $value->getParent()->getParent()->getData()->getSubForms()->indexOf($value->getData());
//                        $temp = $value->getParent()->getParent()->getData()->getSubForms()->get($index-1);
//                        $value->getParent()->getParent()->getData()->getSubForms()->set($index-1, $value->getData());
//                        $value->getParent()->getParent()->getData()->getSubForms()->set($index, $temp);
//                    }else{
//                        $targetForm->getParent()->getParent()->getData()->getSubForms()->add($value->getData());
//                        $value->getParent()->getParent()->getData()->getSubForms()->removeElement($value->getData());
//                    }
//
//                }

//                $index = $value->getParent()->getParent()->getData()->getSubForms()->indexOf($value->getData());
//                if($index == 0)
//                {
//                    $sortNo = $value->getData()->getSortNo();
//                    if($sortNo != 1)
//                    {
//                        $targetForm = null;
//                        $this->searchSubForm($rootForm, $sortNo-1, $targetForm);
//                        $targetForm->getParent()->getParent()->getData()->getSubForms()->add($value->getData());
//                    }
//
////                    $parentIndex = $value->getParent()->getParent()->getParent()->getParent()->getData()->getSubForms()->indexOf($value->getParent()->getParent()->getData());
//
//                }else{
//                    $temp = $value->getParent()->getParent()->getData()->getSubForms()->get($index-1);
//                    $value->getParent()->getParent()->getData()->getSubForms()->set($index-1, $value->getData());
//                    $value->getParent()->getParent()->getData()->getSubForms()->set($index, $temp);
//                }
            }
*/
            if($value->get('Delete')->isClicked())
            {
//                if($value->getData()->getGroupFlag())
//                {
//                    $index = $value->getParent()->getParent()->getData()->getSubForms()->indexOf($value->getData());
//                    $temp = clone $value->getParent()->getParent()->getData()->getSubForms();//コピー
//                    $value->getParent()->getParent()->getData()->getSubForms()->clear();//クリア
//                    foreach($temp as $key => $loopValue)
//                    {
//                        if($key == $index){
//                            //挿入
//                            foreach($value->getData()->getSubForms() as $loopValue2)
//                            {
//                                $value->getParent()->getParent()->getData()->getSubForms()->add($loopValue2);
//                            }
//
//                        }else{
//                            $value->getParent()->getParent()->getData()->getSubForms()->add($loopValue);
//                        }
//
//                    }
//                }else{
//                    $value->getData()->getSubForms()->clear();
//                    $value->getParent()->getParent()->getData()->getSubForms()->removeElement($value->getData());
//                }

//                //子のリストを親のリストに移動
//                foreach($value->getData()->getSubForms() as $valueSub)
//                {
//                    $value->getParent()->getParent()->getData()->addSubForm($valueSub);
//                }

                $value->getData()->getSubForms()->clear();
                $value->getParent()->getParent()->getData()->getSubForms()->removeElement($value->getData());
            }

            //
            if($value->has('SubForms'))
            {
                $this->checkProjectSubForm($rootForm, $value->get('SubForms'));
            }
        }

    }

    /**
     * TBProjectMaster詳細
     *
     * @Route("/test", name="test")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function testAction()
    {
        $request = $this->getRequest();
        $formFactory = $this->get('form.factory');

        $formData = new TestMainFormModel();
        $formType = new TestMainFormType();

        if($request->isMethod('GET'))
        {
//            $data = new TestMainFormModel();
            $sub1_1 = new TestSubFormModel();
            $sub1_2 = new TestSubFormModel();
            $sub1_2_1 = new TestSubFormModel();
            $sub1_2_1_1 = new TestSubFormModel();

            $sub1_2_1->getSubForms()->add($sub1_2_1_1);
            $sub1_2->getSubForms()->add($sub1_2_1);

            $formData->getSubForms()->add($sub1_1);
            $formData->getSubForms()->add($sub1_2);


        }

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder =$formFactory->createBuilder('form', null, array('cascade_validation' => true));
//        $builder =$formFactory->createBuilder('form');
        $mainFormBuilder = $formFactory->createBuilder($formType, $formData);
        $form = $builder->add($mainFormBuilder)
            ->getForm();

//        $form = $this->createForm($formType, $formData);


        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
//            $form->bind($request);

        }
        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * TBProjectMaster詳細
     *
     * @Route("/{id}", name="project_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $projectManager = new ProjectManager($em);

        $project = $projectManager->readProject($id);

        //returnUrlデコード
        $returnUrlQueryString = urldecode($request->get('ret'));

        return array(
            'project' => $project,
//            'entity'      => $entity,
//            'ventity'     => $vprojectEntity,
//            'pcmentities'   => $pcmEntities,
            'returnUrlParam' => $returnUrlQueryString,
        );
    }

//    /**
//     * TBProjectMaster詳細
//     *
//     * @Route("/{id}", name="project_show")
//     * @Method("GET")
//     * @Template()
//     */
//    public function showAction($id)
//    {
//        $request = $this->getRequest();
////        $em = $this->getDoctrine()->getManager();
//
////        $entity = $em->getRepository('ArtePCMSBizlogicBundle:TBProjectMaster')->find($id);
//
//        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
//        $queryBuilder = $this->getDoctrine()
//            ->getRepository('ArtePCMSBizlogicBundle:TBProjectMaster')
//            ->createQueryBuilder('p')
//            ->leftJoin('p.TBCustomerCustomerId', 'c')
//            ->leftJoin('p.TBSystemUserManagerId', 'u')
//            ->select(array(
//                'p',
//                'partial c.{id, Name}',
//                'partial u.{id, DisplayName}',
//            ))
//            ->andWhere('p.id = :id')
//            ->andWhere('p.DeleteFlag = :DeleteFlag')
//            ->setParameter('id', $id)
//            ->setParameter('DeleteFlag', false)
//        ;
//        $entity = $queryBuilder->getQuery()->getSingleResult();
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find TBProjectMaster entity.');
//        }
//
//        //
//        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
//        $queryBuilder = $this->getDoctrine()
//            ->getRepository('ArtePCMSBizlogicBundle:VProjectView')
//            ->createQueryBuilder('vp')
//            ->select(array(
//                'partial vp.{id, ProjectTotalCost, ProductionTotalCost}',
//            ))
//            ->andWhere('vp.id = :id')
//            ->setParameter('id', $id)
//        ;
//        $vprojectEntity = $queryBuilder->getQuery()->getSingleResult();
//
//        //
//        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
//        $queryBuilder = $this->getDoctrine()
//            ->getRepository('ArtePCMSBizlogicBundle:TBProjectCostMaster')
//            ->createQueryBuilder('pcm')
//            ->leftJoin('pcm.TBProductionCostsProjectCostMasterId', 'pc')
//            ->select(array(
//                'partial pcm.{id, Name, Cost}',
//                'sum(pc.Cost)',
//            ))
//            ->groupBy('pcm.id')
//            ->andWhere('pcm.ProjectMasterId = :ProjectMasterId')
//            ->andWhere('pcm.DeleteFlag = :pcmDeleteFlag')
//            ->andWhere('pc.DeleteFlag = :pcDeleteFlag OR pc.DeleteFlag IS NULL')
//            ->setParameter('ProjectMasterId', $id)
//            ->setParameter('pcmDeleteFlag', false)
//            ->setParameter('pcDeleteFlag', false)
//        ;
//        $pcmEntities = $queryBuilder->getQuery()->getResult();
//
//        //returnUrlデコード
//        $returnUrlQueryString = urldecode($request->get('ret'));
//
//        return array(
//            'entity'      => $entity,
//            'ventity'     => $vprojectEntity,
//            'pcmentities'   => $pcmEntities,
//            'returnUrlParam' => $returnUrlQueryString,
//        );
//    }

    /**
     * TBProjectMaster編集
     *
     * @Route("/edit/{id}", name="project_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($id)
    {

        $request = $this->getRequest();
        $formFactory = $this->get('form.factory');

//        $formModel = new TBProjectMasterFormModel();
//        $formType = new TBProjectMasterFormType();
//        $subFormModel = new SubFormModel();
//        $subFormType = new SubFormType();
//        $subFormModel->setReturnAddress($request->get('ret'));

        $formType = new TBProjectMasterFormType();
//        $formType = new TBProjectMasterType();
        $subFormModel = new SubFormModel();
        $subFormType = new SubFormType();
        $subFormModel->setReturnAddress($request->get('ret'));

        $em = $this->getDoctrine()->getManager();
        $projectManager = new ProjectManager($em);
        $project = $projectManager->readProject($id);

//        $entity = $em->getRepository('ArtePCMSPublicBundle:TBProjectMaster')->find($id);
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find SystemUser entity.');
//        }

        //
        $formModel = new TBProjectMasterFormModel();
        $formModel->setName($project->getName());
        $formModel->setStatus($project->getStatus());
        $formModel->setExplanation($project->getExplanation());
        $formModel->setPeriodStart($project->getPeriodStart());
        $formModel->setPeriodEnd($project->getPeriodEnd());
        $formModel->setEstimateFilePath($project->getEstimateFilePath());
        $formModel->setScheduleFilePath($project->getScheduleFilePath());
        $formModel->setTBCustomerCustomerId($project->getCustomer());
        $formModel->setTBSystemUserManagerId($project->getManager());

        if($request->isMethod('GET'))
        {
            $this->exchangeDataModelToFormModel($project->getCosts(), $formModel->getSubForms());
        }

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder =$formFactory->createBuilder();
        $mainFormBuilder = $formFactory->createBuilder($formType, $formModel);
        $subFormBuilder = $formFactory->createBuilder($subFormType, $subFormModel);
//        $form = $builder->add($mainFormBuilder)
//            ->add($subFormBuilder)
//            ->getForm();

        $form = $builder->add($mainFormBuilder)
            ->add($subFormBuilder)
            ->add('AddSubForm', 'submit', array(
                'label' => '項目追加'
            ))
            ->add('Confirm', 'submit', array(
                'label' => '確認'
            ))
            ->add('Return', 'submit', array(
                'label' => '戻る'
            ))
            ->add('Submit', 'submit', array(
                'label' => '登録'
            ))
            ->getForm();

        if($request->isMethod('POST'))
        {
            $form->bind($request);

            //button_action
//            $buttonAction = $subFormModel->getButtonAction();
//
//            if($buttonAction == "confirm" || $buttonAction == "submit")
            if($form->get('Confirm')->isClicked() || $form->get('Submit')->isClicked())
            {
                if($form->isValid())
                {
//                    if($buttonAction == "confirm")
                    if($form->get('Confirm')->isClicked())
                    {
                        //確認画面
                        $builder->setAttribute('freeze', true);
                        $confirmForm = $builder->getForm();

                        return array(
                            'mode' => "confirm",
//                            'entity' => $entity,
                            'ID' => $id,
                            'form' => $confirmForm->createView(),
                            'returnUrlParam' => urldecode($subFormModel->getReturnAddress()),
                        );

                    }else if($form->get('Submit')->isClicked())
                    {
                        //登録実行
                        try{
//                            $em->persist($entity);
//                            $em->flush();
//
//                            $em = $this->getDoctrine()->getManager();
//                            $projectManager = new ProjectManager($em);
//                            $newProject = new ProjectManagerProject();

                            $project->setName($formModel->getName());
                            $project->setStatus($formModel->getStatus());
                            $project->setExplanation($formModel->getExplanation());
                            $project->setPeriodStart($formModel->getPeriodStart());
                            $project->setPeriodEnd($formModel->getPeriodEnd());
                            $project->setEstimateFilePath($formModel->getEstimateFilePath());
                            $project->setScheduleFilePath($formModel->getScheduleFilePath());
                            $project->setCustomer($formModel->getTBCustomerCustomerId());
                            $project->setManager($formModel->getTBSystemUserManagerId());

                            $project->getCosts()->clear();
                            $this->exchangeFormModelToDataModel($formModel->getSubForms(), $project->getCosts());

                            $projectManager->editProject($project);

                        }catch (\Exception $e){
                            throw $e;
                        }
                        return $this->redirect($this->generateUrl('project_show', array('id' => $project->getId(), 'ret' => $subFormModel->getReturnAddress())));
                    }
                }
            }else
            {
                //項目追加
                if($form->get('AddSubForm')->isClicked())
                {
                    $projectMasterSubFormModel = new TBProjectMasterSubFormModel();
                    $projectMasterSubFormModel->setGroupFlag(false);
                    $formModel->addSubForm($projectMasterSubFormModel);
                }else{

                    $projectMasterSubForms = $form->get('TBProjectMaster')->get('SubForms');

                    $this->checkProjectSubForm($projectMasterSubForms, $projectMasterSubForms);
                }

                //ドロップダウンなどのポストバック
                $builder->setAttribute('novalidation', true);
                $form = $builder->getForm();
            }
        }

        return array(
            'mode' => "input",
            'validate' => false,
//            'entity' => $entity,
            'ID' => $id,
            'form' => $form->createView(),
            'returnUrlParam' => urldecode($subFormModel->getReturnAddress()),
        );
    }

    private function exchangeDataModelToFormModel(ArrayCollection $costs, ArrayCollection $formSubModels)
    {
        foreach($costs as $value)
        {
            /** @var $value ProjectManagerProjectCost*/
            $subForm = new TBProjectMasterSubFormModel();
            $subForm->setId($value->getId());
            $subForm->setName($value->getName());

            if($value->getGroupFlag()){
                $this->exchangeDataModelToFormModel($value->getChildCosts(), $subForm->getSubForms());
                $subForm->setGroupFlag(true);
            }else{
                $subForm->setCost($value->getCost());
                $subForm->setGroupFlag(false);
            }

            $formSubModels->add($subForm);
        }
    }

    /**
     * TBProjectMaster削除
     *
     * @Route("/delete/{id}", name="project_delete")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function deleteAction($id)
    {
        $request = $this->getRequest();
        //returnUrlデコード
        $returnUrlQueryString = urldecode($request->get('ret'));

        $form = $this->createFormBuilder(array('id' => $id, 'returnAddress' => $returnUrlQueryString))
            ->add('id', 'hidden')
            ->add('buttonAction', 'hidden')
            ->add('returnAddress', 'hidden')
            ->getForm();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ArtePCMSPublicBundle:TBProjectMaster')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TBProjectMaster entity.');
        }

        if($request->isMethod('POST'))
        {
            $form->bind($request);
            if ($form->isValid()) {

                try{
                    $em->remove($entity);
                    $em->flush();

                }catch (\Exception $e){
                    throw $e;
                }
                $data = $form->getData();
                //returnUrlデコード
                $returnUrlQueryString = urldecode($data['returnAddress']);
                return $this->redirect($this->generateUrl('watertank').'?'.$returnUrlQueryString);
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'returnUrlParam' => $returnUrlQueryString,
        );
    }


}
