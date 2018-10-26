<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Bin;

use Auryn\Injector;
use PeeHaa\AwesomeFeed\Queue\Queue;

require_once __DIR__ . '/../bootstrap.php';

/** @var Injector $auryn */
$queue = $auryn->make(Queue::class);

$queue->start();
