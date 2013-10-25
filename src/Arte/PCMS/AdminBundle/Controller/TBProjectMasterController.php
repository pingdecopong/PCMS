<?php

namespace Arte\PCMS\AdminBundle\Controller;

use Arte\PCMS\AdminBundle\Form\TBProjectMasterFormModel;
use Arte\PCMS\AdminBundle\Form\TBProjectMasterFormType;
use Arte\PCMS\AdminBundle\Form\TBProjectMasterSearchType;


use Arte\PCMS\AdminBundle\Form\TBProjectMasterSubFormModel;
use Arte\PCMS\BizlogicBundle\Entity\TBProjectUser;
use Arte\PCMS\BizlogicBundle\Entity\TBSystemUser;
use Arte\PCMS\BizlogicBundle\Lib\ProjectManager;
use Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProject;
use Arte\PCMS\BizlogicBundle\Lib\ProjectManagerProjectCost;
use Arte\PCMS\BizlogicBundle\Lib\SystemUserManager;
//use Arte\PCMS\PublicBundle\Form\TestFormModel;
//use Arte\PCMS\PublicBundle\Form\TestFormType;
//use Arte\PCMS\PublicBundle\Form\TestMainFormModel;
//use Arte\PCMS\PublicBundle\Form\TestMainFormType;
//use Arte\PCMS\PublicBundle\Form\TestSubFormModel;
use Arte\PCMS\AdminBundle\Form\WorkerEdit2FormModel;
use Arte\PCMS\AdminBundle\Form\WorkerEdit2FormType;
use Arte\PCMS\AdminBundle\Form\WorkerEditFormModel;
use Arte\PCMS\AdminBundle\Form\WorkerEditFormType;
use Arte\PCMS\AdminBundle\Form\WorkerEditRoleFormModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\AST\Join;
//use Doctrine\ORM\Query\ResultSetMapping;
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
//use Arte\PCMS\PublicBundle\Form\TBProjectMasterSearchType;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;


