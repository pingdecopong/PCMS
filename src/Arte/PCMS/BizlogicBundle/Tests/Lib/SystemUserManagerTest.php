<?php
namespace Arte\PCMS\BizlogicBundle\Tests\Lib;

use Arte\PCMS\BizlogicBundle\Entity\TBSystemUser;
use Arte\PCMS\BizlogicBundle\Lib\SystemUserManager;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SystemUserManagerTest extends WebTestCase {

    private $em;
    /**
     * @var \Arte\PCMS\BizlogicBundle\Lib\SystemUserManager
     */
    private $systemUserManager;

    /**
     * Exceptionテスト
     * @expectedException \Doctrine\ORM\NoResultException
     */
    public function test1getTbSystemUser()
    {
        $tbSystemUser = $this->systemUserManager->getTbSystemUser(9999);
    }

    /**
     * 正常データテスト
     */
    public function test2getTbSystemUser()
    {
        $tbSystemUser = $this->systemUserManager->getTbSystemUser(1);
        $this->assertEquals('test001', $tbSystemUser->getLoginId());
        $this->assertEquals('a', $tbSystemUser->getSalt());
        $this->assertEquals('a', $tbSystemUser->getPassword());
        $this->assertEquals(true, $tbSystemUser->getActive());
        $this->assertEquals(1, $tbSystemUser->getSystemRoleId());
        $this->assertEquals('てすと001', $tbSystemUser->getDisplayName());
        $this->assertEquals('テスト001', $tbSystemUser->getDisplayNameKana());
        $this->assertEquals('t001', $tbSystemUser->getNickName());
        $this->assertEquals('test001@test.com', $tbSystemUser->getMailAddress());
        $this->assertEquals('1', $tbSystemUser->getDepartmentId());
        $this->assertEquals(new \DateTime("2012-07-01 0:0:0"), $tbSystemUser->getLastLoginDatetime());
        $this->assertEquals(false, $tbSystemUser->getDeleteFlug());
        $this->assertEquals(null, $tbSystemUser->getCreatedUserId());
        $this->assertEquals(new \DateTime("2012-01-01 0:0:0"), $tbSystemUser->getCreatedDatetime());
        $this->assertEquals(null, $tbSystemUser->getUpdatedUserId());
        $this->assertEquals(null, $tbSystemUser->getUpdatedDatetime());
    }

    /**
     * 削除データテスト
     * @expectedException \Doctrine\ORM\NoResultException
     */
    public function test3getTbSystemUser()
    {
        $tbSystemUser = $this->systemUserManager->getTbSystemUser(2);
    }

    /**
     * 非アクティブデータテスト
     */
    public function test4getTbSystemUser()
    {
        $tbSystemUser = $this->systemUserManager->getTbSystemUser(3);
        $this->assertEquals('test003', $tbSystemUser->getLoginId());
        $this->assertEquals('a', $tbSystemUser->getSalt());
        $this->assertEquals('a', $tbSystemUser->getPassword());
        $this->assertEquals(false, $tbSystemUser->getActive());
        $this->assertEquals(1, $tbSystemUser->getSystemRoleId());
        $this->assertEquals('てすと003', $tbSystemUser->getDisplayName());
        $this->assertEquals('テスト003', $tbSystemUser->getDisplayNameKana());
        $this->assertEquals('t003', $tbSystemUser->getNickName());
        $this->assertEquals('test003@test.com', $tbSystemUser->getMailAddress());
        $this->assertEquals('1', $tbSystemUser->getDepartmentId());
        $this->assertEquals(new \DateTime("2012-07-03 0:0:0"), $tbSystemUser->getLastLoginDatetime());
        $this->assertEquals(false, $tbSystemUser->getDeleteFlug());
        $this->assertEquals(null, $tbSystemUser->getCreatedUserId());
        $this->assertEquals(new \DateTime("2012-01-03 0:0:0"), $tbSystemUser->getCreatedDatetime());
        $this->assertEquals(null, $tbSystemUser->getUpdatedUserId());
        $this->assertEquals(null, $tbSystemUser->getUpdatedDatetime());
    }

    /**
     * 正常登録テスト
     */
    public function test1createSystemUser()
    {
        //保存
        $tbSystemUser = new TBSystemUser();
        $tbSystemUser->setLoginId('create001');
        $tbSystemUser->setSystemRoleId(1);
        $tbSystemUser->setDisplayName('ゆーざー001');
        $tbSystemUser->setDisplayNameKana('ユーザー001');
        $tbSystemUser->setNickName('u001');
//        $tbSystemUser->setTbdepartment(null);
        $tbSystemUser->setMailAddress('create001@test.com');
        $tbSystemUser = $this->systemUserManager->createSystemUser($tbSystemUser);

        //確認
        $tbSystemUser = $this->systemUserManager->getTbSystemUser($tbSystemUser->getId());
        $this->assertEquals('create001', $tbSystemUser->getLoginId());
        $this->assertEquals('aaa', $tbSystemUser->getSalt());
        $this->assertEquals('bbb', $tbSystemUser->getPassword());
        $this->assertEquals(true, $tbSystemUser->getActive());
        $this->assertEquals(1, $tbSystemUser->getSystemRoleId());
        $this->assertEquals('ゆーざー001', $tbSystemUser->getDisplayName());
        $this->assertEquals('ユーザー001', $tbSystemUser->getDisplayNameKana());
        $this->assertEquals('u001', $tbSystemUser->getNickName());
        $this->assertEquals('create001@test.com', $tbSystemUser->getMailAddress());
        $this->assertEquals(null, $tbSystemUser->getDepartmentId());
//        $this->assertEquals(new \DateTime("2012-07-01 0:0:0"), $tbSystemUser->getLastLoginDatetime());
        $this->assertEquals(false, $tbSystemUser->getDeleteFlug());
//        $this->assertEquals(1, $tbSystemUser->getCreatedUserId());
        $now = new \DateTime();
        $this->assertEquals($now->getTimestamp(), $tbSystemUser->getCreatedDatetime()->getTimestamp(), '', 5);
//        $this->assertEquals(null, $tbSystemUser->getUpdatedUserId());
//        $this->assertEquals(null, $tbSystemUser->getUpdatedDatetime());
    }

    /**
     * LoginIdユニークテスト
     */
    public function test2createSystemUser()
    {

    }

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->systemUserManager = new SystemUserManager($this->em);
    }
}
