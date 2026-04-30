<?php

namespace App\Providers;

use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Repositories\Contracts\CommentRepositoryInterface;
use App\Repositories\Contracts\InteractionRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Fake\FakeArticleRepository;
use App\Repositories\Fake\FakeCommentRepository;
use App\Repositories\Fake\FakeInteractionRepository;
use App\Repositories\Fake\FakeUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, FakeUserRepository::class);
        $this->app->bind(CommentRepositoryInterface::class, FakeCommentRepository::class);
        $this->app->bind(InteractionRepositoryInterface::class, FakeInteractionRepository::class);

        $this->app->singleton(ArticleRepositoryInterface::class, function ($app) {
            return new FakeArticleRepository(
                $app->make(CommentRepositoryInterface::class),
                $app->make(InteractionRepositoryInterface::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
