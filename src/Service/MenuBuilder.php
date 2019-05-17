<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 15.02.18
 * Time: 16:13
 */

namespace App\Service;

use App\Entity\Page;
use App\Entity\Post;
use App\Utils\ModelUtils;
use Knp\Menu\FactoryInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class MenuBuilder
 * @package App\Menu
 */
class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * MenuBuilder constructor.
     * @param FactoryInterface $factory
     * @param EntityManager $em
     */
    public function __construct(FactoryInterface $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    /**
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav mr-auto');

        foreach (ModelUtils::DOMAINS as $domain) {
            $menu->addChild(\ucfirst($domain), [
                'route'     => 'post_index',
                'routeParameters'   => [
                    'domain'    => $domain //Use plural for routes
                ],
                /*
                'extras'    => [
                    'routes'    =>
                        ['route' => $route . '_show'],
                        ['route' => $route . '_new'],
                        ['route' => $route . '_edit'],
                ],
                */
                'attributes' => [
                    'class' => 'nav-item rounded',
                    'title' => \sprintf('Ajouter des %s', $domain)
                ],
                'linkAttributes' => [
                    'class' => 'nav-link'
                ]
            ]);
        }

        return $menu;
    }


    /**
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function createFooterMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'footer-nav list-unstyled text-right');
        $pages = $this->em->getRepository(Page::class)->findBy(['embedded' => false]);

        /** @var Page $page */
        foreach ($pages as $page) {
            $menu->addChild($page->getTitle(), [
                'route' => 'index_page',
                'attributes' => ['class' => ''],
                'linkAttributes' => ['class' => 'footer'],
                'routeParameters' => [
                    'alias' => $page->getAlias(),
                ]
            ]);
        }

        return $menu;
    }
}