<?php // generated by @SurvosLanding/MenuBuilder.php.twig

namespace App\Menu;

use Survos\LandingBundle\Menu\LandingMenuBuilder;

class MenuBuilder extends LandingMenuBuilder
{

    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

$menu->setChildrenAttribute('class', 'nav navbar-nav mr-auto');

$menu->addChild('survos_landing', ['route' => 'app_homepage'])->setAttribute('icon', 'fas fa-home');

$menu->addChild('survos_landing_credits', ['route' => 'survos_landing_credits'])->setAttribute('icon', 'fas fa-trophy');
$menu->addChild('app_typography', ['route' => 'app_typography'])->setAttribute('icon', 'fab fa-bootstrap');

$menu->addChild('work_index.title', ['route' => 'work_index'])->setAttribute('icon', 'fas fa-theater-masks');
$menu->addChild('search.title', ['route' => 'search_dashboard'])->setAttribute('icon', 'fas fa-search');

// $menu->addChild('test_rdf', ['route' => 'test_rdf'])->setAttribute('icon', 'fas fa-sync');
$menu->addChild('datatable', ['route' => 'work_datatable'])->setAttribute('icon', 'fas fa-table');
$menu->addChild('api', ['route' => 'api_entrypoint'])->setAttribute('icon', 'fas fa-exchange-alt');
$menu->addChild('easyadmin', ['route' => 'easyadmin'])->setAttribute('icon', 'fas fa-database');



try {
} catch (\Exception $e) {
    // probably not loaded.
}


// $menu->addChild('admin', ['route' => 'easyadmin']);

// ... add more children

return $this->cleanupMenu($menu);
}



}