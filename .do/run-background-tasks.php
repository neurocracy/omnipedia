<?php

declare(strict_types=1);

use Omnipedia\BackgroundTasks\WorkerProcess;
use React\EventLoop\Loop;
use React\Promise\ExtendedPromiseInterface;
use function React\Async\series;

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

// Every 10 minutes, run queues.
$loop->addPeriodicTimer(600, function(): void {

  /** @var boolean Static flag indicating whether a run is currently in progress. */
  static $running = false;

  if ($running === true) {
    return;
  }

  $running = true;

  series([
    function(): ExtendedPromiseInterface {
      return (new WorkerProcess('drush warmer:enqueue ' . \implode(',', [
        'omnipedia_wiki_node_changes',
        'omnipedia_wiki_node_cdn',
      ])))->start()->promise();
    },
    function(): ExtendedPromiseInterface {
      return (new WorkerProcess(
        'drush queue:process image_style_warmer_pregenerator'
      ))->start()->promise();
    },
    function(): ExtendedPromiseInterface {
      return (new WorkerProcess(
        'drush queue:process warmer'
      ))->start()->promise();
    },
  ])->always(function() use (&$running) {

    $running = false;

  });

});
