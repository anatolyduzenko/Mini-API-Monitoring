<?php

namespace App\Console\Commands;

use App\Jobs\CheckEndpointJob;
use App\Models\Endpoint;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

final class CheckEndpoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:check-endpoints';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to check the status of API endpoints';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $endpoints = Endpoint::all()->chunk(15);

        foreach ($endpoints as $batch) {
            Bus::batch(collect($batch)
                ->map(fn ($endpoint) => new CheckEndpointJob($endpoint)))
                ->dispatch();
        }

        $this->info('API endpoints batch check dispatched successfully!');
    }
}
