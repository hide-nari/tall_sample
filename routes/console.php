<?php

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('cli:department_add', function () {
    for ($i = 0; $i < 10; $i++) {
        Department::create(
            [
                'name' => 'CLI make dept:'.now(),
            ]
        );
    }
})->purpose('Add department sample record');

Artisan::command('cli:employee_add', function () {
    for ($i = 0; $i < 10; $i++) {
        Employee::create(
            [
                'name' => 'CLI make name:'.now(),
                'email' => 'test'.random_int(10, 99).'@test.com',
            ]
        );
    }
})->purpose('Add employee sample record');
