<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Infrastructure\Repositories\Contracts\PostRepositoryInterface::class,
            \App\Infrastructure\Repositories\Eloquent\EloquentPostRepository::class
        );

        $this->app->bind(
            \App\Infrastructure\Repositories\Contracts\CommentRepositoryInterface::class,
            \App\Infrastructure\Repositories\Eloquent\EloquentCommentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
