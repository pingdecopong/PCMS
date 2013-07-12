<?php

namespace Arte\PCMS\AdminBundle\Controller;

use Arte\PCMS\AdminBundle\Form\SystemUser\ListSearchFormType;
use pingdecopong\PagerBundle\Pager\Pager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * ユーザー管理　コントローラ
 * @Route("/systemuser")
 */
class SystemUserController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->redirect('list');
    }

    /**
     * ユーザ－一覧
     * @Route("/list", name="list")
     * @Template()
     * @Method({"GET", "POST"})
     */
    public function listAction(Request $request)
    {
        $formFactory = $this->get('form.factory');
        $validator = $this->get('validator');
        $pager = new Pager($formFactory, $validator);

        //pager setting
        $pager
            ->addColumn('id', array(
                'label' => 'ID',
                'sort_enable' => true,
                'db_column_name' => 'id',
            ))
            ->addColumn('a', array(
                'label' => '状態',
                'sort_enable' => true,
                'db_column_name' => 'Active',
            ))
            ->addColumn('lid', array(
                'label' => 'ログインID',
                'sort_enable' => true,
                'db_column_name' => 'LoginId',
            ))
            ->addColumn('dn', array(
                'label' => '名前',
                'sort_enable' => true,
                'db_column_name' => 'DisplayName',
            ))
//            ->addColumn('dnk', array(
//                'label' => '名前カナ',
//                'sort_enable' => true,
//                'db_column_name' => 'DisplayNameKana',
//            ))
//            ->addColumn('nn', array(
//                'label' => '略称',
//                'sort_enable' => true,
//                'db_column_name' => 'NickName',
//            ))
            ->addColumn('ma', array(
                'label' => 'メール',
                'sort_enable' => true,
                'db_column_name' => 'MailAddress',
            ))
            ->addColumn('did', array(
                'label' => '部署名',
                'sort_enable' => true,
            ))
            ->addColumn('lld', array(
                'label' => '最終ログイン',
                'sort_enable' => true,
                'db_column_name' => 'LastLoginDatetime',
            ))
//            ->addColumn('cd', array(
//                'label' => '登録日',
//                'sort_enable' => true,
//                'db_column_name' => 'CreatedDatetime',
//            ))
        ;

        $form = $formFactory->createNamedBuilder('f', 'form', null, array('csrf_protection' => false))
            ->add($pager->getFormBuilder())
            ->add('search', new ListSearchFormType())
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
        $queryBuilder = $this->getDoctrine()
            ->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')
            ->createQueryBuilder('u')
            ->leftJoin('u.tbdepartment', 'd')
            ->select(array('u', 'd'))
            ->andWhere('u.DeleteFlug = :DeleteFlug')
            ->setParameter('DeleteFlug', false);

        //検索
        $data = $form->getData();
        //ログインID
        $searchLoginId = $data['search']->getLoginId();
        if(isset($searchLoginId) && $form['search']['LoginId']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('u.LoginId LIKE :LoginId')
                ->setParameter('LoginId', '%'.$searchLoginId.'%');
        }
        //名前
        $searchDisplayName = $data['search']->getDisplayName();
        if(isset($searchDisplayName) && $form['search']['DisplayName']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('u.DisplayName LIKE :DisplayName')
                ->setParameter('DisplayName', '%'.$searchDisplayName.'%');
        }
        //メール
        $searchMailAddress = $data['search']->getMailAddress();
        if(isset($searchMailAddress) && $form['search']['DisplayName']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('u.MailAddress LIKE :MailAddress')
                ->setParameter('MailAddress', '%'.$searchMailAddress.'%');
        }
        //部署
        $searchDepartment = $data['search']->getDepartment();
        if(isset($searchDepartment) && $form['search']['Department']->isValid())
        {
            $queryBuilder = $queryBuilder->andWhere('u.tbdepartment = :Department')
                ->setParameter('Department', $searchDepartment);
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
                case 'did':
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

        return array(
            'form' => $formView,
            'pager' => $pager->createView(),
            'entities' => $entities,
        );
    }

    /**
     * @Route("/create", name="create")
     * @Template()
     */
    public function createAction(Request $request)
    {
        
    }

}
