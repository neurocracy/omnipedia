<?php

declare(strict_types=1);

namespace Omnipedia\BackgroundTasks;

use React\EventLoop\LoopInterface;
use Closure;

/**
 * Background tasks periodic timer with a leading invocation.
 *
 * This reduces code duplication by invoking a callable both at the start (hence
 * "leading") and at a periodic interval thereafter.
 */
class LeadingPeriodicTimer {

  /**
   * A Closure object representing the callable to call.
   */
  protected Closure $callable;

  /**
   * Constructor.
   *
   * @param \React\EventLoop\LoopInterface $loop
   *   The ReactPHP loop.
   *
   * @param float|int $initialDelay
   *   The initial delay before running the first invocation of the callback.
   *   This also delays the $interval. If this is 0, the first invocation of the
   *   callback will occur on the next tick of the loop with the periodic timer
   *   added on that same tick.
   *
   * @param float|int $interval
   *   The interval for the periodic timer. If $initialDelay is greater than 0,
   *   the first periodic timer invocation will be $initialDelay plus $interval.
   *
   * @param callable $callable
   *   The callable
   */
  public function __construct(
    protected readonly LoopInterface  $loop,
    protected readonly float|int      $initialDelay,
    protected readonly float|int      $interval,
    callable $callable,
  ) {

    // @see https://stackoverflow.com/questions/57935734/is-type-callable-supported-with-typed-properties
    $this->callable = Closure::fromCallable($callable);

    if ($initialDelay === 0) {

      $this->addTimers();

    } else {

      $this->loop->addTimer($this->initialDelay, [$this, 'addTimers']);

    }

  }

  /**
   * Add the leading invocation and periodic timer.
   */
  public function addTimers(): void {

    $this->loop->futureTick($this->callable);

    $this->loop->addPeriodicTimer($this->interval, $this->callable);

  }

}
