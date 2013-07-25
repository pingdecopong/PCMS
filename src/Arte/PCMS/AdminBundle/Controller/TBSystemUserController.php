<?php

namespace Arte\PCMS\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use pingdecopong\PagerBundle\Pager\Pager;
use pingdecopong\PDPGeneratorBundle\Lib\SubFormModel;
use pingdecopong\PDPGeneratorBundle\Lib\SubFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Arte\PCMS\AdminBundle\Entity\TBSystemUser;
use Arte\PCMS\AdminBundle\Form\TBSystemUserType;
use Arte\PCMS\AdminBundle\Form\TBSystemUserSearchType;

/**
 * TBSystemUser controller.
 *
 * @Route("/systemuser")
 */
class TBSystemUserController extends Controller
{

    /**
     * Lists all TBSystemUser entities.
     *
     * @Route("/", name="systemuser")
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
            ->addColumn('1', array(
                'label' => 'ログインID',
                'sort_enable' => true,
                'db_column_name' => 'LoginId',
            ))
            ->addColumn('2', array(
                'label' => '状態',
                'sort_enable' => true,
                'db_column_name' => 'Active',
            ))
            ->addColumn('3', array(
                'label' => '名前',
                'sort_enable' => true,
                'db_column_name' => 'DisplayName',
            ))
            ->addColumn('4', array(
                'label' => 'メール',
                'sort_enable' => true,
                'db_column_name' => 'MailAddress',
            ))
            ->addColumn('5', array(
                'label' => '部署名',
                'sort_enable' => true,
            ))
            ->addColumn('6', array(
                'label' => '最終ログイン',
                'sort_enable' => true,
                'db_column_name' => 'LastLoginDatetime',
            ))
        ;

        /* @var $form \Symfony\Component\Form\Form */
        $form = $formFactory->createNamedBuilder('f', 'form', null, array('csrf_protection' => false))
            ->add($pager->getFormBuilder())
            ->add('search', new TBSystemUserSearchType())
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
            ->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')
            ->createQueryBuilder('u')
            ->leftJoin('u.TBDepartmentDepartmentId', 'd')
            ->select(array('u', 'd'))
            ->andWhere('u.DeleteFlag = :DeleteFlag')
            ->setParameter('DeleteFlag', false);

        //検索
        $data = $form->getData();
        /** @var $searchParam TBSystemUser */
        $searchParam = $data['search'];
        //LoginId
        $searchLoginid = $searchParam->getLoginid();
        if(isset($searchLoginid) && $form['search']['LoginId']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('u.LoginId LIKE :Loginid')
                ->setParameter('Loginid', '%'.$searchLoginid.'%');
        }
        //Active
        $searchActive = $searchParam->getActive();
        if(isset($searchActive) && $form['search']['Active']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('u.Active LIKE :Active')
                ->setParameter('Active', '%'.$searchActive.'%');
        }
        //DisplayName
        $searchDisplayname = $searchParam->getDisplayname();
        if(isset($searchDisplayname) && $form['search']['DisplayName']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('u.DisplayName LIKE :Displayname')
                ->setParameter('Displayname', '%'.$searchDisplayname.'%');
        }
        //MailAddress
        $searchMailaddress = $searchParam->getMailaddress();
        if(isset($searchMailaddress) && $form['search']['MailAddress']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('u.MailAddress LIKE :Mailaddress')
                ->setParameter('Mailaddress', '%'.$searchMailaddress.'%');
        }

        //relation 検索
        //TBDepartmentDepartmentId
        $searchTbdepartmentdepartmentid = $searchParam->getTbdepartmentdepartmentid();
        if(isset($searchTbdepartmentdepartmentid) && $form['search']['TBDepartmentDepartmentId']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('u.TBDepartmentDepartmentId = :Tbdepartmentdepartmentid')
                ->setParameter('Tbdepartmentdepartmentid', $searchTbdepartmentdepartmentid);
        }

        //全件数取得
        $queryBuilderCount = clone $queryBuilder;
        $queryBuilderCount = $queryBuilderCount->select('count(u.id)');
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
                case '5':
                    $queryBuilder = $queryBuilder->orderBy('d.Name', $pageSortType);
                    break;
                default:
                    $sortColumn = $pager->getColumn($pageSortName);
                    $queryBuilder = $queryBuilder->orderBy('u.'.$sortColumn['db_column_name'], $pageSortType);
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
     * TBSystemUser新規作成
     *
     * @Route("/new", name="systemuser_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction()
    {

        $request = $this->getRequest();
        $formFactory = $this->get('form.factory');

        $formModel = new TBSystemUser();
        $formType = new TBSystemUserType();
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
                            return $this->redirect($this->generateUrl('systemuser_show', array('id' => $formModel->getId(), 'ret' => $subFormModel->getReturnAddress())));
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
     * TBSystemUser詳細
     *
     * @Route("/{id}", name="systemuser_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ArtePCMSAdminBundle:TBSystemUser')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TBSystemUser entity.');
        }

        //returnUrlデコード
        $returnUrlQueryString = urldecode($request->get('ret'));

        return array(
            'entity'      => $entity,
            'returnUrlParam' => $returnUrlQueryString,
        );
    }

    /**
     * TBSystemUser編集
     *
     * @Route("/edit/{id}", name="systemuser_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($id)
    {

        $request = $this->getRequest();
        $formFactory = $this->get('form.factory');

        $formType = new TBSystemUserType();
        $subFormModel = new SubFormModel();
        $subFormType = new SubFormType();
        $subFormModel->setReturnAddress($request->get('ret'));

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ArtePCMSAdminBundle:TBSystemUser')->find($id);
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
                        return $this->redirect($this->generateUrl('systemuser_show', array('id' => $entity->getId(), 'ret' => $subFormModel->getReturnAddress())));
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
     * TBSystemUser削除
     *
     * @Route("/delete/{id}", name="systemuser_delete")
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
        $entity = $em->getRepository('ArtePCMSAdminBundle:TBSystemUser')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TBSystemUser entity.');
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
