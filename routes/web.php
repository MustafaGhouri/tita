<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{DashboardController,EmployeeController,MysteryEvaluationController,DiagnosticController,CoachingSessionController};
// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/', fn() => redirect()->route('dashboard'))->middleware('auth');
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Employees (CLIENT only inside controller)
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/toggle', [EmployeeController::class,'toggle'])->name('employees.toggle');

    // Mystery Shopper
    Route::get('employees/{employee}/mystery/create', [MysteryEvaluationController::class,'create'])->name('mystery.create');
    Route::post('employees/{employee}/mystery', [MysteryEvaluationController::class,'store'])->name('mystery.store');
    Route::get('mystery/{evaluation}', [MysteryEvaluationController::class,'show'])->name('mystery.show');
    Route::get('mystery/{evaluation}/pdf', [MysteryEvaluationController::class,'pdf'])->name('mystery.pdf');

    // Diagnostic
    Route::get('employees/{employee}/diagnostic', [DiagnosticController::class,'index'])->name('diagnostic.index');
    Route::get('employees/{employee}/diagnostic/start', [DiagnosticController::class,'start'])->name('diagnostic.start');
    Route::post('employees/{employee}/diagnostic/submit', [DiagnosticController::class,'submit'])->name('diagnostic.submit');
    Route::get('employees/{employee}/diagnostic/view', [DiagnosticController::class,'view'])->name('diagnostic.view');
    Route::get('employees/{employee}/diagnostic/pdf', [DiagnosticController::class,'pdf'])->name('diagnostic.pdf');

    // Coaching
    Route::get('employees/{employee}/coaching', [CoachingSessionController::class,'index'])->name('coaching.index');
    Route::get('employees/{employee}/coaching/create', [CoachingSessionController::class,'create'])->name('coaching.create');
    Route::post('employees/{employee}/coaching', [CoachingSessionController::class,'store'])->name('coaching.store');
    Route::get('employees/{employee}/coaching/{session}/edit', [CoachingSessionController::class,'edit'])->name('coaching.edit');
    Route::put('employees/{employee}/coaching/{session}', [CoachingSessionController::class,'update'])->name('coaching.update');
});


require __DIR__.'/auth.php';
