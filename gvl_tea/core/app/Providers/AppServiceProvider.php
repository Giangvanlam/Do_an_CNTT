<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\BasicSetting as BS;
use App\Models\Social;
use App\Models\Language;
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
//        if (env('APP_ENV') !== 'local') {
//            $this->app['request']->server->set('HTTPS', true);
//        }

        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        if(config('app.env') === 'production') {
//            \URL::forceScheme('https');
//        }

        $socials = Social::orderBy('serial_number', 'ASC')->get();
        $langs = Language::all();

        view()->composer('*', function ($view)
        {
            
            if (session()->has('lang')) {
                $currentLang = Language::where('code', session()->get('lang'))->first();
            } else {
                
                $currentLang = Language::where('is_default', 1)->first();
            }
            
            $bs = $currentLang->basic_setting;
            
            $be = $currentLang->basic_extended;
            if ($currentLang->rtl == 1) {
                $rtl = 1;
            } else {
                $rtl = 0;
            }

            $view->with('bs', $bs );
            $view->with('be', $be );
            $view->with('currentLang', $currentLang );
            $view->with('rtl', $rtl );
        });
        View::share('langs', $langs);
        View::share('socials', $socials);
    }
}
