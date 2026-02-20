<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

use App\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

$kernel = new App\Kernel('test', true);
$kernel->boot();

$container = $kernel->getContainer()->get('test.service_container');
/** @var EntityManagerInterface $em */
$em = $container->get(EntityManagerInterface::class);
/** @var UserPasswordHasherInterface $hasher */
$hasher = $container->get(UserPasswordHasherInterface::class);

$email = 'doc_export_' . bin2hex(random_bytes(6)) . '@example.test';
$password = 'Password123!';
$user = new User(bin2hex(random_bytes(16)), $email, ['ROLE_USER'], '');
$user->setPassword($hasher->hashPassword($user, $password));
$em->persist($user);
$em->flush();

$loginRequest = Request::create(
    '/api/login_check',
    'POST',
    [],
    [],
    [],
    ['CONTENT_TYPE' => 'application/json'],
    json_encode(['username' => $email, 'password' => $password])
);
$loginResponse = $kernel->handle($loginRequest);
if ($loginResponse->getStatusCode() !== 200) {
    fwrite(STDERR, 'OpenAPI export login failed: ' . $loginResponse->getStatusCode() . "\n");
    exit(1);
}
$payload = json_decode((string) $loginResponse->getContent(), true);
$token = $payload['token'] ?? null;
if (!is_string($token) || $token === '') {
    fwrite(STDERR, "OpenAPI export login failed: token missing\n");
    exit(1);
}

$docRequest = Request::create(
    '/api/docs.jsonopenapi',
    'GET',
    [],
    [],
    [],
    ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]
);
$docResponse = $kernel->handle($docRequest);
if ($docResponse->getStatusCode() !== 200) {
    fwrite(STDERR, 'OpenAPI export failed: ' . $docResponse->getStatusCode() . "\n");
    exit(1);
}

file_put_contents(dirname(__DIR__) . '/docs/openapi.json', (string) $docResponse->getContent());
$kernel->terminate($docRequest, $docResponse);
