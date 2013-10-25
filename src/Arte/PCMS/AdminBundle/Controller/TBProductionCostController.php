<?php
namespace Arte\PCMS\AdminBundle\Controller;


use Arte\PCMS\AdminBundle\Form\TBProductionCostModel;
use Arte\PCMS\AdminBundle\Form\TBProductionCostSearchType;
use Arte\PCMS\AdminBundle\Form\TBProductionCostType;
use Arte\PCMS\BizlogicBundle\Lib\ProjectManager;
use Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProductionCost;
use Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost;
//use Arte\PCMS\PublicBundle\Form\TBProductionCostModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use pingdecopong\PagerBundle\Pager\Pager;
use pingdecopong\PDPGeneratorBundle\Lib\SubFormModel;
use pingdecopong\PDPGeneratorBundle\Lib\SubFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
//use Arte\PCMS\PublicBundle\Entity\TBProductionCost;
//use Arte\PCMS\PublicBundle\Form\TBProductionCostType;
//use Arte\PCMS\PublicBundle\Form\TBProductionCostSearchType;

/**
 * TBProductionCost controller.
 *
 * @Route("/cost")
 */
class TBProductionCostController extends Controller
{

    /**
     * Lists all TBProductionCost entities.
     *
     * @Route("/", name="cost")
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
        //
        //案件名
        //作業項目名
        //状態
        //作業日時
        //作業工数
        $pager
            ->addColumn('pn', array(
                'label' => '案件名',
                'sort_enable' => true,
                'db_column_name' => 'TBProjectMaster',
            ))
            ->addColumn('cn', array(
                'label' => '作業項目名',
                'sort_enable' => true,
                'db_column_name' => 'ProjectCostName',
            ))
            ->addColumn('s', array(
                'label' => '状態',
                'sort_enable' => true,
                'db_column_name' => 'Status',
            ))
            ->addColumn('wd', array(
                'label' => '作業日',
                'sort_enable' => true,
                'db_column_name' => 'WorkDate',
            ))
            ->addColumn('u', array(
                'label' => '作業者',
                'sort_enable' => true,
                'db_column_name' => 'TBSystemUser',
            ))
            ->addColumn('c', array(
                'label' => '作業工数',
                'sort_enable' => true,
                'db_column_name' => 'Cost',
            ))
        ;

        /* @var $form \Symfony\Component\Form\Form */
        $form = $formFactory->createNamedBuilder('f', 'form', null, array('csrf_protection' => false))
            ->add($pager->getFormBuilder())
            ->add('search', new TBProductionCostSearchType())
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
            $a = $this->generateUrl($request->get('_route'), $queryAllData);
            return $this->redirect($this->generateUrl($request->get('_route'), $queryAllData));
        }

        if(($request->isMethod('GET') && !$form->isValid()) || !$pager->isValid())
        {
            return $this->redirect($this->generateUrl($request->get('_route')));
        }

        //検索
        $data = $form->getData();
        /** @var $searchParam \Arte\PCMS\BizlogicBundle\Lib\TBProductionCostSearchModel */
        $searchParam = $data['search'];

        //ソート
        $pageSortName = $pager->getSortName();
        $pageSortType = $pager->getSortType();

        //ページング
        $pageSize = $pager->getPageSize();
        $pageNo = $pager->getPageNo();

        //
        $em = $this->getDoctrine()->getManager();
        $projectManager = new ProjectManager($em);
        $sortColumn = $pager->getColumn($pageSortName);
        $costs = $projectManager->getCostList($pageNo, $pageSize, $sortColumn['db_column_name'], $pageSortType, $searchParam);
