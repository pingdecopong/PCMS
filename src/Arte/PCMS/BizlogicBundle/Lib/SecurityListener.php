<?php

namespace Arte\PCMS\BizlogicBundle\Lib;


use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListener {

    /**
     * @var \Arte\PCMS\BizlogicBundle\Lib\SystemUserManager
     */
    private $systemUserManager;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $securityContext;

    function __construct(SecurityContext $securityContext, SystemUserManager $systemUserManager)
    {
        $this->securityContext = $securityContext;
        $this->systemUserManager = $systemUserManager;
    }

    public function onLogin(InteractiveLoginEvent $event)
    {
        $tbSystemUser = $this->securityContext->getToken()->getUser();
        $this->systemUserManager->updateLoginDatetime($tbSystemUser->getId());
    }
}