//use Arte\PCMS\AdminBundle\Form\TBProjectMasterSearchType;



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
                'db_column_name' => 'CustomerName',
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
                'db_column_name' => 'ManagerName',
            ))
            ->addColumn('st', array(
                'label' => '状態',
                'sort_enable' => true,
                'db_column_name' => 'Status',
            ))
            ->addColumn('et', array(
                'label' => '見積工数',
                'sort_enable' => true,
                'db_column_name' => 'ProjectTotalCost',
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

        //検索
        $data = $form->getData();
        /** @var $searchParam \Arte\PCMS\PublicBundle\Form\TBProjectMasterSearchModel */
        $searchParam = $data['search'];

        //ソート
        $pageSortName = $pager->getSortName();
        $pageSortType = $pager->getSortType();

        //ページング
        $pageSize = $pager->getPageSize();
        $pageNo = $pager->getPageNo();

        //権限チェック
//        /** @var \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser $tbSystemUser */

        //
        $em = $this->getDoctrine()->getManager();
        $projectManager = new ProjectManager($em);
        $sortColumn = $pager->getColumn($pageSortName);
        $projects = $projectManager->getProjectList($pageNo, $pageSize, $sortColumn['db_column_name'], $pageSortType, $searchParam);
//        if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
//            $projects = $projectManager->getProjectList($pageNo, $pageSize, $sortColumn['db_column_name'], $pageSortType, $searchParam);
//        }else{
//            $projects = $projectManager->getProjectList($pageNo, $pageSize, $sortColumn['db_column_name'], $pageSortType, $searchParam, $this->getUser());
//        }
        $pager->setAllCount($projects['count']);
        if($pager->getMaxPageNum() < $pageNo){
            return $this->redirect($request->get('_route'));
        }
        $entities = $projects['result'];

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

        $formModel = new TBProjectMasterFormModel();
        $formType = new TBProjectMasterFormType();
        $subFormModel = new SubFormModel();
        $subFormType = new SubFormType();
        $subFormModel->setReturnAddress($request->get('ret'));

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder =$formFactory->createBuilder('form', null, array('cascade_validation' => true));
        $mainFormBuilder = $formFactory->createBuilder($formType, $formModel);
        $subFormBuilder = $formFactory->createBuilder($subFormType, $subFormModel);

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

            if($value->get('Delete')->isClicked())
            {
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
     * @Route("/show/{id}", name="project_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $projectManager = new ProjectManager($em);

        $project = $projectManager->readProject($id);
        $projectUsers = $projectManager->getProjectUsers($id);

        //権限チェック
        $systemRoleAdmin = false;
        $projectRole = 0;
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $systemRoleAdmin = true;
        }else{
            foreach($projectUsers as $value)
            {
                /** @var $value \Arte\PCMS\BizlogicBundle\Entity\TBProjectUser */
                if($value->getTBSystemUserSystemUserId() == $this->getUser()){
                    $projectRole = $value->getRoleNo();
                }
            }
        }
        if($systemRoleAdmin === false &&  $projectRole == 0){
            throw new \Exception("権限がありません");
        }

        //returnUrlデコード
        $returnUrlQueryString = urldecode($request->get('ret'));

        return array(
            'SystemRoleAdmin' => $systemRoleAdmin,
            'ProjectRole' => $projectRole,
            'project' => $project,
            'users' => $projectUsers,
//            'entity'      => $entity,
//            'ventity'     => $vprojectEntity,
//            'pcmentities'   => $pcmEntities,
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

        $formType = new TBProjectMasterFormType();
//        $formType = new TBProjectMasterType();
        $subFormModel = new SubFormModel();
        $subFormType = new SubFormType();
        $subFormModel->setReturnAddress($request->get('ret'));

        $em = $this->getDoctrine()->getManager();
        $projectManager = new ProjectManager($em);
        $project = $projectManager->readProject($id);
        $projectUsers = $projectManager->getProjectUsers($id);

        //権限チェック
        $systemRoleAdmin = false;
        $projectRole = 0;
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $systemRoleAdmin = true;
        }else{
            foreach($projectUsers as $value)
            {
                /** @var $value \Arte\PCMS\BizlogicBundle\Entity\TBProjectUser */
                if($value->getTBSystemUserSystemUserId() == $this->getUser()){
                    $projectRole = $value->getRoleNo();
                }
            }
        }
        if($systemRoleAdmin === false && $projectRole != 1){
            throw new \Exception("権限がありません");
        }

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
//        $entity = $em->getRepository('ArtePCMSPublicBundle:TBProjectMaster')->find($id);
        $projectManager = new ProjectManager($em);
        $project = $projectManager->readProject($id);
        $projectUsers = $projectManager->getProjectUsers($id);


        //権限チェック
        $systemRoleAdmin = false;
        $projectRole = 0;
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $systemRoleAdmin = true;
        }else{
            foreach($projectUsers as $value)
            {
                /** @var $value \Arte\PCMS\BizlogicBundle\Entity\TBProjectUser */
                if($value->getTBSystemUserSystemUserId() == $this->getUser()){
                    $projectRole = $value->getRoleNo();
                }
            }
        }
        if($systemRoleAdmin === false && $projectRole != 1){
            throw new \Exception("権限がありません");
        }

//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find TBProjectMaster entity.');
//        }

        if($request->isMethod('POST'))
        {
            $form->bind($request);
            if ($form->isValid()) {

                try{

                    $projectManager->deleteProject($project);
//                    $em->remove($entity);
//                    $em->flush();

                }catch (\Exception $e){
                    throw $e;
                }
                $data = $form->getData();
                //returnUrlデコード
                $returnUrlQueryString = urldecode($data['returnAddress']);
                return $this->redirect($this->generateUrl('project').'?'.$returnUrlQueryString);
            }
        }

        return array(
            'project' => $project,
//            'entity' => $entity,
            'form' => $form->createView(),
            'returnUrlParam' => $returnUrlQueryString,
        );
    }

    /**
     * 作業者編集
     *
     * @Route("/worker/edit/{id}", name="project_worker_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function workerEditAction($id)
    {
        $request = $this->getRequest();
        /* @var $formFactory \Symfony\Component\Form\FormFactory */
        $formFactory = $this->get('form.factory');

        $formType = new WorkerEditFormType();
        $formModel = new WorkerEditFormModel();
        $formType2 = new WorkerEdit2FormType();
        $formModel2 = new WorkerEdit2FormModel();

        $em = $this->getDoctrine()->getManager();
        //ユーザー一覧
//        $userManager = new SystemUserManager($em);
        /** @var \Arte\PCMS\BizlogicBundle\Lib\SystemUserManager $systemUserManager */
        $userManager = $this->get('Arte.PCMS.Lib.SystemUserManager');
        $users = $userManager->getTBSystemUsersForDropDownList();

        $choices = array();
        foreach($users as $value)
        {
            /** @var $value TBSystemUser */
            $choices[$value->getId()] = $value->getDisplayName();
        }
        $formType->setChoices($choices);

        //所属ユーザー取得
        $projectManager = new ProjectManager($em);
        $projectUsers = $projectManager->getProjectUsers($id);
        $selected = array();
        foreach($projectUsers as $value)
        {
            /** @var $value TBProjectUser */
            $selected[] = $value->getSystemUserId();
        }
        $formModel->setUsers($selected);

        //権限チェック
        $systemRoleAdmin = false;
        $projectRole = 0;
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $systemRoleAdmin = true;
        }else{
            foreach($projectUsers as $value)
            {
                /** @var $value \Arte\PCMS\BizlogicBundle\Entity\TBProjectUser */
                if($value->getTBSystemUserSystemUserId() == $this->getUser()){
                    $projectRole = $value->getRoleNo();
                }
            }
        }
        if($systemRoleAdmin === false && $projectRole != 1){
            throw new \Exception("権限がありません");
        }

        $form = $this->createWorkerForm($formType, $formModel, $formType2, $formModel2, null, array(2));
