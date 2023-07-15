<?php

declare(strict_types=1);

use Omnipedia\BackgroundTasks\WorkerProcess;
use React\EventLoop\Loop;

require __DIR__ . '/../vendor/autoload.php';

$drush = \realpath(__DIR__ . '/../vendor/bin/drush');

/** @var \React\EventLoop\LoopInterface */
$loop = Loop::get();

// Run common tasks immediately.
$loop->futureTick(function() {
  (new WorkerProcess(__DIR__ . '/run-common.sh'))->start();
});

// Run cron every 15 minutes.
$loop->addPeriodicTimer(900, function() use ($drush): void {
  (new WorkerProcess($drush . ' cron'))->start();
});

// Every 5 minutes, process jobs from the image style warmer.
$loop->addPeriodicTimer(300, function() use ($drush): void {
  (new WorkerProcess(
    $drush . ' queue:run image_style_warmer_pregenerator --verbose'
  ))->start();
});

// Every 10 minutes, process the wiki page changes queue.
$loop->addPeriodicTimer(600, function() use ($drush): void {
  (new WorkerProcess($drush . ' omnipedia:changes-build --verbose'))->start();
});

// Every 10 minutes, process the wiki page CDN warmer queue.
$loop->addPeriodicTimer(600, function() use ($drush): void {
  (new WorkerProcess(
    $drush . ' warmer:enqueue omnipedia_wiki_node_cdn --run-queue --verbose'
  ))->start();
});

$loop->run();
