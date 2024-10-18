<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AvailabilityDate;
use App\Models\User; // Assuming the phone numbers are in the users table
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SendMonthlySMS extends Command
{
    protected $signature = 'send:monthly-sms';
    protected $description = 'Send SMS on from_date every month if a record exists for the current month';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $currentDate = Carbon::now()->toDateString();
        $currentMonth = Carbon::now()->month;

        $availability = AvailabilityDate::whereMonth('from_date', $currentMonth)->first();

        if ($availability && $availability->from_date == $currentDate) {

            $users = [
                (object) ['phone' => '917002613213'],
                (object) ['phone' => '918486926661'],
            ];

            foreach ($users as $user) {
                $this->sendSMS($user->phone);
            }
        }
    }

    private function sendSMS($phoneNumber)
    {
        $url = "https://sms6.rmlconnect.net:8443/bulksms/bulksms";
        $params = [
            'username' => 'AssamTrans',
            'password' => '}yA3bI[7',
            'type' => '0',
            'dlr' => '1',
            'destination' => $phoneNumber,
            'source' => 'AGCLSC',
            'message' => 'Alert: Self-Meter Reading window is now open! Upload your reading today to avoid delays. Thank you for your cooperation! - Team AGCL',
            'entityid' => '1201159514504706254',
            'tempid' => '1207172906903948230'
        ];

        try {
            $response = Http::withOptions(['verify' => false])->get($url, $params);

            if ($response->successful()) {
                $this->info('SMS sent successfully to ' . $phoneNumber);
            } else {
                $this->error('Failed to send SMS to ' . $phoneNumber);
            }
        } catch (\Exception $e) {
            $this->error('Error sending SMS to ' . $phoneNumber . ': ' . $e->getMessage());
        }
    }
}
