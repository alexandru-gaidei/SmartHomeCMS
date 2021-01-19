<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

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
        Collection::macro('paginate', function ($perPage = 15, $page = null, $options = []) {
            $options = empty($options) ? ['path' => request()->url()] : $options;
            $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
            return new LengthAwarePaginator($this->forPage($page, $perPage), $this->count(), $perPage, $page, $options);
        });
    }
}
