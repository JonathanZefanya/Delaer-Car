<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;

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
		Paginator::useBootstrap();
		
        $json_data = json_decode(file_get_contents(resource_path('lang/json_admin/menu_texts.json')));
        foreach($json_data as $key=>$value) {
            define($key,$value);
        }

        $json_data1 = json_decode(file_get_contents(resource_path('lang/json_admin/admin_panel_texts.json')));
        foreach($json_data1 as $key=>$value) {
            define($key,$value);
        }

        $json_data2 = json_decode(file_get_contents(resource_path('lang/json_admin/notification_texts.json')));
        foreach($json_data2 as $key=>$value) {
            define($key,$value);
        }

        $json_data3 = json_decode(file_get_contents(resource_path('lang/json_admin/website_texts.json')));
        foreach($json_data3 as $key=>$value) {
            define($key,$value);
        }
    }
}
