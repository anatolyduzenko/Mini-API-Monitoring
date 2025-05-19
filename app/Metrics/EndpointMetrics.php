<?php

namespace App\Metrics;

use AnatolyDuzenko\ConfigurablePrometheus\Contracts\MetricGroup;
use AnatolyDuzenko\ConfigurablePrometheus\DTO\MetricDefinition;
use AnatolyDuzenko\ConfigurablePrometheus\Enums\MetricType;

class EndpointMetrics implements MetricGroup
{
    protected $namespace = 'endpoints';

    public function definitions(): array
    {
        return [
            new MetricDefinition(
                namespace: $this->namespace,
                name: 'total',
                helpText: 'Total number of endpoints.',
                type: MetricType::Gauge,
                labelNames: ['total'],
            ),
            new MetricDefinition(
                namespace: $this->namespace,
                name: 'checks_total',
                helpText: 'Total number of endpoints checks.',
                type: MetricType::Counter,
                labelNames: ['total'],
            ),
            new MetricDefinition(
                namespace: $this->namespace,
                name: 'response_time_seconds',
                helpText: 'API response time in seconds.',
                type: MetricType::Histogram,
                labelNames: ['method', 'code'],
                buckets: [0.1, 0.3, 0.5, 1, 2.5, 5, 10, 50, 100]
            ),
            new MetricDefinition(
                namespace: $this->namespace,
                name: 'response_codes',
                helpText: 'API response codes.',
                type: MetricType::Counter,
                labelNames: ['code']
            ),
        ];
    }
}
