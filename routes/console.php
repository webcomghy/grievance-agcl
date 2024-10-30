<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('grievances:forward-unaddressed')->hourly();
Schedule::command('grievances:auto-close')->hourly();
Schedule::command('send:monthly-sms')->daily();

