<?php

namespace AppBundle\Model;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\ClassLoader;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;


class ChatService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * ChatService constructor.
     */
    public function __construct()
    {
//        $this->createEntityManager();
            $this->em = (new EntityManagerFactory())->build();
    }

    private function createEntityManager()
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
        try {
            $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);
            $this->em = EntityManager::create($dbParams, $config);
        } catch (\Exception $ex) {
            echo "Error : {$ex->getMessage()}\n";
        }
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    public function processPersistedMessages()
    {
        echo "\tBEGIN processPersistedMessages\n";

        echo "\tEND processPersistedMessages\n\n";
    }
}