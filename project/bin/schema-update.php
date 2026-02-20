<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\SchemaTool;

require dirname(__DIR__) . '/config/bootstrap.php';

$kernel = new App\Kernel('test', true);
$kernel->boot();

$em = $kernel->getContainer()->get('doctrine')->getManager();
$metadata = $em->getMetadataFactory()->getAllMetadata();
if (count($metadata) === 0) {
    fwrite(STDERR, "No metadata found.\n");
    exit(1);
}

$tool = new SchemaTool($em);
$tool->dropSchema($metadata);
$tool->createSchema($metadata);

fwrite(STDOUT, "Schema created.\n");
