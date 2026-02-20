<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require_once dirname(__DIR__) . '/src/Kernel.php';

$_ENV['KERNEL_CLASS'] = $_ENV['KERNEL_CLASS'] ?? App\Kernel::class;
$_SERVER['KERNEL_CLASS'] = $_SERVER['KERNEL_CLASS'] ?? App\Kernel::class;
