<?php

namespace Tests;

use AnatolyDuzenko\ConfigurablePrometheus\Services\MetricManager;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(MetricManager::class, fn () => new class
        {
            public function set(string $namespace, string $name, $value, array $labels = []): void {}

            public function register(array $metricGroups): void {}

            public function getMetric(string $name): mixed
            {
                return null;
            }

            public function collect(): void {}
        });
    }
}
