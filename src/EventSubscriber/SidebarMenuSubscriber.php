<?php

namespace App\EventSubscriber;

use App\Entity\Character;
use App\Entity\Work;
use Survos\BaseBundle\Menu\BaseMenuSubscriber;
use Survos\BaseBundle\Menu\MenuBuilder;
use Survos\BaseBundle\Services\KnpMenuHelper;
use Survos\BaseBundle\Traits\KnpMenuHelperTrait;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Survos\BaseBundle\Event\KnpMenuEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class SidebarMenuSubscriber extends BaseMenuSubscriber implements EventSubscriberInterface
{
    use KnpMenuHelperTrait;

    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security, AuthorizationCheckerInterface $authorizationChecker, ParameterBagInterface $bag, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->setAuthorizationChecker($authorizationChecker);
        $this->setParameterBag($bag);
    }

    private function isDev(): bool
    {
        // php8
//        return $this->getParameterBag()?->get('kernel.environment') == 'dev';
        return $this->getParameterBag()->get('kernel.environment') == 'dev';
    }

    public function onKnpMenuEvent(KnpMenuEvent $event)
    {
        $menu = $event->getMenu();
        $request = $this->requestStack->getCurrentRequest();

        /** @var $work Work */
        if ($work = $request->get('work')) {
            $workMenu = $menu;
            // $workMenu = $this->addMenuItem($menu, ['menu_code' => $work->getSlug(), 'label' => 'Work: ' . $work->getTitle()]);
            $this->addMenuItem($workMenu, ['route' => 'work_show', 'rp' => $work]);
            // too similar right now$this->addMenuItem($workMenu, ['route' => 'admin_work_show', 'rp' => $work]);
            $this->addMenuItem($workMenu, ['route' => 'work_characters', 'rp' => $work]);
            $this->addMenuItem($workMenu, ['route' => 'work_chapters', 'rp' => $work]);
            $this->addMenuItem($workMenu, ['route' => 'work_text', 'rp' => $work]);
            if ($this->isGranted('WORK_ADMIN', $work)) {
                $this->addMenuItem($workMenu, ['route' => 'work_edit', 'rp' => $work]);
            }
            return;
        }

        /** @var  $character Character */
        if ($character = $request->get('character')) {
            // $scriptMenu = $this->addMenuItem($menu, ['menu_code' => $script->getSlug(), 'label' => 'Script: ' . $script->getTitle()]);
            $this->addMenuItem($menu, ['route' => 'character_show', 'rp' => $character]);
            $this->addMenuItem($menu, ['route' => 'character_scenes', 'rp' => $character]);
            $this->addMenuItem($menu, ['route' => 'character_edit', 'rp' => $character]);
            return;
        }


        $this->addMenuItem($menu, ['route' => 'app_homepage', 'label' => 'Home', 'icon' => 'fas fa-home']);
        if ($this->isDev()) {
            // or 'env' => ['dev']??
            $this->addMenuItem($menu, ['route' => 'es_ally', 'label' => 'ES Ally', 'icon' => 'fas fa-search']);
        }

        $worksMenu = $this->addMenuItem($menu, ['menu_code' => 'works_header', 'icon' => 'fas fa-theater-masks']);
        $this->addMenuItem($worksMenu, ['route' => 'work_index', 'label' => 'HTML', 'icon' => 'fas fa-list']);
        $this->addMenuItem($worksMenu, ['route' => 'work_html_plus_datatable', 'label' => 'HTML+DT', 'icon' => 'fas fa-list']);
        if ($this->isDev())
        {
            $this->addMenuItem($worksMenu, ['route' => 'work_doctrine_api_platform', 'label' => 'Doctrine Search', 'icon' => 'fas fa-table']);
        // @todo: pass ROLE to addMenuItem and only display if permitted?  Or pass a boolean?

        $worksMenu = $this->addMenuItem($menu, ['menu_code' => 'search']);
        $this->addMenuItem($worksMenu, ['route' => 'search_dashboard', 'icon' => 'fas fa-list']);
        $this->addMenuItem($worksMenu, ['route' => 'search_create_index', 'icon' => 'fas fa-plus']);
        $this->addMenuItem($worksMenu, ['route' => 'work_es_datatable', 'label' => 'ElasticSearch', 'icon' => 'fas fa-table']);
    }
        if ($this->isGranted('ROLE_ADMIN')) {
            $this->addMenuItem($menu, ['route' => 'app_debug_menus', 'label' => 'Debug Menu', 'icon' => 'fas fa-bug']);
        }


//        $menu->addChild('work_index.title', ['route' => 'work_index'])->setAttribute('icon', 'fas fa-theater-masks');
//        $menu->addChild('datatable', ['route' => 'work_datatable'])->setAttribute('icon', 'fas fa-table');

        $charactersMenu = $this->addMenuItem($menu, ['menu_code' => 'characters_header', 'icon' => 'fas fa-users']);
        foreach ([
            'character_index'=>'HTML only',
                     'character_js_datatable' => 'HTML + basic dt',
                     'character_datatable_via_api' => 'API basic',
//                     'character_datatable_via_api_custom' => 'API + custom'
                     ] as $route => $lable) {
            $this->addMenuItem($charactersMenu, ['route' => $route, 'label' => $lable]);
        }
//        $worksMenu = $this->addMenuItem($menu, ['menu_code' => 'works_header', 'icon' => 'fas fa-scroll']);
//        foreach ([
//                     'work_index'=>'HTML only',
////                     'work_search' => 'Search',
//                 ] as $route => $lable) {
//            $this->addMenuItem($worksMenu, ['route' => $route, 'label' => $lable]);
//        }

        /*
         *             $this->addMenuItem($charactersMenu, ['label' => 'DataTable(HTML)', 'route' => 'character_datatable', 'icon' => 'fas fa-table']);
            $this->addMenuItem($charactersMenu, ['label' => 'DataTable(API)', 'route' => 'character_datatable_via_api', 'icon' => 'fas fa-exchange-alt']);
            $this->addMenuItem($charactersMenu, ['route' => 'character_new', 'icon' => 'fas fa-plus']);

         */

//        $this->addMenuItem($menu, ['route' => 'app_typography', 'label' => 'Bootstrap 4', 'icon' => 'fab fa-bootstrap']);
        // for nested menus, don't add a route, just a label, then use it for the argument to addMenuItem
        $nestedMenu = $this->addMenuItem($menu, ['label' => 'Credits']);
        foreach (['bundles' => 'fab fa-php', 'javascript' => 'fab fa-js-square'] as $type => $icon) {
            $this->addMenuItem($nestedMenu, [
                'route' => 'survos_base_credits', 'rp' => ['type' => $type],
                'icon' => $icon,
                'label' => ucfirst($type)]);
        }


// $menu->addChild('test_rdf', ['route' => 'test_rdf'])->setAttribute('icon', 'fas fa-sync');
//        $this->addMenuItem($menu, ['route' => 'easyadmin', 'external' => 'true', 'attributes' => ['target' => '_blank'], 'label' => 'EasyAdmin', 'icon' => 'fas fa-database']);
        $this->addMenuItem($menu, ['route' => 'api_entrypoint', 'external' => 'true', 'label' => 'API', 'icon' => 'fas fa-exchange-alt']);

//        $this->authMenu($this->getAuthorizationChecker(), $menu, $event->getChildOptions());
        // ...
    }

    public static function getSubscribedEvents()
    {
        return [
            MenuBuilder::SIDEBAR_MENU_EVENT => 'onKnpMenuEvent',
        ];
    }
}
