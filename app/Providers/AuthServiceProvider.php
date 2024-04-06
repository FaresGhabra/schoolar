<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\SchoolAccounts\OnlineStudent;
use App\Models\SchoolAccounts\Student;
use App\Models\SchoolAccounts\StuParent;
use App\Policies\OnlineStudentProfilePolicy;
use App\Policies\StudentProfilePolicy;
use App\Policies\StuParentProfilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        OnlineStudent::class => OnlineStudentProfilePolicy::class,
        Student::class => StudentProfilePolicy::class,
        StuParent::class => StuParentProfilePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
