<?php

namespace App\EventSubscriber;

use App\Entity\Character;
use App\Entity\Work;

use Survos\BaseBundle\Menu\BaseMenuSubscriber;
use Survos\BaseBundle\Traits\KnpMenuHelperTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use KevinPapst\AdminLTEBundle\Event\KnpMenuEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class KnpTopMenuSubscriber extends BaseMenuSubscriber implements EventSubscriberInterface
{
    use KnpMenuHelperTrait;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Security|AuthorizationCheckerInterface
     */
    private $security;

    public function __construct(Security $security, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
    }



    public static function getSubscribedEvents()
    {
        return [
            // 'topMenuEvent' => 'onKnpTopMenuEvent'
        ];
    }
}