//        if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
//            $costs = $projectManager->getCostList($pageNo, $pageSize, $sortColumn['db_column_name'], $pageSortType, $searchParam);
//        }else{
//            $costs = $projectManager->getCostList($pageNo, $pageSize, $sortColumn['db_column_name'], $pageSortType, $searchParam);
//            $projects = $projectManager->getProjectList($pageNo, $pageSize, $sortColumn['db_column_name'], $pageSortType, $searchParam, $this->getUser());
//        }

        $pager->setAllCount($costs['count']);
        if($pager->getMaxPageNum() < $pageNo){
            return $this->redirect($request->get('_route'));
        }
        $entities = $costs['result'];

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
     * TBProductionCost新規作成
     *
     * @Route("/new", name="cost_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction()
    {

        $request = $this->getRequest();
//        $formFactory = $this->get('form.factory');
        $returnParam = $request->get('ret');

        $formModel = new TBProductionCostModel();
        $formType = new TBProductionCostType();

        $formBuilder = $this->createProductionCostForm($formType, $formModel);
        /* @var $form \Symfony\Component\Form\Form */
        $form = $formBuilder->setAction($this->generateUrl($request->get('_route')))
            ->setMethod('POST')
            ->getForm();
        $form->get('ret')->setData($returnParam);

        if($request->isMethod('POST'))
        {
            $form->submit($request);
            $returnParam = $form->get('ret')->getData();

            if($form->get('Confirm')->isClicked() || $form->get('Submit')->isClicked())
            {
                if($form->isValid())
                {
                    if($form->get('Confirm')->isClicked())
                    {
                        //確認画面
                        $formBuilder = $this->createProductionCostForm($formType, $formModel, true, true);
                        /* @var $form \Symfony\Component\Form\Form */
                        $form = $formBuilder->setAction($this->generateUrl($request->get('_route')))
                            ->setMethod('POST')
                            ->getForm();
                        $form->submit($request);

                        return array(
                            'title' => '作業工数　登録確認',
                            'type' => 'new',
                            'mode' => "confirm",
                            'entity' => $formModel,
                            'form' => $form->createView(),
                        );

                    }else if($form->get('Submit')->isClicked())
                    {
                        //登録実行
                        try{
                            $em = $this->getDoctrine()->getManager();
                            $projectManager = new ProjectManager($em);

                            $projectManagerProjectCost = new ProjectManagerProjectCost();
                            $projectManagerProjectCost->setId($formModel->getTBProjectCost()->getId());

                            $productionCost = new ProjectManagerProductionCost();
                            $productionCost->setProjectCost($projectManagerProjectCost);
                            $productionCost->setWorker($formModel->getTBSystemUser());
                            $productionCost->setCost($formModel->getCost());
                            $productionCost->setWorkDate($formModel->getWorkDate());
                            $productionCost->setNote($formModel->getNote());
                            $cost = $projectManager->createProductionCost($productionCost);

                        }catch (\Exception $e){
                            throw $e;
                        }
                        return $this->redirect($this->generateUrl('cost_show', array('id' => $cost->getId(), 'ret' => $returnParam)));
                    }
                }
            }else
            {
                //ドロップダウンなどのポストバック
                $formBuilder = $this->createProductionCostForm($formType, $formModel, true);
                /* @var $form \Symfony\Component\Form\Form */
                $form = $formBuilder->setAction($this->generateUrl($request->get('_route')))
                    ->setMethod('POST')
                    ->getForm();
                $form->submit($request);
            }
        }

        return array(
            'title' => '作業工数　登録',
            'type' => 'new',
            'mode' => "input",
            'validate' => false,
            'entity' => $formModel,
            'form' => $form->createView(),
            'returnUrlParam' => urldecode($returnParam),
        );
    }

    private function createProductionCostForm($formType, $formModel, $noValidation = false, $freeze = false)
    {
        /* @var $formFactory \Symfony\Component\Form\FormFactory */
        $formFactory = $this->get('form.factory');

        $option = array();
        if($noValidation){
            $option['validation_groups'] = false;
        }
        if($freeze){
            $option['freeze'] = true;
        }
        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder =$formFactory->createBuilder('form', null, $option);
        $mainFormBuilder = $formFactory->createBuilder($formType, $formModel, $option);

        /* @var $form \Symfony\Component\Form\Form */
        $formBuilder = $builder->add($mainFormBuilder)
            ->add('Confirm', 'submit', array(
                'label' => '確認'
            ))
            ->add('Return', 'submit', array(
                'label' => '戻る'
            ))
            ->add('Submit', 'submit', array(
                'label' => '登録'
            ))
            ->add('ret', 'hidden', array(
            ));
//            ->getForm();

        return $formBuilder;
    }

    /**
     * TBProductionCost詳細
     *
     * @Route("/show/{id}", name="cost_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $projectManager = new ProjectManager($em);
        $entity = $projectManager->getProductionCost($id);

//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find TBProductionCost entity.');
//        }

        $tbProjectCostMaster = $projectManager->getProjectCost($entity->getProjectCost()->getId());

        //returnUrlデコード
        $returnUrlQueryString = urldecode($request->get('ret'));

        return array(
            'entity'      => $entity,
            'ProjectCost' => $tbProjectCostMaster,
            'returnUrlParam' => $returnUrlQueryString,
        );
    }

    /**
     * TBProductionCost編集
     *
     * @Route("/edit/{id}", name="cost_edit")
     * @Method({"GET", "POST"})
     * @Template("ArtePCMSAdminBundle:TBProductionCost:new.html.twig")
     */
    public function editAction($id)
    {
        $request = $this->getRequest();
        $returnParam = $request->get('ret');
        $em = $this->getDoctrine()->getManager();

        //
        $projectManager = new ProjectManager($em);
        $entity = $projectManager->getProductionCost($id);
        $tbProjectCostMaster = $projectManager->getProjectCost($entity->getProjectCost()->getId());

        $formModel = new TBProductionCostModel();
        $formModel->setTBProjectMaster($tbProjectCostMaster->getTBProjectMasterProjectMasterId());
        $formModel->setTBProjectCost($tbProjectCostMaster);
        $formModel->setTBSystemUser($entity->getWorker());
        $formModel->setWorkDate($entity->getWorkDate());
        $formModel->setCost($entity->getCost());
        $formModel->setNote($entity->getNote());
        $formType = new TBProductionCostType();

        $formBuilder = $this->createProductionCostForm($formType, $formModel);
        /* @var $form \Symfony\Component\Form\Form */
        $form = $formBuilder->setAction($this->generateUrl($request->get('_route'), array('id' => $id)))
            ->setMethod('POST')
            ->getForm();
        $form->get('ret')->setData($returnParam);

        if($request->isMethod('POST'))
        {
            $form->submit($request);
            $returnParam = $form->get('ret')->getData();

            if($form->get('Confirm')->isClicked() || $form->get('Submit')->isClicked())
            {
                if($form->isValid())
                {
                    if($form->get('Confirm')->isClicked())
                    {
                        //確認画面
                        $formBuilder = $this->createProductionCostForm($formType, $formModel, true, true);
                        /* @var $form \Symfony\Component\Form\Form */
                        $form = $formBuilder->setAction($this->generateUrl($request->get('_route'), array('id' => $id)))
                            ->setMethod('POST')
                            ->getForm();
                        $form->submit($request);

                        return array(
                            'title' => '作業工数　編集確認',
                            'type' => 'edit',
                            'mode' => "confirm",
                            'entity' => $formModel,
                            'form' => $form->createView(),
                        );

                    }else if($form->get('Submit')->isClicked())
                    {
                        //登録実行
                        try{
                            $em = $this->getDoctrine()->getManager();
                            $projectManager = new ProjectManager($em);

                            $projectManagerProjectCost = new ProjectManagerProjectCost();
                            $projectManagerProjectCost->setId($formModel->getTBProjectCost()->getId());

                            $productionCost = new ProjectManagerProductionCost();
                            $productionCost->setId($id);
                            $productionCost->setProjectCost($projectManagerProjectCost);
                            $productionCost->setWorker($formModel->getTBSystemUser());
                            $productionCost->setCost($formModel->getCost());
                            $productionCost->setWorkDate($formModel->getWorkDate());
                            $productionCost->setNote($formModel->getNote());
                            $cost = $projectManager->editProductionCost($productionCost);

                        }catch (\Exception $e){
                            throw $e;
                        }
                        return $this->redirect($this->generateUrl('cost_show', array('id' => $cost->getId(), 'ret' => $returnParam)));
                    }
                }
            }else
            {
                //ドロップダウンなどのポストバック
                $formBuilder = $this->createProductionCostForm($formType, $formModel, true);
                /* @var $form \Symfony\Component\Form\Form */
                $form = $formBuilder->setAction($this->generateUrl($request->get('_route'), array('id' => $id)))
                    ->setMethod('POST')
                    ->getForm();
                $form->submit($request);
            }
        }

        return array(
            'title' => '作業工数　編集',
            'type' => 'edit',
            'mode' => "input",
            'validate' => false,
            'entity' => $formModel,
            'form' => $form->createView(),
            'returnUrlParam' => urldecode($returnParam),
        );
    }

    /**
     * TBProductionCost削除
     *
     * @Route("/delete/{id}", name="cost_delete")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function deleteAction($id)
    {
        $request = $this->getRequest();
        $returnParam = $request->get('ret');
        $em = $this->getDoctrine()->getManager();

        //データ取得
        $projectManager = new ProjectManager($em);
        $entity = $projectManager->getProductionCost($id);
        $tbProjectCostMaster = $projectManager->getProjectCost($entity->getProjectCost()->getId());

        //フォーム作成
        $form = $this->createFormBuilder(array('ret' => urldecode($returnParam)))
            ->setAction($this->generateUrl($request->get('_route'), array('id' => $id)))
            ->setMethod('POST')
            ->add('ret', 'hidden')
            ->add('Submit', 'submit', array(
                'label' => '削除'
            ))
            ->getForm();

        if($request->isMethod('POST'))
        {
            $form->submit($request);
            $returnParam = $form->get('ret')->getData();

            if($form->get('Submit')->isClicked() && $form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $projectManager = new ProjectManager($em);

                $projectManager->deleteProductionCost($id);

                return $this->redirect($this->generateUrl('cost').'?'.urldecode($returnParam));
            }
        }

        return array(
            'entity'      => $entity,
            'ProjectCost' => $tbProjectCostMaster,
            'form' => $form->createView(),
            'returnUrlParam' => urldecode($returnParam),
        );
    }


}
