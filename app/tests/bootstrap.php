<?php
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\ErrorHandler;
require dirname(__DIR__).'/vendor/autoload.php';
if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}
// Register Symfony's ErrorHandler early so PHPUnit 11's exception handler
// snapshot (taken before setUp()) includes it. Without this, the very first
// test that boots the kernel appears "risky" because FrameworkBundle registers
// the handler inside setUp() — after the snapshot was already taken.
if (class_exists(ErrorHandler::class)) {
    ErrorHandler::register();
}
