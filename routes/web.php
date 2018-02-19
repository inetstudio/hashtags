<?php

Route::group([
    'middleware' => ['web', 'back.auth'],
    'prefix' => 'back/hashtags'
], function () {
    Route::group([
        'namespace' => 'InetStudio\Hashtags\Contracts\Http\Controllers\Back\Points',
    ], function () {
        Route::any('points/data', 'PointsDataControllerContract@data')->name('back.hashtags.points.data.index');

        Route::resource('points', 'PointsControllerContract', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);
    });

    Route::group([
        'namespace' => 'InetStudio\Hashtags\Contracts\Http\Controllers\Back\Prizes',
    ], function () {
        Route::any('prizes/data', 'PrizesDataControllerContract@data')->name('back.hashtags.prizes.data.index');

        Route::post('prizes/suggestions', 'PrizesUtilityControllerContract@getSuggestions')->name('back.hashtags.prizes.getSuggestions');

        Route::resource('prizes', 'PrizesControllerContract', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);
    });

    Route::group([
        'namespace' => 'InetStudio\Hashtags\Contracts\Http\Controllers\Back\Stages',
    ], function () {
        Route::any('stages/data', 'StagesDataControllerContract@data')->name('back.hashtags.stages.data.index');

        Route::post('stages/suggestions', 'StagesUtilityControllerContract@getSuggestions')->name('back.hashtags.stages.getSuggestions');

        Route::resource('stages', 'StagesControllerContract', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);
    });

    Route::group([
        'namespace' => 'InetStudio\Hashtags\Contracts\Http\Controllers\Back\Statuses',
    ], function () {
        Route::any('statuses/data', 'StatusesDataControllerContract@data')->name('back.hashtags.statuses.data.index');

        Route::post('statuses/suggestions', 'StatusesUtilityControllerContract@getSuggestions')->name('back.hashtags.statuses.getSuggestions');

        Route::resource('statuses', 'StatusesControllerContract', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);
    });

    Route::group([
        'namespace' => 'InetStudio\Hashtags\Contracts\Http\Controllers\Back\Tags',
    ], function () {
        Route::any('tags/data', 'TagsDataControllerContract@data')->name('back.hashtags.tags.data.index');

        Route::post('tags/suggestions', 'TagsUtilityControllerContract@getSuggestions')->name('back.hashtags.tags.getSuggestions');

        Route::resource('tags', 'TagsControllerContract', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);
    });

    Route::group([
        'namespace' => 'InetStudio\Hashtags\Contracts\Http\Controllers\Back\Posts',
    ], function () {
        Route::any('posts/data', 'PostsDataControllerContract@data')->name('back.hashtags.posts.data.index');

        Route::post('posts/append', 'PostsModerationControllerContract@add')->name('back.hashtags.posts.append');
        Route::post('posts/sort', 'PostsModerationControllerContract@sort')->name('back.hashtags.posts.sort');
        Route::any('posts/moderate/{id}/{status}', 'PostsModerationControllerContract@moderate')->name('back.hashtags.posts.moderate');

        Route::get('posts/download/{status}/{id?}', 'PostsUtilityControllerContract@download')->name('back.hashtags.posts.download');

        Route::get('posts/{status?}', 'PostsControllerContract@index')->name('back.hashtags.posts.index');
        Route::resource('posts', 'PostsControllerContract', ['except' => [
            'show', 'index', 'create',
        ], 'as' => 'back.hashtags']);
    });
});
