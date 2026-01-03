<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

if ($_ENV['APP_ENV'] === 'test') {
    $testDbPath = dirname(__DIR__) . '/var/cache/test/test.db';
    $testCacheDir = dirname($testDbPath);

    if (! is_dir($testCacheDir)) {
        mkdir($testCacheDir, 0777, true);
    }

    if (file_exists($testDbPath)) {
        unlink($testDbPath);
    }

    require_once dirname(__DIR__) . '/src/Kernel.php';
    $kernel = new App\Kernel($_ENV['APP_ENV'], (bool) $_ENV['APP_DEBUG']);
    $kernel->boot();

    $entityManager = $kernel->getContainer()
        ->get('doctrine')
        ->getManager();
    $metadata = $entityManager->getMetadataFactory()
        ->getAllMetadata();

    $schemaTool = new SchemaTool($entityManager);
    $schemaTool->createSchema($metadata);

    $kernel->shutdown();
}
