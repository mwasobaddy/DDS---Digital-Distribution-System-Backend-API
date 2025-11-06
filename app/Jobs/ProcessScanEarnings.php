<?php

namespace App\Jobs;

use App\Models\Scan;
use App\Models\DCD;
use App\Models\DA;
use App\Models\Earning;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessScanEarnings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $scanId
    ) {}

    public function handle(): void
    {
        DB::transaction(function () {
            $scan = Scan::findOrFail($this->scanId);

            // Prevent double-processing
            if ($scan->earnings_processed) {
                return;
            }

            $dcd = DCD::findOrFail($scan->dcd_id);
            $earningsAmount = $scan->earnings_amount;

            // 1. Award earnings to DCD
            Earning::create([
                'user_id' => $dcd->user_id,
                'scan_id' => $scan->id,
                'amount' => $earningsAmount,
                'type' => 'scan',
                'status' => 'pending',
                'period' => now()->format('Y-m'),
            ]);

            Log::info("DCD earnings recorded", [
                'dcd_id' => $dcd->id,
                'amount' => $earningsAmount
            ]);

            // 2. Award 5% commission to referring DA (if exists)
            if ($dcd->referring_da_id) {
                $da = DA::find($dcd->referring_da_id);

                if ($da) {
                    $commission = $earningsAmount * 0.05;

                    Earning::create([
                        'user_id' => $da->user_id,
                        'scan_id' => $scan->id,
                        'amount' => $commission,
                        'type' => 'commission',
                        'status' => 'pending',
                        'period' => now()->format('Y-m'),
                    ]);

                    Log::info("DA commission recorded", [
                        'da_id' => $da->id,
                        'dcd_id' => $dcd->id,
                        'commission' => $commission
                    ]);
                }
            }

            // 3. Mark scan as processed
            $scan->update(['earnings_processed' => true]);
        });
    }
}