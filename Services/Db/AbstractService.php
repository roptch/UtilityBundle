<?php

namespace NyroDev\UtilityBundle\Services\Db;

use NyroDev\UtilityBundle\Services\AbstractService as AbstractServiceSrc;
use Doctrine\Common\Persistence\ObjectManager;

abstract class AbstractService extends AbstractServiceSrc
{
    protected $objectManager;

    public function __construct($container, ObjectManager $objectManager)
    {
        parent::__construct($container);
        $this->objectManager = $objectManager;
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @param string $name class name
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($name)
    {
        return is_object($name) ? $name : $this->getObjectManager()->getRepository($name);
    }

    /**
     * @param string $name class name
     * @param bool $elastica True to get elastica query Builder
     *
     * @return \NyroDev\UtilityBundle\QueryBuilder\AbstractQueryBuilder
     */
    public function getQueryBuilder($name, $elastica = false)
    {
        if ($elastica) {
            $class = \NyroDev\UtilityBundle\QueryBuilder\ElasticaQueryBuilder::class;
        } else {
            $class = $this->getParameter('nyrodev_utility.queryBuilder.class');
        }

        $queryBuilder = new $class(is_object($name) ? $name : $this->getRepository($name), $this->getObjectManager(), $this);

        return $queryBuilder;
    }

    public function getNew($name, $persist = true)
    {
        $repo = $this->getRepository($name);
        $classname = $repo->getClassName();
        $new = new $classname();

        if ($persist) {
            $this->persist($new);
        }

        return $new;
    }

    public function persist($object)
    {
        $this->getObjectManager()->persist($object);
    }

    public function remove($object)
    {
        $this->getObjectManager()->remove($object);
    }

    public function refresh($object)
    {
        $this->getObjectManager()->refresh($object);
    }

    public function flush()
    {
        $this->getObjectManager()->flush();
    }

    abstract public function getFormType();
}
