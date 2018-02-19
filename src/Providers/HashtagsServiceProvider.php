<?php

namespace InetStudio\Hashtags\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/**
 * Class HashtagsServiceProvider.
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
        $this->registerEvents();
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
                'InetStudio\Hashtags\Console\Commands\CreateFoldersCommand',
                'InetStudio\Hashtags\Console\Commands\SearchInstagramPostsByTagCommand',
                'InetStudio\Hashtags\Console\Commands\SearchVkontaktePostsByTagCommand',
                'InetStudio\Hashtags\Console\Commands\SetupCommand',
                'InetStudio\Hashtags\Console\Commands\StatusesSeedCommand',
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
            __DIR__.'/../../public' => public_path(),
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
     * Регистрация событий.
     *
     * @return void
     */
    protected function registerEvents(): void
    {
        Event::listen('InetStudio\Hashtags\Contracts\Events\Posts\ModifyPostEventContract', 'InetStudio\Hashtags\Contracts\Listeners\Back\Posts\ClearPostsCacheListenerContract');
    }

    /**
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    protected function registerBindings(): void
    {
        // Controllers
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Points\PointsControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Points\PointsController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Points\PointsDataControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Points\PointsDataController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Posts\PostsControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Posts\PostsController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Posts\PostsDataControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Posts\PostsDataController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Posts\PostsModerationControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Posts\PostsModerationController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Posts\PostsUtilityControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Posts\PostsUtilityController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Prizes\PrizesControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Prizes\PrizesController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Prizes\PrizesDataControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Prizes\PrizesDataController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Prizes\PrizesUtilityControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Prizes\PrizesUtilityController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Stages\StagesControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Stages\StagesController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Stages\StagesDataControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Stages\StagesDataController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Stages\StagesUtilityControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Stages\StagesUtilityController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Statuses\StatusesControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Statuses\StatusesController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Statuses\StatusesDataControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Statuses\StatusesDataController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Statuses\StatusesUtilityControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Statuses\StatusesUtilityController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Tags\TagsControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Tags\TagsController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Tags\TagsDataControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Tags\TagsDataController');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Controllers\Back\Tags\TagsUtilityControllerContract', 'InetStudio\Hashtags\Http\Controllers\Back\Tags\TagsUtilityController');

        // Events
        $this->app->bind('InetStudio\Hashtags\Contracts\Events\Points\ModifyPointEventContract', 'InetStudio\Hashtags\Events\Points\ModifyPointEvent');
        $this->app->bind('InetStudio\Hashtags\Contracts\Events\Posts\ModifyPostEventContract', 'InetStudio\Hashtags\Events\Posts\ModifyPostEvent');
        $this->app->bind('InetStudio\Hashtags\Contracts\Events\Prizes\ModifyPrizeEventContract', 'InetStudio\Hashtags\Events\Prizes\ModifyPrizeEvent');
        $this->app->bind('InetStudio\Hashtags\Contracts\Events\Stages\ModifyStageEventContract', 'InetStudio\Hashtags\Events\Stages\ModifyStageEvent');
        $this->app->bind('InetStudio\Hashtags\Contracts\Events\Statuses\ModifyStatusEventContract', 'InetStudio\Hashtags\Events\Statuses\ModifyStatusEvent');
        $this->app->bind('InetStudio\Hashtags\Contracts\Events\Tags\ModifyTagEventContract', 'InetStudio\Hashtags\Events\Tags\ModifyTagEvent');

        // Listeners
        $this->app->bind('InetStudio\Hashtags\Contracts\Listeners\Back\Posts\ClearPostsCacheListenerContract', 'InetStudio\Hashtags\Listeners\Back\Posts\ClearPostsCacheListener');

        // Requests
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Requests\Back\Points\SavePointRequestContract', 'InetStudio\Hashtags\Http\Requests\Back\Points\SavePointRequest');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Requests\Back\Posts\SavePostRequestContract', 'InetStudio\Hashtags\Http\Requests\Back\Posts\SavePostRequest');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Requests\Back\Prizes\SavePrizeRequestContract', 'InetStudio\Hashtags\Http\Requests\Back\Prizes\SavePrizeRequest');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Requests\Back\Stages\SaveStageRequestContract', 'InetStudio\Hashtags\Http\Requests\Back\Stages\SaveStageRequest');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Requests\Back\Statuses\SaveStatusRequestContract', 'InetStudio\Hashtags\Http\Requests\Back\Statuses\SaveStatusRequest');
        $this->app->bind('InetStudio\Hashtags\Contracts\Http\Requests\Back\Tags\SaveTagRequestContract', 'InetStudio\Hashtags\Http\Requests\Back\Tags\SaveTagRequest');

        // Services
        $this->app->bind('InetStudio\Hashtags\Contracts\Services\Back\Points\PointsDataTableServiceContract', 'InetStudio\Hashtags\Services\Back\Points\PointsDataTableService');
        $this->app->bind('InetStudio\Hashtags\Contracts\Services\Back\Posts\ContestPostsServiceContract', 'InetStudio\Hashtags\Services\Back\Posts\ContestPostsService');
        $this->app->bind('InetStudio\Hashtags\Contracts\Services\Back\Posts\PostsDataTableServiceContract', 'InetStudio\Hashtags\Services\Back\Posts\PostsDataTableService');
        $this->app->bind('InetStudio\Hashtags\Contracts\Services\Back\Prizes\PrizesDataTableServiceContract', 'InetStudio\Hashtags\Services\Back\Prizes\PrizesDataTableService');
        $this->app->bind('InetStudio\Hashtags\Contracts\Services\Back\Stages\StagesDataTableServiceContract', 'InetStudio\Hashtags\Services\Back\Stages\StagesDataTableService');
        $this->app->bind('InetStudio\Hashtags\Contracts\Services\Back\Statuses\StatusesDataTableServiceContract', 'InetStudio\Hashtags\Services\Back\Statuses\StatusesDataTableService');
        $this->app->bind('InetStudio\Hashtags\Contracts\Services\Back\Tags\TagsDataTableServiceContract', 'InetStudio\Hashtags\Services\Back\Tags\TagsDataTableService');
        $this->app->bind('InetStudio\Hashtags\Contracts\Services\Front\Posts\ContestPostsServiceContract', 'InetStudio\Hashtags\Services\Front\Posts\ContestPostsService');

        // Transformers
        $this->app->bind('InetStudio\Hashtags\Contracts\Transformers\Back\Points\PointTransformerContract', 'InetStudio\Hashtags\Transformers\Back\Points\PointTransformer');
        $this->app->bind('InetStudio\Hashtags\Contracts\Transformers\Back\Posts\PostTransformerContract', 'InetStudio\Hashtags\Transformers\Back\Posts\PostTransformer');
        $this->app->bind('InetStudio\Hashtags\Contracts\Transformers\Back\Prizes\PrizeTransformerContract', 'InetStudio\Hashtags\Transformers\Back\Prizes\PrizeTransformer');
        $this->app->bind('InetStudio\Hashtags\Contracts\Transformers\Back\Stages\StageTransformerContract', 'InetStudio\Hashtags\Transformers\Back\Stages\StageTransformer');
        $this->app->bind('InetStudio\Hashtags\Contracts\Transformers\Back\Statuses\StatusTransformerContract', 'InetStudio\Hashtags\Transformers\Back\Statuses\StatusTransformer');
        $this->app->bind('InetStudio\Hashtags\Contracts\Transformers\Back\Tags\TagTransformerContract', 'InetStudio\Hashtags\Transformers\Back\Tags\TagTransformer');
    }
}
