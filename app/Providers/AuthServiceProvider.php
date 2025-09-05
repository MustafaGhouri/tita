<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Model => Policy mapping (optional; auto-discovery bhi hoti hai)
     */
    protected $policies = [
        // \App\Models\Employee::class => \App\Policies\EmployeePolicy::class,
        // \App\Models\MysteryEvaluation::class => \App\Policies\MysteryEvaluationPolicy::class,
        // \App\Models\CoachingSession::class => \App\Policies\CoachingSessionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Agar policies map ki hain to:
        $this->registerPolicies();

        /**
         * ---- Gates (simple checks) ----
         */

        // 1) CLIENT-only module access (Employees, Mystery, Coaching, Reports)
        Gate::define('client-only', function (User $u) {
            // aapne User model me isClient() helper banaya hoga:
            // public function isClient(){ return $this->role === 'CLIENT'; }
            return $u->isClient();
        });

        // 2) Same-company generic check (useful jab aap global scope bypass karen)
        Gate::define('same-company', function (User $u, $model) {
            // NOTE: aapka snippet yahan truncate tha; yeh sahi version:
            return isset($model->company_id) && $u->company_id === $model->company_id;
        });

        // 3) Employee ko sirf apne diagnostic par access dena (self check)
        Gate::define('employee-self', function (User $u, $employee) {
            // aap email mapping ya kisi relation se match kara sakte hain
            return $u->role === 'EMPLOYEE'
                && $employee
                && $employee->email === $u->email;
        });
    }
}
