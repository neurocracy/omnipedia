<?php

declare(strict_types=1);

use Omnipedia\BackgroundTasks\WorkerProcess;
use React\EventLoop\Loop;

require __DIR__ . '/../vendor/autoload.php';

/** @var \React\EventLoop\LoopInterface */
$loop = Loop::get();

// Run common tasks immediately.
$loop->futureTick(function(): void {
  (new WorkerProcess(__DIR__ . '/run-common.sh'))->start();
});

// Run cron every 15 minutes.
$loop->addPeriodicTimer(900, function(): void {
  (new WorkerProcess('drush cron'))->start();
});

// Every 5 minutes, process jobs from the image style warmer.
$loop->addPeriodicTimer(300, function(): void {
  (new WorkerProcess(
    'drush queue:process image_style_warmer_pregenerator'
  ))->start();
});

// Every 10 minutes, enqueue and process the warmer queue.
$loop->addPeriodicTimer(600, function() use ($loop): void {

  $loop->futureTick(function(): void {
    (new WorkerProcess('drush warmer:enqueue ' . \implode(',', [
      'omnipedia_wiki_node_changes',
      'omnipedia_wiki_node_cdn',
    ])))->start();
  });

  $loop->futureTick(function(): void {
    (new WorkerProcess('drush queue:process warmer'))->start();
  });

});
