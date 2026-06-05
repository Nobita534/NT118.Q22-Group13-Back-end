<?php

namespace App\Providers;

use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Repositories\Contracts\BookmarkRepositoryInterface;
use App\Repositories\Contracts\CommentRepositoryInterface;
use App\Repositories\Contracts\InteractionRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Fake\FakeArticleRepository;
use App\Repositories\Eloquent\ArticleRepository as EloquentArticleRepository;
use App\Repositories\Eloquent\BookmarkRepository;
use App\Repositories\Eloquent\CommentRepository as EloquentCommentRepository;
use App\Repositories\Eloquent\InteractionRepository;
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
        $useFake = config('repositories.use_fake', false);

        if ($useFake) {
            $this->app->bind(UserRepositoryInterface::class, FakeUserRepository::class);
            $this->app->bind(CommentRepositoryInterface::class, FakeCommentRepository::class);
            // $this->app->bind(InteractionRepositoryInterface::class, FakeInteractionRepository::class);

            $this->app->singleton(ArticleRepositoryInterface::class, function ($app) use ($useFake) {
                if ($useFake) {
                    return new FakeArticleRepository(
                        new FakeCommentRepository(),
                        new FakeInteractionRepository()
                    );
                }

                return new EloquentArticleRepository();
            });
        } else {
            // Bind Eloquent (real) implementations for user only for now
            $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
            $this->app->bind(CommentRepositoryInterface::class, EloquentCommentRepository::class);
            $this->app->bind(InteractionRepositoryInterface::class, InteractionRepository::class);
            $this->app->bind(BookmarkRepositoryInterface::class, BookmarkRepository::class);

            // Bind ArticleRepositoryInterface to the real Eloquent implementation.
            // EloquentArticleRepository currently has no constructor dependencies,
            // so bind the class directly as a singleton.
            $this->app->singleton(ArticleRepositoryInterface::class, EloquentArticleRepository::class);
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
