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
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var RequestStack
     */
    private $requestStack;

    /**
     * MenuBuilder constructor.
     * @param FactoryInterface $factory
     * @param EntityManager $em
     */
    public function __construct(FactoryInterface $factory, EntityManager $em, RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav mr-auto');

        foreach (ModelUtils::ENTITY_DOMAIN as $domain => $entity) {
            $menu->addChild(\ucfirst(\strstr($domain, '-', true)), [
                'route'     => 'post_index',
                'routeParameters'   => [
                    'domain'    => $domain //SEO route
                ],
                /*
                'extras'    => [
                    'routes'    =>
                        ['route' => 'post_show', 'parameters' => [
                            'domain' => $domain,
                            'id'    => $this->requestStack->getMasterRequest()->request->get('id'),
                            'slug'  => $this->requestStack->getMasterRequest()->request->get('slug'),
                        ]],
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

        //Set current for sub items
        $uri = $this->requestStack->getCurrentRequest()->getRequestUri();
        switch (true) {
            case \strpos($uri, 'mots'):
                $menu->getChild('Mots')->setCurrent(true);
                break;
            case \strpos($uri, 'expressions'):
                $menu->getChild('Expressions')->setCurrent(true);
                break;
            case \strpos($uri, 'proverbes'):
                $menu->getChild('Proverbes')->setCurrent(true);
                break;
            case \strpos($uri, 'blagues'):
                $menu->getChild('Blagues')->setCurrent(true);
                break;

            default:
                $menu->setCurrent(true);
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