<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Validator;
use Hash;
use Auth;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('old_password', function ($attribute, $value) {
            if(!Hash::check($value, Auth::user()->password)){
                return false;
            }
            else{
                return true;
            }
        });
    }
}
