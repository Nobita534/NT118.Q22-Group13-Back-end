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
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $useFake = config('repositories.use_fake', true);

        if ($useFake) {
            // Bind User repository (real/eloquent)
            $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

            // The following repository bindings are commented out temporarily
            // because their implementations are not yet created.
            // $this->app->bind(CommentRepositoryInterface::class, FakeCommentRepository::class);
            // $this->app->bind(InteractionRepositoryInterface::class, FakeInteractionRepository::class);

            // Article repository depends on Comment/Interaction repositories.
            // Temporarily disabled until those repositories are implemented.
            // $this->app->singleton(ArticleRepositoryInterface::class, function ($app) {
            //     return new FakeArticleRepository(
            //         $app->make(CommentRepositoryInterface::class),
            //         $app->make(InteractionRepositoryInterface::class)
            //     );
            // });
        } else {
            // Bind Eloquent (real) implementations for user only for now
            $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

            // Temporarily disable other bindings until implementations exist
            // $this->app->bind(CommentRepositoryInterface::class, EloquentCommentRepository::class);
            // $this->app->bind(InteractionRepositoryInterface::class, EloquentInteractionRepository::class);

            // $this->app->singleton(ArticleRepositoryInterface::class, function ($app) {
            //     return new EloquentArticleRepository(
            //         $app->make(CommentRepositoryInterface::class),
            //         $app->make(InteractionRepositoryInterface::class)
            //     );
            // });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
