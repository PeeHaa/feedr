#!/usr/bin/env php
<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Bin;

use PeeHaa\AwesomeFeed\Install\Installer;
use PeeHaa\AwesomeFeed\Install\Output\StdOut;
use PeeHaa\AwesomeFeed\Install\Task\CompileAssets;
use PeeHaa\AwesomeFeed\Install\Task\GenerateEncryptionKey;

require __DIR__ . '/../bootstrap.php';

$installer = new Installer(new StdOut());

$installer->addTask(new GenerateEncryptionKey(__DIR__ . '/../config/encryption.key'));
$installer->addTask(new CompileAssets(__DIR__ . '/../'));

$installer->run();
