<?php

namespace AppBundle\Model;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class EntityManagerFactory
{
    public function build()
    {
        $paths = array(
            __DIR__.'/../Entity'
        );
        $isDevMode = false;

        $dbParams = array(
            'driver'    => 'pdo_pgsql',
            'dbname'    => 'chat',
            'user'      => 'postgres',
            'password'  => '120872',
            'host'      => '127.0.0.1',
            'port'      => '5432'
        );

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);

        return EntityManager::create($dbParams, $config);
    }
}