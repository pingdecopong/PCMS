<?php

namespace Arte\PCMS\PublicBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use pingdecopong\PagerBundle\Pager\Pager;
use pingdecopong\PDPGeneratorBundle\Lib\SubFormModel;
use pingdecopong\PDPGeneratorBundle\Lib\SubFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster;
use Arte\PCMS\PublicBundle\Form\TBProjectMasterType;
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
            ->addColumn('Name', array(
                'label' => '案件名',
                'sort_enable' => true,
                'db_column_name' => 'Name',
            ))
            ->addColumn('CustomerId', array(
                'label' => '顧客',
                'sort_enable' => true,
                'db_column_name' => 'CustomerId',
            ))
            ->addColumn('PeriodStart', array(
                'label' => '開始日',
                'sort_enable' => true,
                'db_column_name' => 'PeriodStart',
            ))
            ->addColumn('PeriodEnd', array(
                'label' => '終了日',
                'sort_enable' => true,
                'db_column_name' => 'PeriodEnd',
            ))
            ->addColumn('ManagerId', array(
                'label' => '管理者',
                'sort_enable' => true,
                'db_column_name' => 'ManagerId',
            ))
            ->addColumn('Status', array(
                'label' => '状態',
                'sort_enable' => true,
                'db_column_name' => 'Status',
            ))
            ->addColumn('EstimateTotal', array(
                'label' => '見積工数',
                'sort_enable' => true,
                'db_column_name' => 'Status',
            ))
            ->addColumn('ProductionCostTotal', array(
                'label' => '実工数',
                'sort_enable' => true,
                'db_column_name' => 'Status',
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
            ->getRepository('ArtePCMSBizlogicBundle:TBProjectMaster')
            ->createQueryBuilder('p')
            ->leftJoin('p.TBCustomerCustomerId', 'c')
            ->leftJoin('p.TBSystemUserManagerId', 'u')
            ->leftJoin('p.TBProjectCostMastersProjectMasterId', 'pcm')
            ->select(array('p', 'c', 'u', 'SUM(pcm.Cost)'))
            ->groupBy('p.id')
            ->andWhere('p.DeleteFlag = :DeleteFlag')
            ->setParameter('DeleteFlag', false);

        //検索
        $data = $form->getData();
        /** @var $searchParam TBProjectMaster */
        $searchParam = $data['search'];
        //Name
        $searchName = $searchParam->getName();
        if(isset($searchName) && $form['search']['Name']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('p.Name LIKE :Name')
                ->setParameter('Name', '%'.$searchName.'%');
        }
        //Status
        $searchStatus = $searchParam->getStatus();
        if(isset($searchStatus) && $form['search']['Status']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('p.Status = :Status')
                ->setParameter('Status', $searchStatus);
        }
        //PeriodStart
        $searchPeriodstart = $searchParam->getPeriodstart();
        if(isset($searchPeriodstart) && $form['search']['PeriodStart']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('p.PeriodStart = :Periodstart')
                ->setParameter('Periodstart', $searchPeriodstart);
        }
        //PeriodEnd
        $searchPeriodend = $searchParam->getPeriodend();
        if(isset($searchPeriodend) && $form['search']['PeriodEnd']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('p.PeriodEnd = :Periodend')
                ->setParameter('Periodend', $searchPeriodend);
        }

        //relation 検索
        //TBCustomerCustomerId
        $searchTbcustomercustomerid = $searchParam->getTbcustomercustomerid();
        if(isset($searchTbcustomercustomerid) && $form['search']['TBCustomerCustomerId']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('p.TBCustomerCustomerId = :Tbcustomercustomerid')
                ->setParameter('Tbcustomercustomerid', $searchTbcustomercustomerid);
        }
        //TBSystemUserManagerId
        $searchTbsystemusermanagerid = $searchParam->getTbsystemusermanagerid();
        if(isset($searchTbsystemusermanagerid) && $form['search']['TBSystemUserManagerId']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('p.TBSystemUserManagerId = :Tbsystemusermanagerid')
                ->setParameter('Tbsystemusermanagerid', $searchTbsystemusermanagerid);
        }

        //全件数取得
        $queryBuilderCount = clone $queryBuilder;
        $queryBuilderCount = $queryBuilderCount->select('count(p.id)');
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
                default:
                    $sortColumn = $pager->getColumn($pageSortName);
                    $queryBuilder = $queryBuilder->orderBy('p.'.$sortColumn['db_column_name'], $pageSortType);
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

        //returnURL生成
        $returnUrlQueryDataArray = $pager->getAllFormQueryStrings();
        $returnUrlQueryString = urlencode(http_build_query($returnUrlQueryDataArray));

        return array(
            'form' => $formView,
            'pager' => $pager->createView(),
            'entities' => $entities,
            'returnUrlParam' => $returnUrlQueryString,
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

        $formModel = new TBProjectMaster();
        $formType = new TBProjectMasterType();
        $subFormModel = new SubFormModel();
        $subFormType = new SubFormType();
        $subFormModel->setReturnAddress($request->get('ret'));

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder =$formFactory->createBuilder();
        $mainFormBuilder = $formFactory->createBuilder($formType, $formModel);
        $subFormBuilder = $formFactory->createBuilder($subFormType, $subFormModel);
        $form = $builder->add($mainFormBuilder)
            ->add($subFormBuilder)
            ->getForm();

        if($request->isMethod('POST'))
        {
            $form->bind($request);

            //button_action
            $buttonAction = $subFormModel->getButtonAction();

            if($buttonAction == "confirm" || $buttonAction == "submit")
            {
                if($form->isValid())
                {
                    if($buttonAction == "confirm")
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

                    }else if($buttonAction == "submit")
                    {
                        //登録実行
                        try{
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($formModel);
                            $em->flush();

                        }catch (\Exception $e){
                            throw $e;
                        }
                            return $this->redirect($this->generateUrl('project_show', array('id' => $formModel->getId(), 'ret' => $subFormModel->getReturnAddress())));
                    }
                }
            }else
            {
                //ドロップダウンなどのポストバック
                $builder->setAttribute('novalidation', true);
                $form = $builder->getForm();
            }
        }


        return array(
            'mode' => "input",
            'validate' => false,
            'entity' => $formModel,
            'form' => $form->createView(),
            'returnUrlParam' => urldecode($subFormModel->getReturnAddress()),
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

        $entity = $em->getRepository('ArtePCMSPublicBundle:TBProjectMaster')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TBProjectMaster entity.');
        }

        //returnUrlデコード
        $returnUrlQueryString = urldecode($request->get('ret'));

        return array(
            'entity'      => $entity,
            'returnUrlParam' => $returnUrlQueryString,
        );
    }

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

        $formType = new TBProjectMasterType();
        $subFormModel = new SubFormModel();
        $subFormType = new SubFormType();
        $subFormModel->setReturnAddress($request->get('ret'));

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ArtePCMSPublicBundle:TBProjectMaster')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SystemUser entity.');
        }

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder =$formFactory->createBuilder();
        $mainFormBuilder = $formFactory->createBuilder($formType, $entity);
        $subFormBuilder = $formFactory->createBuilder($subFormType, $subFormModel);
        $form = $builder->add($mainFormBuilder)
            ->add($subFormBuilder)
            ->getForm();

        if($request->isMethod('POST'))
        {
            $form->bind($request);

            //button_action
            $buttonAction = $subFormModel->getButtonAction();

            if($buttonAction == "confirm" || $buttonAction == "submit")
            {
                if($form->isValid())
                {
                    if($buttonAction == "confirm")
                    {
                        //確認画面
                        $builder->setAttribute('freeze', true);
                        $confirmForm = $builder->getForm();

                        return array(
                            'mode' => "confirm",
                            'entity' => $entity,
                            'form' => $confirmForm->createView(),
                            'returnUrlParam' => urldecode($subFormModel->getReturnAddress()),
                        );

                    }else if($buttonAction == "submit")
                    {
                        //登録実行
                        try{
                            $em->persist($entity);
                            $em->flush();

                        }catch (\Exception $e){
                            throw $e;
                        }
                        return $this->redirect($this->generateUrl('project_show', array('id' => $entity->getId(), 'ret' => $subFormModel->getReturnAddress())));
                    }
                }
            }else
            {
                //ドロップダウンなどのポストバック
                $builder->setAttribute('novalidation', true);
                $form = $builder->getForm();
            }
        }

        return array(
            'mode' => "input",
            'validate' => false,
            'entity' => $entity,
            'form' => $form->createView(),
            'returnUrlParam' => urldecode($subFormModel->getReturnAddress()),
        );
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
