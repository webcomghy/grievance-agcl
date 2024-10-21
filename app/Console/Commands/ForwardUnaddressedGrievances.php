<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Grievance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ForwardUnaddressedGrievances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grievances:forward-unaddressed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forward grievances that have not been addressed in 48 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $grievances = Grievance::where('status', 'Pending')
            ->where('updated_at', '<', Carbon::now()->subHours(24))
            ->whereDoesntHave('transactions')
            ->get();

        if ($grievances->isEmpty()) {
            $this->info('No unaddressed grievances found.');
            return;
        }

        foreach ($grievances as $grievance) {
            $this->forwardGrievance($grievance);
        }

        $this->info('Unaddressed grievances have been forwarded.');
    }

    private function forwardGrievance(Grievance $grievance)
    {
        // Assuming you have a way to determine the higher authority
        $admin = User::where('username', 'nodal_officer')->first();

        if ($admin) {
            try {
                DB::beginTransaction();

                $grievance->update([
                    'status' => 'Forwarded',
                ]);

                // Create a new grievance transaction
                $grievance->transactions()->create([
                    'status' => 'Forwarded',
                    'description' => 'Automatically forwarded due to inactivity for 24 hours',
                    'assigned_to' => $admin->id,
                    'created_by' => $admin->id, // Assuming 1 is the system user ID
                ]);

                DB::commit();
                $this->line("Grievance ID {$grievance->id} forwarded to higher authority.");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Failed to forward Grievance ID {$grievance->id}: " . $e->getMessage());
            }
        } else {
            $this->warn("Admin user not found. Grievance ID {$grievance->id} not forwarded.");
        }
    }
}
