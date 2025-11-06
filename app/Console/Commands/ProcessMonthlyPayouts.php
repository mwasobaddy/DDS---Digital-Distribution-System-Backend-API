<?php

namespace App\Console\Commands;

use App\Models\Earning;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessMonthlyPayouts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payouts:process-monthly {--period= : The period to process (YYYY-MM format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process monthly payouts for the specified period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $period = $this->option('period') ?: now()->subMonth()->format('Y-m');

        $this->info("Processing payouts for period: {$period}");

        DB::transaction(function () use ($period) {
            // Get all pending earnings for the period
            $earnings = Earning::where('period', $period)
                ->where('status', 'pending')
                ->with('user')
                ->get();

            if ($earnings->isEmpty()) {
                $this->info("No pending earnings found for period {$period}");
                return;
            }

            // Group by user
            $userEarnings = $earnings->groupBy('user_id');

            $totalUsers = 0;
            $totalAmount = 0;

            foreach ($userEarnings as $userId => $userEarningRecords) {
                $user = $userEarningRecords->first()->user;
                $userTotal = $userEarningRecords->sum('amount');

                // Mark earnings as paid
                Earning::whereIn('id', $userEarningRecords->pluck('id'))
                    ->update(['status' => 'paid']);

                $totalUsers++;
                $totalAmount += $userTotal;

                Log::info("Processed payout", [
                    'user_id' => $userId,
                    'user_name' => $user->name,
                    'amount' => $userTotal,
                    'period' => $period,
                ]);

                $this->line("Processed payout for {$user->name}: \${$userTotal}");
            }

            $this->info("Successfully processed payouts for {$totalUsers} users totaling \${$totalAmount}");
        });
    }
}
