<?php

namespace Arte\PCMS\AdminBundle\Controller;

use Arte\PCMS\BizlogicBundle\Lib\SystemUserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use pingdecopong\PagerBundle\Pager\Pager;
use pingdecopong\PDPGeneratorBundle\Lib\SubFormModel;
use pingdecopong\PDPGeneratorBundle\Lib\SubFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Arte\PCMS\BizlogicBundle\Entity\TBSystemUser;
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
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('systemuser'));
    }

    /**
     * Lists all TBSystemUser entities.
     *
     * @Route("/list", name="systemuser")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function listAction()
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
                'db_column_name' => 'Username',
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
                'db_column_name' => 'TBDepartment',
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

        //
        $em = $this->getDoctrine()->getManager();
        /** @var \Arte\PCMS\BizlogicBundle\Lib\SystemUserManager $systemUserManager */
        $systemUserManager = $this->get('Arte.PCMS.Lib.SystemUserManager');
        $sortColumn = $pager->getColumn($pageSortName);
        $projects = $systemUserManager->getTBSystemUserList($pageNo, $pageSize, $sortColumn['db_column_name'], $pageSortType, $searchParam);
        $pager->setAllCount($projects['count']);
        if($pager->getMaxPageNum() < $pageNo){
            return $this->redirect($request->get('_route'));
        }
        $entities = $projects['result'];

        //returnURL生成
        $returnUrlQueryDataArray = $pager->getAllFormQueryStrings();
