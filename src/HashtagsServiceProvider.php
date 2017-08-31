<?php

namespace InetStudio\Hashtags;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class HashtagsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/hashtags.php' => config_path('hashtags.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../public' => public_path(),
        ], 'public');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'admin.module.hashtags');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->mergeConfigFrom(
            __DIR__.'/../config/filesystems.php', 'filesystems.disks'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\SetupCommand::class,
                Commands\CreateFoldersCommand::class,
                Commands\SearchInstagramPostsByTagCommand::class,
                Commands\SearchVkontaktePostsByTagCommand::class,
            ]);

            if (! class_exists('CreateHashtagsTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../database/migrations/create_hashtags_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_hashtags_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register('InetStudio\Instagram\InstagramServiceProvider');
        $this->app->register('InetStudio\Vkontakte\VkontakteServiceProvider');

        $this->app->register('Chumper\Zipper\ZipperServiceProvider');
        $this->app->register('Maatwebsite\Excel\ExcelServiceProvider');
        $this->app->register('Yajra\Datatables\DatatablesServiceProvider');
        $this->app->register('Yajra\Datatables\HtmlServiceProvider');

        $loader = AliasLoader::getInstance();
        $loader->alias('Zipper', 'Chumper\Zipper\Zipper');
        $loader->alias('Excel', 'Maatwebsite\Excel\Facades\Excel');
    }
}
