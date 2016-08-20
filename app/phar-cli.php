<?php
use PharCli\ConsoleKernel;
use PharCli\PharCliApplication;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new PharCliApplication(new ConsoleKernel());

$app->run();
