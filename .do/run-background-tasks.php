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
 * Check if we're currently deploying.
 *
 * @return boolean
 *   True if a deployment is currenty running; false otherwise.
 */
function isDeploymentActive(): bool {

  // The drush state:get command returns an empty value if the key isn't set,
  // so we don't need to check for that directly.
  return \trim(\exec(
    'drush state:get digitalocean.deployment_active',
  )) === 'true';

}

/**
 * True if a deployment is currently running; false otherwise.
 *
 * @var boolean
 */
$isDeploymentActive = isDeploymentActive();

/**
 * Amount of time to sleep between checks for deployment to finish.
 *
 * @var integer
 */
$deploymentActiveSleep = 5;

// Basic and ugly wait for a deployment to finish.
//
// @see https://reactphp.org/promise-timer/#sleep Can we instead implement this
//   using ReactPHP? Attempted using ChildProcess but that didn't seem to work
//   as expected, was not calling the stdout/stderr callbacks at all.
if ($isDeploymentActive === true) {

  print 'Waiting for deployment to finish.' . "\n";

  do {

    $isDeploymentActive = isDeploymentActive();

    if ($isDeploymentActive === false) {

      print 'Deployment finished; continuing run.' . "\n";

      break;

    }

    print \sprintf(
      'Still deploying; sleeping for %d seconds.',
      $deploymentActiveSleep,
    ) . "\n";

    sleep($deploymentActiveSleep);

  } while (true);

}

// Run cron every 15 minutes.
$loop->addPeriodicTimer(900, function(): void {
  (new WorkerProcess('drush cron'))->start();
});

// Run queues 10 seconds after starting, then every ten minutes after that.
new LeadingPeriodicTimer($loop, 10, 600, function(): void {

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

    print 'Queue run complete.' . "\n";

    $running = false;

  });

});
