<?php

namespace InetStudio\Hashtags\Providers;

use Illuminate\Support\ServiceProvider;
use InetStudio\Hashtags\Console\Commands\SetupCommand;
use InetStudio\Hashtags\Services\Back\ContestPostsService;
use InetStudio\Hashtags\Console\Commands\StatusesSeedCommand;
use InetStudio\Hashtags\Console\Commands\CreateFoldersCommand;
use InetStudio\Hashtags\Console\Commands\SearchInstagramPostsByTagCommand;
use InetStudio\Hashtags\Console\Commands\SearchVkontaktePostsByTagCommand;
use InetStudio\Hashtags\Contracts\Services\Back\ContestPostsServiceContract;

/**
 * Class HashtagsServiceProvider
 * @package InetStudio\Hashtags\Providers
 */
class HashtagsServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
        $this->registerRoutes();
        $this->registerViews();
    }

    /**
     * Регистрация привязки в контейнере.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
    }

    /**
     * Регистрация команд.
     *
     * @return void
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateFoldersCommand::class,
                SearchInstagramPostsByTagCommand::class,
                SearchVkontaktePostsByTagCommand::class,
                SetupCommand::class,
                StatusesSeedCommand::class,
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     *
     * @return void
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../config/hashtags.php' => config_path('hashtags.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/filesystems.php', 'filesystems.disks'
        );

        $this->publishes([
            __DIR__ . '/../../public' => public_path(),
        ], 'public');

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateHashtagsTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_hashtags_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_hashtags_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Регистрация путей.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }

    /**
     * Регистрация представлений.
     *
     * @return void
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'admin.module.hashtags');
    }

    /**
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    protected function registerBindings(): void
    {
        $this->app->bind(ContestPostsServiceContract::class, ContestPostsService::class);
    }
}
