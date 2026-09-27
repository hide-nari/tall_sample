<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('/departments', 'departments')
        ->name('departments.index');
    Route::livewire('/department/{department?}', 'departments.show')
        ->name('department.show');
    Route::livewire('/employees', 'employees')
        ->name('employees.index');
    Route::livewire('/employee/image/{employee?}', 'employees.image')
        ->name('employee.image');
    Route::livewire('/employee/{employee?}', 'employees.show')
        ->name('employee.show');
});

require __DIR__.'/settings.php';
