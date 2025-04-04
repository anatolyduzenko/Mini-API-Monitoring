<?php

namespace App\Jobs;

use App\Models\Endpoint;
use App\Notifications\EndpointFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EvaluateUptimeJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected Endpoint $endpoint;

    /**
     * Create a new job instance.
     */
    public function __construct(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $from = Carbon::now()->subMinutes(20);

        $logs = DB::table('endpoint_logs')
            ->where('endpoint_id', $this->endpoint->id)
            ->where('created_at', '>=', $from)
            ->get();

        $total = $logs->count();
        $success = $logs->whereBetween('status_code', [200, 399])->count();

        if ($total === 0) {
            return;
        }

        $uptime = ($success / $total) * 100;

        $threshold = $this->endpoint->alert_threshold ?? 90;

        if ($uptime < $threshold) {
            $cacheKey = "endpoint:{$this->endpoint->id}:notified";

            if (! Cache::has($cacheKey)) {
                $this->endpoint->user?->notify(new EndpointFailed($this->endpoint));
                // Prevent to send notifications for already checked
                Cache::put($cacheKey, true, now()->addMinutes(30));
            }
        }
    }
}
