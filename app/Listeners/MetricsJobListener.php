<?php

namespace App\Listeners;

use AnatolyDuzenko\ConfigurablePrometheus\Contracts\MetricManagerInterface;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Events\Attributes\AsEventListener;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;

class MetricsJobListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected MetricManagerInterface $metrics) {}

    #[AsEventListener]
    public function handleJobProcessed(JobProcessed $event)
    {
        $duration = microtime(true) - LARAVEL_START;
        $this->metrics->observe('jobs', 'duration_seconds', $duration, [self::getJobName($event->job)]);
        $this->metrics->inc('jobs', 'processed_total', [self::getJobName($event->job)]);
    }

    #[AsEventListener]
    public function handleJobFailed(JobFailed $event)
    {
        $this->metrics->inc('jobs', 'failed_total', [self::getJobName($event->job)]);
    }

    private static function getJobName(Job $job): string
    {
        return class_basename($job->resolveName() ?? $job->getName());
    }
}
