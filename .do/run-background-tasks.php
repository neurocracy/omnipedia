<?php

declare(strict_types=1);

use React\ChildProcess\Process;
use React\EventLoop\Loop;

require __DIR__ . '/../vendor/autoload.php';

function worker_process(string $command): void {

  print 'Running: ' . $command . \PHP_EOL;

  $process = new Process($command);

  $process->start();

  $process->on('exit', function(
    ?int $exitCode, $termSignal
  ) use ($command): void {
    if ($exitCode === null) {
      print "Command '$command' terminated with signal: $termSignal";
    }
    // print 'Process exited with code ' . $exitCode . \PHP_EOL;
  });
  $process->stdout->on('data', function($chunk): void {
    print $chunk;
  });
  $process->stderr->on('data', function($chunk) use ($command): void {
    if (!empty(trim($chunk))) {
      print $chunk;
    }
  });
  // $process->stdout->on('error', function(\Exception $exception) use ($command): void {
  //   print 'Error: ' . $exception->getMessage();
  // });
  // $process->stderr->on('error', function(\Exception $exception) use ($command): void {
  //   print 'Error: ' . $exception->getMessage();
  // });

}

$drush = __DIR__ . '/../vendor/bin/drush';

$loop = Loop::get();

// Run common tasks immediately.
$loop->futureTick(function() {
  \worker_process(__DIR__ . '/run-common.sh');
});

// Run cron every 15 minutes.
$loop->addPeriodicTimer(900, function() use ($drush): void {
  \worker_process($drush . ' cron');
});

// Every 5 minutes, process jobs from the image style warmer.
$loop->addPeriodicTimer(300, function() use ($drush): void {
  \worker_process(
    $drush . ' queue:run image_style_warmer_pregenerator --verbose'
  );
});

// Every 10 minutes, process the wiki page changes queue.
$loop->addPeriodicTimer(600, function() use ($drush): void {
  \worker_process($drush . ' omnipedia:changes-build --verbose');
});

// Every 10 minutes, process the wiki page CDN warmer queue.
$loop->addPeriodicTimer(600, function() use ($drush): void {
  \worker_process(
    $drush . ' warmer:enqueue omnipedia_wiki_node_cdn --run-queue --verbose'
  );
});

$loop->run();