//        $form->get('return')->setData($request->get('ret'));

        if($request->isMethod('POST'))
        {
            $form->submit($request);

            //page1to2
            if($form->get('NextTo2')->isClicked())//ページ１からページ２へ
            {
                $form = $this->createWorkerForm($formType, $formModel, $formType2, $formModel2, array(1), array(2));
                $form->submit($request);

                $templateName = 'ArtePCMSAdminBundle:TBProjectMaster:workeredit.html.twig';
                if($form->isValid()){
                    $templateName = 'ArtePCMSAdminBundle:TBProjectMaster:workeredit2.html.twig';

                    $formModel2->getWorkerEditRoleForms()->clear();
                    foreach($formModel->getUsers() as $value)
                    {
                        $role = new WorkerEditRoleFormModel();
                        $role->setId($value);

                        //すでに権限が設定されてある場合は、値を設定する
                        foreach($projectUsers as $projectUser)
                        {
                            /** @var $projectUser TBProjectUser */
                            if($projectUser->getSystemUserId() == $value){
                                $role->setRole($projectUser->getRoleNo());
                            }
                        }

                        $formModel2->addWorkerEditRoleForm($role);
                    }
                    $form = $this->createWorkerForm($formType, $formModel, $formType2, $formModel2, array(1), array(1));
//                    $form->submit($request);
                }

                return $this->render($templateName, array(
                    'entities' => $users,
                    'mode' => "input",
                    'ID' => $id,
                    'form' => $form->createView(),
                    'returnUrlParam' => "",
                ));

            }else if($form->get('Confirm')->isClicked())//ページ２から確認画面へ
            {
                $form = $this->createWorkerForm($formType, $formModel, $formType2, $formModel2, array(1, 2), array(1));
                $form->submit($request);

                $templateName = 'ArtePCMSAdminBundle:TBProjectMaster:workeredit2.html.twig';
                $mode = 'input';
                if($form->isValid()){
                    $templateName = 'ArtePCMSAdminBundle:TBProjectMaster:workereditcfm.html.twig';
                    $mode = 'confirm';

                    $form = $this->createWorkerForm($formType, $formModel, $formType2, $formModel2, array(1, 2), array(1, 2));

                }

                return $this->render($templateName, array(
                    'entities' => $users,
                    'mode' => $mode,
                    'ID' => $id,
                    'form' => $form->createView(),
                    'returnUrlParam' => "",
                ));

            }else if($form->get('ReturnTo1')->isClicked())//ページ２からページ１へ
            {
                $form = $this->createWorkerForm($formType, $formModel, $formType2, $formModel2, null, array(2));
                $form->submit($request);

                return $this->render('ArtePCMSAdminBundle:TBProjectMaster:workeredit.html.twig', array(
                    'entities' => $users,
                    'mode' => "input",
                    'ID' => $id,
                    'form' => $form->createView(),
                    'returnUrlParam' => "",
                ));
            }else if($form->get('Submit')->isClicked())//確認画面から登録へ
            {
                $form = $this->createWorkerForm($formType, $formModel, $formType2, $formModel2, array(1, 2), array(2));
                $form->submit($request);

                if($form->isValid()){
                    //登録実行
                    $setData = array();
                    foreach($formModel2->getWorkerEditRoleForms() as $roleModel)
                    {
                        $data = array();
                        /** @var $roleModel WorkerEditRoleFormModel */
                        foreach($users as $value)
                        {
                            /** @var $value TBSystemUser */
                            if($roleModel->getId() == $value->getId()){
                                $data['user'] = $value;
                                $data['role'] = $roleModel->getRole();
                                break;
                            }
                        }
                        $setData[] = $data;
                    }
                    $projectManager->createProjectUsers($id, $setData);

                    return $this->redirect($this->generateUrl('project_show', array('id' => $id, 'ret' => "")));
//                    return $this->redirect($this->generateUrl('project_show', array('id' => $id, 'ret' => $subFormModel->getReturnAddress())));
                }

                return $this->render('ArtePCMSAdminBundle:TBProjectMaster:workeredit.html.twig', array(
                    'entities' => $users,
                    'mode' => "input",
                    'ID' => $id,
                    'form' => $form->createView(),
                    'returnUrlParam' => "",
                ));

            }else if($form->get('ReturnTo2')->isClicked())//確認画面からページ２へ
            {
                $form = $this->createWorkerForm($formType, $formModel, $formType2, $formModel2, null, array(1));
                $form->submit($request);

                return $this->render('ArtePCMSAdminBundle:TBProjectMaster:workeredit2.html.twig', array(
                    'entities' => $users,
                    'mode' => "input",
                    'ID' => $id,
                    'form' => $form->createView(),
                    'returnUrlParam' => "",
                ));
            }

        }

        return array(
            'entities' => $users,
            'mode' => "input",
            'validate' => false,
            'ID' => $id,
            'form' => $form->createView(),
            'returnUrlParam' => "",
//            'returnUrlParam' => urldecode($form->get('return')->getData()),
        );
    }

    private function createWorkerForm($formType1, $formModel1, $formType2, $formModel2, $validationGroups = null, $freezeArray = null)
    {
        /* @var $formFactory \Symfony\Component\Form\FormFactory */
        $formFactory = $this->get('form.factory');

        $setValidationGroups = array();
        if(is_array($validationGroups)){
            foreach($validationGroups as $value)
            {
                switch($value){
                    case 1:
                        $setValidationGroups[] = 'WorkerEditForm';
                        break;
                    case 2:
                        $setValidationGroups[] = 'WorkerEdit2Form';
                        break;
                }
            }
        }

        $setFreeze1 = false;
        $setFreeze2 = false;
        if(is_array($freezeArray)){
            foreach($freezeArray as $value)
            {
                switch($value){
                    case 1:
                        $setFreeze1 = true;
                        break;
                    case 2:
                        $setFreeze2 = true;
                        break;
                }
            }
        }



        $option = array('cascade_validation' => true);
        if($validationGroups != null)
        {
            $option['validation_groups'] = $setValidationGroups;
        }

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder =$formFactory->createBuilder('form', null, $option);
//        $builder =$formFactory->createBuilder('form', null);
        $mainFormBuilder = $formFactory->createBuilder($formType1, $formModel1, array('freeze' => $setFreeze1));
        $main2FormBuilder = $formFactory->createBuilder($formType2, $formModel2, array('freeze' => $setFreeze2));

        /* @var $form \Symfony\Component\Form\Form */
        $form = $builder->add($mainFormBuilder)
            ->add($main2FormBuilder)
            ->add('NextTo2', 'submit', array(
                'label' => '次へ',
            ))
            ->add('ReturnTo1', 'submit', array(
                'label' => '戻る'
            ))
            ->add('Confirm', 'submit', array(
                'label' => '確認'
            ))
            ->add('ReturnTo2', 'submit', array(
                'label' => '戻る'
            ))
            ->add('Submit', 'submit', array(
                'label' => '登録'
            ))
            ->add('return', 'hidden', array(
            ))
            ->getForm();
//        $main2FormBuilder->setAttribute('freeze', true);
//        $form = $builder->getForm();

        return $form;
    }

}
