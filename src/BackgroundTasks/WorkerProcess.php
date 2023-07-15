<?php

declare(strict_types=1);

namespace Omnipedia\BackgroundTasks;

use React\ChildProcess\Process;

/**
 * Omnipedia background tasks worker process wrapper.
 */
class WorkerProcess {

  /**
   * The ReactPHP Process instance for this command.
   *
   * @var \React\ChildProcess\Process
   */
  protected Process $process;

  /**
   * Constructor; instantiates a new ReactPHP Process.
   *
   * @param string $command
   *   Command to run.
   */
  public function __construct(string $command) {

    $this->process = new Process($command);

  }

  /**
   * Start the configured process.
   */
  public function start(): void {

    print 'Running: ' . $this->process->getCommand() . \PHP_EOL;

    $this->process->start();

    $this->process->on('exit', [$this, 'onProcessExit']);

    $this->process->stdout->on('data', [$this, 'onStdOutData']);

    $this->process->stderr->on('data', [$this, 'onStdErrData']);

  }

  /**
   * Process exit event handler.
   *
   * @param int|null $exitCode
   *
   * @param int|null $termSignal
   */
  public function onProcessExit(?int $exitCode, ?int $termSignal): void {

    if ($exitCode !== null) {
      return;
    }

    print \sprintf(
      'Command %s terminated with signal: %s',
      $this->process->getCommand(), $termSignal
    );

  }

  /**
   * stdout data event handler; prints output.
   *
   * @param string $chunk
   *   Output data string/chunk. In other contexts this can also be binary data,
   *   but in our case we're only expecting a string.
   */
  public function onStdOutData(string $chunk): void {

    print $chunk;

  }

  /**
   * stderr data event handler; prints output.
   *
   * @param string $chunk
   *   Output data string/chunk. In other contexts this can also be binary data,
   *   but in our case we're only expecting a string.
   */
  public function onStdErrData(string $chunk): void {

    if (empty(trim($chunk))) {
      return;
    }

    print $chunk;

  }

}
