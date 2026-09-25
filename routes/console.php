<?php

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('add:department', function () {
    for ($i = 0; $i < 26; $i++) {
        Department::create(
            [
                'name' => '営業'.$i.'部',
            ]
        );
    }
})->purpose('Add department sample record');

Artisan::command('add:employee', function () {
    for ($i = 0; $i < 26; $i++) {
        Employee::create(
            [
                'name' => 'test user:'.$i,
                'email' => 'test'.$i.'@test.com',
            ]
        );
    }
})->purpose('Add employee sample record');

// Schedule::command('cli:employee_add')->dailyAt('0:25');
