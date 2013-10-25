<?php
namespace Arte\PCMS\BizlogicBundle\Tests\Lib;

use Arte\PCMS\BizlogicBundle\Entity\TBDepartment;
use Arte\PCMS\BizlogicBundle\Entity\TBSystemUser;
use Arte\PCMS\BizlogicBundle\Lib\SystemUserManager;
use Arte\PCMS\BizlogicBundle\Lib\TBSystemUserSearchModel;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\AppKernel;
use Symfony\Component\Validator\Validation;


class SystemUserManagerTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var \Arte\PCMS\BizlogicBundle\Lib\SystemUserManager
     */
    private $systemUserManager;

    /**
     * @var AppKernel
     */
    private $localKernel;

    /**
     * ユーザー一覧取得
     * @test
     * @group none
     */
    public function ユーザー一覧取得()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10);

        //確認
        $this->assertEquals(4, $users['count']);
        $expectIds = array(1, 3, 6, 9);
        $this->checkTBSystemUserIds($expectIds, $users['result']);

    }

    /**
     * ユーザー一覧取得　検索パラメータ　バリデーション
     * @test
     * @group none
     */
    public function ユーザー一覧取得_検索パラメータ_バリデーション()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //検索データ作成
        $searchModel = new TBSystemUserSearchModel();
        $searchModel->setUsername('aaaaaaaaaabbbbbbbbbbccccccccccdddddddddde');
        $searchModel->setActive(null);
        $str = '';//51文字
        for($i=0; $i< 51; $i++)
        {
            $str .= 'a';
        }
        $searchModel->setDisplayName($str);
        $str = '';//101文字
        for($i=0; $i< 101; $i++)
        {
            $str .= 'a';
        }
        $searchModel->setMailAddress($str);
        $searchModel->setTBDepartmentDepartmentId(null);

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, null, null, $searchModel);

        //確認
        $this->assertEquals(4, $users['count']);
        $expectIds = array(1, 3, 6, 9);
        $this->checkTBSystemUserIds($expectIds, $users['result']);

    }

    /**
     * ユーザー一覧取得　検索パラメータ　Username
     * @test
     * @group none
     */
    public function ユーザー一覧取得_検索パラメータ_Username()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //検索データ作成
        $searchModel = new TBSystemUserSearchModel();
        $searchModel->setUsername('test001');

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, null, null, $searchModel);

        //確認
        $this->assertEquals(1, $users['count']);
        $expectIds = array(1);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　検索パラメータ　Username　削除データ
     * @test
     * @group none
     */
    public function ユーザー一覧取得_検索パラメータ_Username_削除データ()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //検索データ作成
        $searchModel = new TBSystemUserSearchModel();
        $searchModel->setUsername('test002');

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, null, null, $searchModel);

        //確認
        $this->assertEquals(0, $users['count']);
    }

    /**
     * ユーザー一覧取得　検索パラメータ　Username
     * @test
     * @group none
     */
    public function ユーザー一覧取得_検索パラメータ_Active()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //検索データ作成
        $searchModel = new TBSystemUserSearchModel();
        $searchModel->setActive(true);

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, null, null, $searchModel);

        //確認
        $this->assertEquals(2, $users['count']);
        $expectIds = array(1, 6);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　検索パラメータ　Username　削除データ
     * @test
     * @group none
     */
    public function ユーザー一覧取得_検索パラメータ_Active2()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //検索データ作成
        $searchModel = new TBSystemUserSearchModel();
        $searchModel->setActive(false);

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, null, null, $searchModel);

        //確認
        $this->assertEquals(2, $users['count']);
        $expectIds = array(3, 9);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　検索パラメータ　DisplayName
     * @test
     * @group none
     */
    public function ユーザー一覧取得_検索パラメータ_DisplayName()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //検索データ作成
        $searchModel = new TBSystemUserSearchModel();
        $searchModel->setDisplayName('てすと001');

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, null, null, $searchModel);

        //確認
        $this->assertEquals(1, $users['count']);
        $expectIds = array(1);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　検索パラメータ　DisplayName　削除データ
     * @test
     * @group none
     */
    public function ユーザー一覧取得_検索パラメータ_DisplayName_削除データ()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //検索データ作成
        $searchModel = new TBSystemUserSearchModel();
        $searchModel->setDisplayName('てすと002');

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, null, null, $searchModel);

        //確認
        $this->assertEquals(0, $users['count']);
    }

    /**
     * ユーザー一覧取得　検索パラメータ　MailAddress
     * @test
     * @group none
     */
    public function ユーザー一覧取得_検索パラメータ_MailAddress()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //検索データ作成
        $searchModel = new TBSystemUserSearchModel();
        $searchModel->setMailAddress('test001@test.com');

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, null, null, $searchModel);

        //確認
        $this->assertEquals(1, $users['count']);
        $expectIds = array(1);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　検索パラメータ　MailAddress　削除データ
     * @test
     * @group none
     */
    public function ユーザー一覧取得_検索パラメータ_MailAddress_削除データ()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //検索データ作成
        $searchModel = new TBSystemUserSearchModel();
        $searchModel->setMailAddress('test002@test.com');

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, null, null, $searchModel);

        //確認
        $this->assertEquals(0, $users['count']);
    }

    /**
     * ユーザー一覧取得　検索パラメータ　Department
     * @test
     * @group none
     */
    public function ユーザー一覧取得_検索パラメータ_Department()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //検索データ作成
        $searchModel = new TBSystemUserSearchModel();
        $tbDepartment = $this->em->getRepository('ArtePCMSBizlogicBundle:TBDepartment')->find(1);//所属無し
        $searchModel->setTBDepartmentDepartmentId($tbDepartment);

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, null, null, $searchModel);

        //確認
        $this->assertEquals(2, $users['count']);
        $expectIds = array(1, 6);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　検索パラメータ　Department　削除済み部署で検索
     * @test
     * @group none
     */
    public function ユーザー一覧取得_検索パラメータ_Department_削除済み部署で検索()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //検索データ作成
        $searchModel = new TBSystemUserSearchModel();
        $tbDepartment = $this->em->getRepository('ArtePCMSBizlogicBundle:TBDepartment')->find(3);//部署02
        $searchModel->setTBDepartmentDepartmentId($tbDepartment);

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, null, null, $searchModel);

        //確認
        $this->assertEquals(1, $users['count']);
        $expectIds = array(3);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　ソート　Username　ASC
     * @test
     * @group none
     */
    public function ユーザー一覧取得_ソート_Username_ASC()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, 'Username', 'asc');

        //確認
        $this->assertEquals(4, $users['count']);
        $expectIds = array(1, 3, 6, 9);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　ソート　Username　DESC
     * @test
     * @group none
     */
    public function ユーザー一覧取得_ソート_Username_DESC()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, 'Username', 'desc');

        //確認
        $this->assertEquals(4, $users['count']);
        $expectIds = array(9, 6, 3, 1);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　ソート　Active　ASC
     * @test
     * @group none
     */
    public function ユーザー一覧取得_ソート_Active_ASC()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, 'Active', 'asc');

        //確認
        $this->assertEquals(4, $users['count']);
        $expectIds = array(9, 3, 6, 1);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　ソート　Active　DESC
     * @test
     * @group none
     */
    public function ユーザー一覧取得_ソート_Active_DESC()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, 'Active', 'desc');

        //確認
        $this->assertEquals(4, $users['count']);
        $expectIds = array(6, 1, 9, 3);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　ソート　DisplayName　ASC
     * @test
     * @group none
     */
    public function ユーザー一覧取得_ソート_DisplayName_ASC()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, 'DisplayName', 'asc');

        //確認
        $this->assertEquals(4, $users['count']);
        $expectIds = array(1, 3, 6, 9);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　ソート　DisplayName　DESC
     * @test
     * @group none
     */
    public function ユーザー一覧取得_ソート_DisplayName_DESC()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, 'DisplayName', 'desc');

        //確認
        $this->assertEquals(4, $users['count']);
        $expectIds = array(9, 6, 3, 1);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　ソート　MailAddress　ASC
     * @test
     * @group none
     */
    public function ユーザー一覧取得_ソート_MailAddress_ASC()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, 'MailAddress', 'asc');

        //確認
        $this->assertEquals(4, $users['count']);
        $expectIds = array(1, 3, 6, 9);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　ソート　MailAddress　DESC
     * @test
     * @group none
     */
    public function ユーザー一覧取得_ソート_MailAddress_DESC()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, 'MailAddress', 'desc');

        //確認
        $this->assertEquals(4, $users['count']);
        $expectIds = array(9, 6, 3, 1);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　ソート　TBDepartment　ASC
     * @test
     * @group none
     */
    public function ユーザー一覧取得_ソート_TBDepartment_ASC()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, 'TBDepartment', 'asc');

        //確認
        $this->assertEquals(4, $users['count']);
        $expectIds = array(6, 1, 3, 9);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー一覧取得　ソート　TBDepartment　DESC
     * @test
     * @group none
     */
    public function ユーザー一覧取得_ソート_TBDepartment_DESC()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $users = $this->systemUserManager->getTBSystemUserList(1, 10, 'TBDepartment', 'desc');

        //確認
        $this->assertEquals(4, $users['count']);
        $expectIds = array(9, 3, 6, 1);
        $this->checkTBSystemUserIds($expectIds, $users['result']);
    }

    /**
     * ユーザー取得１
     * @test
     * @group none
     */
    public function ユーザー取得１()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();

        //実行
        $user = $this->systemUserManager->getTbSystemUser(1);

        //確認
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('test001', $user->getUsername());
        $this->assertEquals('25451566efae7a5da9672e561feb5079', $user->getSalt());
        $this->assertEquals('b8a05631d0c15ddefa74ec5232b6ab19f1493c77', $user->getPassword());
        $this->assertEquals(true, $user->getActive());
        $this->assertEquals(1, $user->getSystemRoleId());
        $this->assertEquals('てすと001', $user->getDisplayName());
        $this->assertEquals('テスト001', $user->getDisplayNameKana());
        $this->assertEquals('t001', $user->getNickName());
        $this->assertEquals('test001@test.com', $user->getMailAddress());
        $this->assertEquals(1, $user->getDepartmentId());
        $this->assertEquals(new \DateTime("2012-07-1 0:0:0"), $user->getLastLoginDatetime());
        $this->assertEquals(false, $user->getDeleteFlag());
        //リレーション先
        $this->assertEquals(1, $user->getTBDepartmentDepartmentId()->getId());
        $this->assertEquals('所属無し', $user->getTBDepartmentDepartmentId()->getName());
        $this->assertEquals(false, $user->getTBDepartmentDepartmentId()->getDeleteFlag());

    }

    /**
     * ユーザー取得２（削除データ）
     * @test
     * @group none
     * @expectedException \Doctrine\ORM\NoResultException
     */
    public function ユーザー取得２_削除データ()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();

        //実行
        $user = $this->systemUserManager->getTbSystemUser(2);

        //確認
        //exception発生
    }

    /**
     * ユーザー取得３(部署削除済み)
     * @test
     * @group none
     */
    public function ユーザー取得３_部署削除済み()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();

        //実行
        $user = $this->systemUserManager->getTbSystemUser(3);

        //確認
        $this->assertEquals(3, $user->getId());
        $this->assertEquals('test003', $user->getUsername());
        $this->assertEquals('25451566efae7a5da9672e561feb5079', $user->getSalt());
        $this->assertEquals('b8a05631d0c15ddefa74ec5232b6ab19f1493c77', $user->getPassword());
        $this->assertEquals(false, $user->getActive());
        $this->assertEquals(1, $user->getSystemRoleId());
        $this->assertEquals('てすと003', $user->getDisplayName());
        $this->assertEquals('テスト003', $user->getDisplayNameKana());
        $this->assertEquals('t003', $user->getNickName());
        $this->assertEquals('test003@test.com', $user->getMailAddress());
        $this->assertEquals(3, $user->getDepartmentId());
        $this->assertEquals(new \DateTime("2012-07-3 0:0:0"), $user->getLastLoginDatetime());
        $this->assertEquals(false, $user->getDeleteFlag());
        //リレーション先
        $this->assertEquals(3, $user->getTBDepartmentDepartmentId()->getId());
        $this->assertEquals('部署02', $user->getTBDepartmentDepartmentId()->getName());
        $this->assertEquals(true, $user->getTBDepartmentDepartmentId()->getDeleteFlag());

    }

    /**
     * ユーザー取得４(無効)
     * @test
     * @group none
     */
    public function ユーザー取得４_無効()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();

        //実行
        $user = $this->systemUserManager->getTbSystemUser(9);

        //確認
        $this->assertEquals(9, $user->getId());
        $this->assertEquals('test009', $user->getUsername());
        $this->assertEquals('25451566efae7a5da9672e561feb5079', $user->getSalt());
        $this->assertEquals('b8a05631d0c15ddefa74ec5232b6ab19f1493c77', $user->getPassword());
        $this->assertEquals(false, $user->getActive());
        $this->assertEquals(1, $user->getSystemRoleId());
        $this->assertEquals('てすと009', $user->getDisplayName());
        $this->assertEquals('テスト009', $user->getDisplayNameKana());
        $this->assertEquals('t009', $user->getNickName());
        $this->assertEquals('test009@test.com', $user->getMailAddress());
        $this->assertEquals(4, $user->getDepartmentId());
        $this->assertEquals(new \DateTime("2012-07-9 0:0:0"), $user->getLastLoginDatetime());
        $this->assertEquals(false, $user->getDeleteFlag());
        //リレーション先
        $this->assertEquals(4, $user->getTBDepartmentDepartmentId()->getId());
        $this->assertEquals('部署03', $user->getTBDepartmentDepartmentId()->getName());
        $this->assertEquals(false, $user->getTBDepartmentDepartmentId()->getDeleteFlag());
    }

    /**
     * Username存在チェック 存在する
     * @test
     * @group none
     */
    public function Username存在チェック_存在する()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();

        //実行
        $result = $this->systemUserManager->existsUsername('test001');

        //確認
        $this->assertEquals(true, $result);
    }

    /**
     * Username存在チェック 存在しない
     * @test
     * @group none
     */
    public function Username存在チェック_存在しない()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();

        //実行
        $result = $this->systemUserManager->existsUsername('aaa');

        //確認
        $this->assertEquals(false, $result);
    }

    /**
     * ユーザー登録１
     * @test
     * @group none
     */
    public function ユーザー登録１()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //ユーザー
        $tbSystemUser = new TBSystemUser();
        $tbSystemUser->setUsername('test100');
        $tbSystemUser->setSalt('25451566efae7a5da9672e561feb5079');
        $tbSystemUser->setActive(true);
        $tbSystemUser->setSystemRoleId(1);
        $tbSystemUser->setDisplayName('てすと100');
        $tbSystemUser->setDisplayNameKana('テスト100');
        $tbSystemUser->setNickName('t100');
        $tbSystemUser->setMailAddress('test100@test.com');
        $tbSystemUser->setLastLoginDatetime(new \DateTime("2013-09-10 0:0:0"));
        $tbSystemUser->setDeleteFlag(false);
        //部署
        $tbDepartment = $this->em->getRepository('ArtePCMSBizlogicBundle:TBDepartment')->find(1);//所属無し

        //実行
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'abcdefgh');

        //確認
        $this->em->clear();
        $resultTBSystemUser = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find($user->getId());
        $user = null;
        $this->assertEquals('test100', $resultTBSystemUser->getUsername());
        $this->assertEquals('25451566efae7a5da9672e561feb5079', $resultTBSystemUser->getSalt());
        $this->assertEquals('b8a05631d0c15ddefa74ec5232b6ab19f1493c77', $resultTBSystemUser->getPassword());
        $this->assertEquals(true, $resultTBSystemUser->getActive());
        $this->assertEquals(1, $resultTBSystemUser->getSystemRoleId());
        $this->assertEquals('てすと100', $resultTBSystemUser->getDisplayName());
        $this->assertEquals('テスト100', $resultTBSystemUser->getDisplayNameKana());
        $this->assertEquals('t100', $resultTBSystemUser->getNickName());
        $this->assertEquals('test100@test.com', $resultTBSystemUser->getMailAddress());
        $this->assertEquals(1, $resultTBSystemUser->getDepartmentId());
        $this->assertEquals(new \DateTime("2013-09-10 0:0:0"), $resultTBSystemUser->getLastLoginDatetime());
        $this->assertEquals(false, $resultTBSystemUser->getDeleteFlag());
        //リレーション先
        $this->assertEquals(1, $resultTBSystemUser->getTBDepartmentDepartmentId()->getId());
        $this->assertEquals('所属無し', $resultTBSystemUser->getTBDepartmentDepartmentId()->getName());
        $this->assertEquals(false, $resultTBSystemUser->getTBDepartmentDepartmentId()->getDeleteFlag());

    }

    /**
     * ユーザー登録(不正データ)
     *
     * 登録されてない部署を指定しているためexception発生
     * @test
     * @group none
     * @expectedException \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function ユーザー登録_不正データ()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //ユーザー
        $tbSystemUser = new TBSystemUser();
        $tbSystemUser->setUsername('test100');
        $tbSystemUser->setSalt('25451566efae7a5da9672e561feb5079');
        $tbSystemUser->setActive(true);
        $tbSystemUser->setSystemRoleId(1);
        $tbSystemUser->setDisplayName('てすと100');
        $tbSystemUser->setDisplayNameKana('テスト100');
        $tbSystemUser->setNickName('t100');
        $tbSystemUser->setMailAddress('test100@test.com');
        $tbSystemUser->setLastLoginDatetime(new \DateTime("2013-09-10 0:0:0"));
        $tbSystemUser->setDeleteFlag(false);
        //部署
        $tbDepartment = new TBDepartment();
        $tbDepartment->setName('不正部署');
        $tbDepartment->setSortNo(10);

        //実行
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'abcdefgh');

        //確認
        //exception発生

    }

    /**
     * ユーザー登録　バリデーション
     * @test
     * @group none
     */
    public function ユーザー登録_バリデーション()
    {
        //@expectedException \Doctrine\ORM\ORMInvalidArgumentException
        $errorCount = 0;
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //部署
        $tbDepartment = $this->em->getRepository('ArtePCMSBizlogicBundle:TBDepartment')->find(1);//所属無し

        /*********************** Username ***********************/
        //Username 必須
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setUsername(null);
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setUsername('');
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //Username string
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setUsername(true);
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //Username 6文字以下
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setUsername('aaaaa');//5文字
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setUsername('aaaaaa');//6文字
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');//OK

        //Username 40文字以上
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setUsername('aaaaaaaaaabbbbbbbbbbccccccccccdddddddddd');//40文字
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setUsername('aaaaaaaaaabbbbbbbbbbccccccccccdddddddddde');//41文字
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //Username 成功
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setUsername('abcdefghij');
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');

        /*********************** Active ***********************/
        //Active 必須
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setActive(null);
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }
        $tbSystemUser = $this->createTBSystemUserDefaultData();
