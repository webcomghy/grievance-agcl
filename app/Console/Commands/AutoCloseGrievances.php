<?php

namespace App\Console\Commands;

use App\Models\Grievance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCloseGrievances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grievances:auto-close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark Grievances as closed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admin = User::where('username', 'admin')->first();

        try {
            // Start a database transaction
            DB::beginTransaction();

            $grievances = Grievance::where('status', 'Resolved') // Corrected the typo 'Resloved' to 'Resolved'
                ->where('updated_at', '<', Carbon::now()->subHours(24))
                ->get();

            if ($grievances->isEmpty()) {
                $this->info('No unaddressed grievances found.');
                return;
            }

            foreach ($grievances as $grievance) {
                $grievance->status = 'Closed';
                $grievance->save();

                $grievance->transactions()->create([
                    'status' => 'Closed',
                    'description' => 'Automatically closed due to inactivity for 24 hours',
                    'created_by' => $admin->id,
                    'assigned_to' => 0,
                ]);
            }

            // Commit the transaction
            DB::commit();

            $this->info('Unaddressed grievances have been closed.');
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();

            // Log the error message to the console and log file
            $this->error('An error occurred while closing grievances: ' . $e->getMessage());
            Log::error('AutoCloseGrievances Error: ' . $e->getMessage());
        }
    }
}
