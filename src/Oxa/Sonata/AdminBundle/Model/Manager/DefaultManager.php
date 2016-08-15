<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/14/16
 * Time: 3:05 PM
 */

namespace Oxa\Sonata\AdminBundle\Model\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DefaultManager implements ContainerAwareInterface
{
    const REPORT_NAME_DATE_FORMAT = 'Ymd_His';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }
}