//        $tbSystemUser->setActive('');
//        try{
//            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
//        }catch (\Exception $e){
//            $errorCount++;
//        }

//        //Active bool
//        $tbSystemUser = $this->createTBSystemUserDefaultData();
//        $tbSystemUser->setActive('abcdefg');//文字列
//        try{
//            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
//        }catch (\Exception $e){
//            $errorCount++;
//        }

        //Active 成功
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setActive(1);
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setActive(0);
//        $tbSystemUser->setUsername('test101');
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');

        /*********************** SystemRoleId ***********************/
        //SystemRoleId 必須
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setSystemRoleId(null);
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setSystemRoleId('');
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //SystemRoleId int
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setSystemRoleId('abcdefg');//文字列
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //SystemRoleId 選択肢以外
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setSystemRoleId(3);
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //SystemRoleId 成功
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setSystemRoleId(1);
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setSystemRoleId(2);
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');

        /*********************** DisplayName ***********************/
        //DisplayName 必須
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setDisplayName(null);
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setDisplayName('');
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //DisplayName string
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setDisplayName(true);
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //DisplayName 50文字以上
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setDisplayName('aaaaaaaaaabbbbbbbbbbccccccccccddddddddddeeeeeeeeee');//50文字
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setDisplayName('aaaaaaaaaabbbbbbbbbbccccccccccddddddddddeeeeeeeeeef');//51文字
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //DisplayName 成功
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setDisplayName('abcdefghij');
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');

        /*********************** DisplayNameKana ***********************/
        //DisplayNameKana 必須
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setDisplayNameKana(null);
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setDisplayNameKana('');
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //DisplayNameKana string
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setDisplayNameKana(true);
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //DisplayNameKana 50文字以上
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setDisplayNameKana('aaaaaaaaaabbbbbbbbbbccccccccccddddddddddeeeeeeeeee');//50文字
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setDisplayNameKana('aaaaaaaaaabbbbbbbbbbccccccccccddddddddddeeeeeeeeeef');//51文字
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //DisplayNameKana 成功
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setDisplayNameKana('abcdefghij');
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');

        /*********************** NickName ***********************/
        //NickName 必須
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setNickName(null);
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setNickName('');
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //NickName string
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setNickName(true);
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //NickName 10文字以上
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setNickName('aaaaaaaaaa');//10文字
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setNickName('aaaaaaaaaab');//11文字
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //NickName 成功
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setNickName('abcd');
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');

        /*********************** MailAddress ***********************/
        //MailAddress 必須
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setMailAddress(null);
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setMailAddress('');
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //MailAddress string
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setMailAddress(true);
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //MailAddress 100文字以上
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $str = '';//100文字
        for($i=0; $i< 100; $i++)
        {
            $str .= 'a';
        }
        $tbSystemUser->setMailAddress($str);//100文字
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $str = '';//101文字
        for($i=0; $i< 101; $i++)
        {
            $str .= 'a';
        }
        $tbSystemUser->setMailAddress($str);//101文字
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //MailAddress 成功
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $tbSystemUser->setMailAddress('abcd');
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');

        /*********************** TBDepartmentDepartmentId ***********************/
        //TBDepartmentDepartmentId 必須
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, null, 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        try{
            $user = $this->systemUserManager->createSystemUser($tbSystemUser, '', 'aaaaaaaaaa');
        }catch (\Exception $e){
            $errorCount++;
        }

        //TBDepartmentDepartmentId 成功
        $tbSystemUser = $this->createTBSystemUserDefaultData();
        $user = $this->systemUserManager->createSystemUser($tbSystemUser, $tbDepartment, 'aaaaaaaaaa');

        //確認
        $this->assertEquals(28, $errorCount);

    }

    /**
     * ユーザー変更　正常登録
     * @test
     * @group none
     */
    public function ユーザー変更_正常登録()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //ユーザー
        $tbSystemUser = $this->systemUserManager->getTbSystemUser(1);
        $tbSystemUser->setUsername('test100');
        $tbSystemUser->setActive(true);
        $tbSystemUser->setSystemRoleId(1);
        $tbSystemUser->setDisplayName('てすと100');
        $tbSystemUser->setDisplayNameKana('テスト100');
        $tbSystemUser->setNickName('t100');
        $tbSystemUser->setMailAddress('test100@test.com');
        $tbSystemUser->setLastLoginDatetime(new \DateTime("2013-09-10 0:0:0"));
        $tbSystemUser->setDeleteFlag(false);
        //部署
        $tbDepartment = $this->em->getRepository('ArtePCMSBizlogicBundle:TBDepartment')->find(2);//部署01

        //実行
        $user = $this->systemUserManager->updateSystemUser(1, $tbSystemUser, $tbDepartment, '12345678');

        //確認
        $this->em->clear();
        $resultTBSystemUser = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find($user->getId());
        $user = null;
        $this->assertEquals('test100', $resultTBSystemUser->getUsername());
        $this->assertEquals('25451566efae7a5da9672e561feb5079', $resultTBSystemUser->getSalt());
        $this->assertEquals('2e787f369d13a097f730a74ce13668a8e006405a', $resultTBSystemUser->getPassword());
        $this->assertEquals(true, $resultTBSystemUser->getActive());
        $this->assertEquals(1, $resultTBSystemUser->getSystemRoleId());
        $this->assertEquals('てすと100', $resultTBSystemUser->getDisplayName());
        $this->assertEquals('テスト100', $resultTBSystemUser->getDisplayNameKana());
        $this->assertEquals('t100', $resultTBSystemUser->getNickName());
        $this->assertEquals('test100@test.com', $resultTBSystemUser->getMailAddress());
        $this->assertEquals(2, $resultTBSystemUser->getDepartmentId());
        $this->assertEquals(new \DateTime("2013-09-10 0:0:0"), $resultTBSystemUser->getLastLoginDatetime());
        $this->assertEquals(false, $resultTBSystemUser->getDeleteFlag());
        //リレーション先
        $this->assertEquals(2, $resultTBSystemUser->getTBDepartmentDepartmentId()->getId());
        $this->assertEquals('部署01', $resultTBSystemUser->getTBDepartmentDepartmentId()->getName());
        $this->assertEquals(false, $resultTBSystemUser->getTBDepartmentDepartmentId()->getDeleteFlag());

    }

    /**
     * ユーザー変更　パスワード変更無し
     * @test
     * @group none
     */
    public function ユーザー変更_パスワード変更無し()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //ユーザー
        $tbSystemUser = $this->systemUserManager->getTbSystemUser(1);
        $tbSystemUser->setUsername('test100');
        $tbSystemUser->setActive(true);
        $tbSystemUser->setSystemRoleId(1);
        $tbSystemUser->setDisplayName('てすと100');
        $tbSystemUser->setDisplayNameKana('テスト100');
        $tbSystemUser->setNickName('t100');
        $tbSystemUser->setMailAddress('test100@test.com');
        $tbSystemUser->setLastLoginDatetime(new \DateTime("2013-09-10 0:0:0"));
        $tbSystemUser->setDeleteFlag(false);
        //部署
        $tbDepartment = $this->em->getRepository('ArtePCMSBizlogicBundle:TBDepartment')->find(2);//部署01

        //実行
        $user = $this->systemUserManager->updateSystemUser(1, $tbSystemUser, $tbDepartment, null);

        //確認
        $this->em->clear();
        $resultTBSystemUser = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find($user->getId());
        $user = null;
        $this->assertEquals('test100', $resultTBSystemUser->getUsername());
        $this->assertEquals('25451566efae7a5da9672e561feb5079', $resultTBSystemUser->getSalt());
        $this->assertEquals('b8a05631d0c15ddefa74ec5232b6ab19f1493c77', $resultTBSystemUser->getPassword());
        $this->assertEquals(true, $resultTBSystemUser->getActive());
        $this->assertEquals(1, $resultTBSystemUser->getSystemRoleId());
        $this->assertEquals('てすと100', $resultTBSystemUser->getDisplayName());
        $this->assertEquals('テスト100', $resultTBSystemUser->getDisplayNameKana());
        $this->assertEquals('t100', $resultTBSystemUser->getNickName());
        $this->assertEquals('test100@test.com', $resultTBSystemUser->getMailAddress());
        $this->assertEquals(2, $resultTBSystemUser->getDepartmentId());
        $this->assertEquals(new \DateTime("2013-09-10 0:0:0"), $resultTBSystemUser->getLastLoginDatetime());
        $this->assertEquals(false, $resultTBSystemUser->getDeleteFlag());
        //リレーション先
        $this->assertEquals(2, $resultTBSystemUser->getTBDepartmentDepartmentId()->getId());
        $this->assertEquals('部署01', $resultTBSystemUser->getTBDepartmentDepartmentId()->getName());
        $this->assertEquals(false, $resultTBSystemUser->getTBDepartmentDepartmentId()->getDeleteFlag());

    }

    /**
     * ユーザーパスワード変更　成功
     * @test
     * @group debug
     */
    public function ユーザーパスワード変更_成功()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $user = $this->systemUserManager->updatePassword(1, '12345678');

        //確認
        $this->em->clear();
        $resultTBSystemUser = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);
        $this->assertEquals(1, $resultTBSystemUser->getId());
        $this->assertEquals('test001', $resultTBSystemUser->getUsername());
        $this->assertEquals('25451566efae7a5da9672e561feb5079', $resultTBSystemUser->getSalt());
        $this->assertEquals('2e787f369d13a097f730a74ce13668a8e006405a', $resultTBSystemUser->getPassword());
        $this->assertEquals(true, $resultTBSystemUser->getActive());
        $this->assertEquals(1, $resultTBSystemUser->getSystemRoleId());
        $this->assertEquals('てすと001', $resultTBSystemUser->getDisplayName());
        $this->assertEquals('テスト001', $resultTBSystemUser->getDisplayNameKana());
        $this->assertEquals('t001', $resultTBSystemUser->getNickName());
        $this->assertEquals('test001@test.com', $resultTBSystemUser->getMailAddress());
        $this->assertEquals(1, $resultTBSystemUser->getDepartmentId());
        $this->assertEquals(new \DateTime("2012-07-1 0:0:0"), $resultTBSystemUser->getLastLoginDatetime());
        $this->assertEquals(false, $resultTBSystemUser->getDeleteFlag());
        //リレーション先
        $this->assertEquals(1, $resultTBSystemUser->getTBDepartmentDepartmentId()->getId());
        $this->assertEquals('所属無し', $resultTBSystemUser->getTBDepartmentDepartmentId()->getName());
        $this->assertEquals(false, $resultTBSystemUser->getTBDepartmentDepartmentId()->getDeleteFlag());

    }

    /**
     * ユーザーパスワード変更　失敗
     * @test
     * @group debug
     * @expectedException \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function ユーザーパスワード変更_失敗()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $user = $this->systemUserManager->updatePassword(1, '123');

        //exception発生
    }

    /**
     * ログイン日時更新
     * @test
     * @group none
     */
    public function ログイン日時更新()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $this->systemUserManager->updateLoginDatetime(1);

        //確認
        $this->em->clear();
        $resultTBSystemUser = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);
        $this->assertEquals(1, $resultTBSystemUser->getId());
        $this->assertEquals('test001', $resultTBSystemUser->getUsername());
        $this->assertEquals('25451566efae7a5da9672e561feb5079', $resultTBSystemUser->getSalt());
        $this->assertEquals('b8a05631d0c15ddefa74ec5232b6ab19f1493c77', $resultTBSystemUser->getPassword());
        $this->assertEquals(true, $resultTBSystemUser->getActive());
        $this->assertEquals(1, $resultTBSystemUser->getSystemRoleId());
        $this->assertEquals('てすと001', $resultTBSystemUser->getDisplayName());
        $this->assertEquals('テスト001', $resultTBSystemUser->getDisplayNameKana());
        $this->assertEquals('t001', $resultTBSystemUser->getNickName());
        $this->assertEquals('test001@test.com', $resultTBSystemUser->getMailAddress());
        $this->assertEquals(1, $resultTBSystemUser->getDepartmentId());
        $this->assertEquals(new \DateTime(), $resultTBSystemUser->getLastLoginDatetime());
        $this->assertEquals(false, $resultTBSystemUser->getDeleteFlag());
        //リレーション先
        $this->assertEquals(1, $resultTBSystemUser->getTBDepartmentDepartmentId()->getId());
        $this->assertEquals('所属無し', $resultTBSystemUser->getTBDepartmentDepartmentId()->getName());
        $this->assertEquals(false, $resultTBSystemUser->getTBDepartmentDepartmentId()->getDeleteFlag());
    }

    /**
     * ユーザー削除　正常
     * @test
     * @group none
     */
    public function ユーザー削除_正常()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $this->systemUserManager->deleteSystemUser(1);

        //確認
        $resultTBSystemUser = $this->em->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')->find(1);
        $this->assertEquals(1, $resultTBSystemUser->getId());
        $this->assertEquals('test001', $resultTBSystemUser->getUsername());
        $this->assertEquals('25451566efae7a5da9672e561feb5079', $resultTBSystemUser->getSalt());
        $this->assertEquals('b8a05631d0c15ddefa74ec5232b6ab19f1493c77', $resultTBSystemUser->getPassword());
        $this->assertEquals(true, $resultTBSystemUser->getActive());
        $this->assertEquals(1, $resultTBSystemUser->getSystemRoleId());
        $this->assertEquals('てすと001', $resultTBSystemUser->getDisplayName());
        $this->assertEquals('テスト001', $resultTBSystemUser->getDisplayNameKana());
        $this->assertEquals('t001', $resultTBSystemUser->getNickName());
        $this->assertEquals('test001@test.com', $resultTBSystemUser->getMailAddress());
        $this->assertEquals(1, $resultTBSystemUser->getDepartmentId());
        $this->assertEquals(new \DateTime("2012-07-1 0:0:0"), $resultTBSystemUser->getLastLoginDatetime());
        $this->assertEquals(true, $resultTBSystemUser->getDeleteFlag());
        //リレーション先
        $this->assertEquals(1, $resultTBSystemUser->getTBDepartmentDepartmentId()->getId());
        $this->assertEquals('所属無し', $resultTBSystemUser->getTBDepartmentDepartmentId()->getName());
        $this->assertEquals(false, $resultTBSystemUser->getTBDepartmentDepartmentId()->getDeleteFlag());

    }

    /**
     * ユーザー削除　失敗　削除済みデータ
     * @test
     * @group none
     * @expectedException \Doctrine\ORM\NoResultException
     */
    public function ユーザー削除_失敗_削除済みデータ()
    {
        //データ生成
        $this->deleteDBData();
        $this->createTBDepartmentData();
        $this->createTBSystemUserData();
        $this->em->clear();

        //実行
        $this->systemUserManager->deleteSystemUser(2);

        //確認
        //exception発生

    }

    private $countCreateTBSystemUserDefaultData = 100;
    private function createTBSystemUserDefaultData()
    {
        $username = sprintf("%03d", $this->countCreateTBSystemUserDefaultData);
        //ユーザー
        $tbSystemUser = new TBSystemUser();
        $tbSystemUser->setUsername('test'.$username);
        $tbSystemUser->setPassword('abc');
        $tbSystemUser->setActive(true);
        $tbSystemUser->setSystemRoleId(1);
        $tbSystemUser->setDisplayName('てすと100');
        $tbSystemUser->setDisplayNameKana('テスト100');
        $tbSystemUser->setNickName('t100');
        $tbSystemUser->setMailAddress('test100@test.com');
        $tbSystemUser->setLastLoginDatetime(new \DateTime("2013-09-10 0:0:0"));
        $tbSystemUser->setDeleteFlag(false);

        $this->countCreateTBSystemUserDefaultData++;

        return $tbSystemUser;
    }

    /**
     * TBSystemUser IDチェック
     * @param $expectIds 期待するID配列
     * @param $tbSystemUsers チェック対象のTBSystemUser配列
     */
    private function checkTBSystemUserIds($expectIds, $tbSystemUsers)
    {
        //count check
        $this->assertEquals(count($expectIds), count($tbSystemUsers));

        //detail check
        $i = 0;
        foreach($tbSystemUsers as $value)
        {
            /** @var $value \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser */
            $this->assertEquals($expectIds[$i], $value->getId());
            $i++;
        }

    }

    /**
     * TBDepartment 初期データ作成
     * @throws \Exception
     */
    private function createTBDepartmentData()
    {
        $this->em->getConnection()->beginTransaction();
        try {

            $bundle = $this->localKernel->getBundle('ArtePCMSBizlogicBundle');
            $bundlePath = $bundle->getPath();
            $dbDumpFilePath = $bundlePath . '/Resources/sql/TestDataTBDepartment.sql';
            $sql = file_get_contents($dbDumpFilePath);
            $this->em->getConnection()->query($sql);

            $this->em->getConnection()->commit();

        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
        }
    }

    /**
     * TBSystemUser 初期データ作成
     * @throws \Exception
     */
    private function createTBSystemUserData()
    {
        $this->em->getConnection()->beginTransaction();
        try {

            $bundle = $this->localKernel->getBundle('ArtePCMSBizlogicBundle');
            $bundlePath = $bundle->getPath();
            $dbDumpFilePath = $bundlePath . '/Resources/sql/TestDataTBSystemUser.sql';
            $sql = file_get_contents($dbDumpFilePath);
            $this->em->getConnection()->query($sql);

            $this->em->getConnection()->commit();

        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
        }
    }

    /**
     * 全テーブルTruncate
     */
    private function deleteDBData()
    {

        $connection = $this->em->getConnection();
        $platform   = $connection->getDatabasePlatform();

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');

        $truncateSql = $platform->getTruncateTableSQL('TBProductionCost');
        $connection->executeUpdate($truncateSql);
        $truncateSql = $platform->getTruncateTableSQL('TBProjectCostMaster');
        $connection->executeUpdate($truncateSql);
        $truncateSql = $platform->getTruncateTableSQL('TBProjectCostHierarchyMaster');
        $connection->executeUpdate($truncateSql);
        $truncateSql = $platform->getTruncateTableSQL('TBProjectUser');
        $connection->executeUpdate($truncateSql);
        $truncateSql = $platform->getTruncateTableSQL('TBProjectMaster');
        $connection->executeUpdate($truncateSql);
        $truncateSql = $platform->getTruncateTableSQL('TBDepartment');
        $connection->executeUpdate($truncateSql);
        $truncateSql = $platform->getTruncateTableSQL('TBSystemUser');
        $connection->executeUpdate($truncateSql);

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
    }

    protected function setUp()
    {
        $this->localKernel = static::createKernel();
        $this->localKernel->boot();
        $this->em = $this->localKernel->getContainer()->get('doctrine.orm.entity_manager');
        $validation = $this->localKernel->getContainer()->get('validator');
        $encoder = $this->localKernel->getContainer()->get('security.encoder_factory');

        $this->systemUserManager = new SystemUserManager($this->em, $validation, $encoder);
    }
}
