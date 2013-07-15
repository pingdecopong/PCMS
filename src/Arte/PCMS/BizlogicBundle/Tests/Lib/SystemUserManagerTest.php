<?php
namespace Arte\PCMS\BizlogicBundle\Tests\Lib;

use Arte\PCMS\BizlogicBundle\Lib\SystemUserManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SystemUserManagerTest extends WebTestCase {

    private $em;

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testgetTbSystemUser()
    {
//        $systemUserManager = new SystemUserManager($this->em);

//        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
//        $systemUserManager = new SystemUserManager($em);

        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $systemUserManager = new SystemUserManager($em);

        $tbSystemUser = $systemUserManager->getTbSystemUser(1);
        $tbSystemUser->getLoginId();
//        $this->assertEquals('test001', $tbSystemUser->getLoginId());

    }
}