//        $returnUrlQueryString = urlencode(http_build_query($returnUrlQueryDataArray));

        return array(
            'form' => $formView,
            'pager' => $pager->createView(),
            'entities' => $entities,
//            'returnUrlParam' => $returnUrlQueryString,
            'returnUrlParam' => http_build_query($returnUrlQueryDataArray),
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
        $returnParam = $request->get('ret');

        $formModel = new TBSystemUser();
        $formType = new TBSystemUserType();

        $formBuilder = $this->createTBSystemUserForm($formType, $formModel, array('form', 'newform'));
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
                        $formBuilder = $this->createTBSystemUserForm($formType, $formModel, array('form', 'newform'), true);
                        /* @var $form \Symfony\Component\Form\Form */
                        $form = $formBuilder->setAction($this->generateUrl($request->get('_route')))
                            ->setMethod('POST')
                            ->getForm();
                        $form->submit($request);

                        return array(
                            'title' => 'ユーザー　登録確認',
                            'type' => 'new',
                            'mode' => "confirm",
                            'entity' => $formModel,
                            'form' => $form->createView(),
                        );

                    }else if($form->get('Submit')->isClicked())
                    {
                        //登録実行
                        /** @var \Arte\PCMS\BizlogicBundle\Lib\SystemUserManager $systemUserManager */
                        $systemUserManager = $this->get('Arte.PCMS.Lib.SystemUserManager');
                        $tbSystemUser = $systemUserManager->createSystemUser($formModel, $formModel->getTBDepartmentDepartmentId(), $formModel->getFormPassword());

                        return $this->redirect($this->generateUrl('systemuser_message', array('type' => 1,'id' => $tbSystemUser->getId(), 'ret' => $returnParam)));
                    }
                }
            }else
            {
                //ドロップダウンなどのポストバック
                $formBuilder = $this->createTBSystemUserForm($formType, $formModel, false);
                /* @var $form \Symfony\Component\Form\Form */
                $form = $formBuilder->setAction($this->generateUrl($request->get('_route')))
                    ->setMethod('POST')
                    ->getForm();
                $form->submit($request);
            }
        }

        return array(
            'title' => 'ユーザー　登録',
            'type' => 'new',
            'mode' => "input",
            'validate' => false,
            'entity' => $formModel,
            'form' => $form->createView(),
            'returnUrlParam' => urldecode($returnParam),
        );

    }

    private function createTBSystemUserForm($formType, $formModel, $validationGroups = null, $freeze = false)
    {
        /* @var $formFactory \Symfony\Component\Form\FormFactory */
        $formFactory = $this->get('form.factory');

        $option = array();
        if($validationGroups !== null){
            $option['validation_groups'] = $validationGroups;
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

        return $formBuilder;
    }

    /**
     * TBSystemUser詳細
     *
     * @Route("/show/{id}", name="systemuser_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $request = $this->getRequest();

        /** @var \Arte\PCMS\BizlogicBundle\Lib\SystemUserManager $systemUserManager */
        $systemUserManager = $this->get('Arte.PCMS.Lib.SystemUserManager');

        $tbSystemUser = $systemUserManager->getTbSystemUser($id);

        //returnUrlデコード
        $returnUrlQueryString = urldecode($request->get('ret'));

        return array(
            'entity' => $tbSystemUser,
            'returnUrlParam' => $returnUrlQueryString,
        );
    }

    /**
     * TBSystemUser編集
     *
     * @Route("/edit/{id}", name="systemuser_edit")
     * @Method({"GET", "POST"})
     * @Template("ArtePCMSAdminBundle:TBSystemUser:new.html.twig")
     */
    public function editAction($id)
    {
        $request = $this->getRequest();
        $returnParam = $request->get('ret');

//        $formModel = new TBSystemUser();
        $formType = new TBSystemUserType();

        /** @var \Arte\PCMS\BizlogicBundle\Lib\SystemUserManager $systemUserManager */
        $systemUserManager = $this->get('Arte.PCMS.Lib.SystemUserManager');
        $formModel = $systemUserManager->getTbSystemUser($id);

        $formBuilder = $this->createTBSystemUserForm($formType, $formModel, array('form'));
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
                        $formBuilder = $this->createTBSystemUserForm($formType, $formModel, array('form'), true);
                        /* @var $form \Symfony\Component\Form\Form */
                        $form = $formBuilder->setAction($this->generateUrl($request->get('_route'), array('id' => $formModel->getId())))
                            ->setMethod('POST')
                            ->getForm();
                        $form->submit($request);

                        return array(
                            'title' => 'ユーザー　変更確認',
                            'type' => 'edit',
                            'mode' => "confirm",
                            'entity' => $formModel,
                            'form' => $form->createView(),
                        );

                    }else if($form->get('Submit')->isClicked())
                    {
                        //編集実行
                        $tbSystemUser = $systemUserManager->updateSystemUser($formModel->getId(), $formModel, $formModel->getTBDepartmentDepartmentId());

                        return $this->redirect($this->generateUrl('systemuser_message', array('type' => 2,'id' => $tbSystemUser->getId(), 'ret' => $returnParam)));
                    }
                }
            }else
            {
                //ドロップダウンなどのポストバック
                $formBuilder = $this->createTBSystemUserForm($formType, $formModel, false);
                /* @var $form \Symfony\Component\Form\Form */
                $form = $formBuilder->setAction($this->generateUrl($request->get('_route'), array('id' => $id)))
                    ->setMethod('POST')
                    ->getForm();
                $form->submit($request);
            }
        }

        return array(
            'title' => 'ユーザー　変更',
            'type' => 'edit',
            'mode' => "input",
            'validate' => false,
            'entity' => $formModel,
            'form' => $form->createView(),
            'returnUrlParam' => urldecode($returnParam),
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
        $returnParam = $request->get('ret');

        //データ取得
        /** @var \Arte\PCMS\BizlogicBundle\Lib\SystemUserManager $systemUserManager */
        $systemUserManager = $this->get('Arte.PCMS.Lib.SystemUserManager');
        $entity = $systemUserManager->getTbSystemUser($id);

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
                $systemUserManager->deleteSystemUser($id);
                return $this->redirect($this->generateUrl('systemuser_message', array('type' => 3, 'ret' => $returnParam)));
//                return $this->redirect($this->generateUrl('systemuser').'?'.urldecode($returnParam));
            }
        }

        return array(
            'entity'      => $entity,
            'form' => $form->createView(),
            'returnUrlParam' => urldecode($returnParam),
        );

    }

    /**
     * パスワード変更
     *
     * @Route("/password/{id}", name="systemuser_password")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function passwordAction($id)
    {
        $request = $this->getRequest();
        $returnParam = $request->get('ret');

        $formModel = new TBSystemUser();
        $formType = new TBSystemUserType();

        /** @var \Arte\PCMS\BizlogicBundle\Lib\SystemUserManager $systemUserManager */
        $systemUserManager = $this->get('Arte.PCMS.Lib.SystemUserManager');
        $tbSystemUser = $systemUserManager->getTbSystemUser($id);

        $formBuilder = $this->createTBSystemUserForm($formType, $formModel, array('editform'));
        /* @var $form \Symfony\Component\Form\Form */
        $form = $formBuilder->setAction($this->generateUrl($request->get('_route'), array('id' => $id)))
            ->setMethod('POST')
            ->getForm();
        $form->get('ret')->setData($returnParam);

        if($request->isMethod('POST'))
        {
            $form->submit($request);
            $returnParam = $form->get('ret')->getData();

            if($form->get('Submit')->isClicked())
            {
                if($form->isValid())
                {

                    //編集実行
                    $systemUserManager->updatePassword($id, $formModel->getFormPassword());

                    return $this->redirect($this->generateUrl('systemuser_message', array('type' => 4, 'id' => $id, 'ret' => $returnParam)));

                }
            }else
            {
                //ドロップダウンなどのポストバック
                $formBuilder = $this->createTBSystemUserForm($formType, $formModel, false);
                /* @var $form \Symfony\Component\Form\Form */
                $form = $formBuilder->setAction($this->generateUrl($request->get('_route'), array('id' => $id)))
                    ->setMethod('POST')
                    ->getForm();
                $form->submit($request);
            }
        }

        return array(
            'title' => 'パスワード　変更',
            'type' => 'password_edit',
            'mode' => "input",
            'validate' => false,
            'entity' => $tbSystemUser,
            'form' => $form->createView(),
            'returnUrlParam' => urldecode($returnParam),
            'ret' => $returnParam
        );
    }

    /**
     * メッセージ表示
     *
     * @Route("/message/{type}", name="systemuser_message")
     * @Method({"GET"})
     * @Template()
     */
    public function messageAction($type)
    {
        $request = $this->getRequest();

        switch($type){
            case 1://新規登録
                $id = $request->get('id');

                return array(
                    'title' => 'ユーザー　登録完了',
                    'type' => 'systemuser_new',
                    'message' => 'ユーザーの登録が完了しました。',
                    'id' => $id,
                );
                break;
            case 2://編集
                $id = $request->get('id');

                return array(
                    'title' => 'ユーザー　編集完了',
                    'type' => 'systemuser_edit',
                    'message' => 'ユーザーの編集が完了しました。',
                    'id' => $id,
                );
                break;
            case 3://削除
                $returnUrlQueryString = urldecode($request->get('ret'));
                return array(
                    'title' => 'ユーザー　削除完了',
                    'type' => 'systemuser_delete',
                    'message' => 'ユーザーの削除が完了しました。',
                    'returnUrlParam' => $returnUrlQueryString,
                );
                break;
            case 4://パスワード変更
                $id = $request->get('id');

                return array(
                    'title' => 'パスワード変更　完了',
                    'type' => 'password_edit',
                    'message' => 'パスワードを変更しました。',
                    'id' => $id,
                );
                break;
        }
    }

}
