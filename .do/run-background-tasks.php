<?php

declare(strict_types=1);

use Omnipedia\BackgroundTasks\LeadingPeriodicTimer;
use Omnipedia\BackgroundTasks\WorkerProcess;
use React\EventLoop\Loop;
use React\Promise\PromiseInterface;
use function React\Async\series;

require __DIR__ . '/../vendor/autoload.php';

/** @var \React\EventLoop\LoopInterface */
$loop = Loop::get();

// Create .htaccess in various directories to prevent PHP execution.
$loop->futureTick(function(): void {
  (new WorkerProcess(__DIR__ . '/build/write-htaccess.sh'))->start();
});

/**
 * True if maintenance mode is currently on; false otherwise.
 *
 * @var boolean
 */
$maintenance = \trim(\exec('drush maint:get')) === '1';

/**
 * Amount of time to sleep between checks for maintenance mode to turn off.
 *
 * @var integer
 */
$maintenanceSleep = 5;

// Basic and ugly wait for maintenance mode to be turned off during a deploy.
//
// @see https://reactphp.org/promise-timer/#sleep Can we instead implement this
//   using ReactPHP? Attempted using ChildProcess but that didn't seem to work
//   as expected, was not calling the stdout/stderr callbacks at all.
if ($maintenance === true) {

  print 'Waiting for maintenance mode to be turned off.' . "\n";

  do {

    if (\trim(\exec('drush maint:get')) === '0') {

      $maintenance = false;

      print 'Maintenance mode has been turned off; continuing run.' . "\n";

      break;

    }

    print \sprintf(
      'Sleeping for %d seconds.',
      $maintenanceSleep,
    ) . "\n";

    sleep($maintenanceSleep);

  } while (true);

}

// Run cron every 15 minutes.
$loop->addPeriodicTimer(900, function(): void {
  (new WorkerProcess('drush cron'))->start();
});

// Run queues 60 seconds after starting, then every ten minutes after that.
new LeadingPeriodicTimer($loop, 60, 600, function(): void {

  /** @var boolean Static flag indicating whether a run is currently in progress. */
  static $running = false;

  if ($running === true) {
    return;
  }

  $running = true;

  series([
    function(): PromiseInterface {
      return (new WorkerProcess('drush warmer:enqueue ' . \implode(',', [
        'omnipedia_wiki_node_changes',
        'omnipedia_wiki_node_cdn',
      ])))->start()->promise();
    },
    function(): PromiseInterface {
      return (new WorkerProcess(
        'drush queue:process image_style_warmer_pregenerator'
      ))->start()->promise();
    },
    function(): PromiseInterface {
      return (new WorkerProcess(
        'drush queue:process warmer'
      ))->start()->promise();
    },
  ])->always(function() use (&$running) {

    $running = false;

  });

});
