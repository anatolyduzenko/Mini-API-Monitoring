<?php

namespace App\Metrics;

use AnatolyDuzenko\ConfigurablePrometheus\Contracts\MetricGroup;
use AnatolyDuzenko\ConfigurablePrometheus\DTO\MetricDefinition;
use AnatolyDuzenko\ConfigurablePrometheus\Enums\MetricType;

class JobMetrics implements MetricGroup
{
    protected $namespace = 'jobs';

    public function definitions(): array
    {
        return [
            new MetricDefinition(
                namespace: $this->namespace,
                name: 'duration_seconds',
                helpText: 'Job execution time.',
                type: MetricType::Histogram,
                labelNames: ['job'],
                buckets: [0.1, 0.3, 0.5, 1, 2.5, 5, 10, 50, 100]
            ),
            new MetricDefinition(
                namespace: $this->namespace,
                name: 'processed_total',
                helpText: 'Total processed jobs.',
                type: MetricType::Counter,
                labelNames: ['job'],
            ),
            new MetricDefinition(
                namespace: $this->namespace,
                name: 'failed_total',
                helpText: 'Total failed jobs.',
                type: MetricType::Counter,
                labelNames: ['job'],
            ),
        ];
    }
}
