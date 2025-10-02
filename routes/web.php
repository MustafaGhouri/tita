<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\{
    DashboardController,
    EmployeeController,
    MysteryEvaluationController,
    DiagnosticController,
    CoachingSessionController,
    MysteryShopperController
};

// util
Route::get('/optimize', function () {
    Artisan::call('optimize', ['--quiet' => true]);
    return 'Optimize Successfully!';
});

Route::get('/', fn () => redirect()->route('dashboard'))->middleware('auth');

Route::middleware('auth')->group(function () {
    /* Profile */
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');

    /* Dashboard */
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    /* Employees (CLIENT-only is enforced in controller middleware) */
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/toggle', [EmployeeController::class,'toggle'])->name('employees.toggle');

    /* Mystery Shopper directory + employee profile */
    Route::prefix('mystery')->name('mystery.')->group(function () {
        Route::get('/', [MysteryShopperController::class, 'index'])->name('index');                 // list employees
        Route::get('/employee/{employee}', [MysteryShopperController::class, 'employee'])->name('employee'); // profile page
    });

    /* Mystery Evaluations (secured with company code) */
    // 🔐 verify company code -> set one-time unlock -> redirect to create
    Route::post('employees/{employee}/mystery/unlock', [MysteryEvaluationController::class,'unlock'])->name('mystery.unlock');

    // show create form (only if unlocked in session)
    Route::get('employees/{employee}/mystery/create', [MysteryEvaluationController::class,'create'])->name('mystery.create');

    // submit evaluation (consumes unlock)
    Route::post('employees/{employee}/mystery', [MysteryEvaluationController::class,'store'])->name('mystery.store');

    // view & pdf
    Route::get('mystery/{evaluation}', [MysteryEvaluationController::class,'show'])->name('mystery.show');
    Route::get('mystery/{evaluation}/pdf', [MysteryEvaluationController::class,'pdf'])->name('mystery.pdf');

    /* Diagnostic */
    Route::get('employees/{employee}/diagnostic',           [DiagnosticController::class,'index'])->name('diagnostic.index');
    Route::get('employees/{employee}/diagnostic/start',     [DiagnosticController::class,'start'])->name('diagnostic.start');
    Route::post('employees/{employee}/diagnostic/submit',   [DiagnosticController::class,'submit'])->name('diagnostic.submit');
    Route::get('employees/{employee}/diagnostic/view',      [DiagnosticController::class,'view'])->name('diagnostic.view');
    Route::get('employees/{employee}/diagnostic/pdf',       [DiagnosticController::class,'pdf'])->name('diagnostic.pdf');

    /* Coaching */
    Route::get('employees/{employee}/coaching',                 [CoachingSessionController::class,'index'])->name('coaching.index');
    Route::get('employees/{employee}/coaching/create',          [CoachingSessionController::class,'create'])->name('coaching.create');
    Route::post('employees/{employee}/coaching',                [CoachingSessionController::class,'store'])->name('coaching.store');
    Route::get('employees/{employee}/coaching/{session}/edit',  [CoachingSessionController::class,'edit'])->name('coaching.edit');
    Route::put('employees/{employee}/coaching/{session}',       [CoachingSessionController::class,'update'])->name('coaching.update');


     Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        return "Cache cleared successfully!";
    });
});

require __DIR__.'/auth.php';
