<?php

Route::group([
    'middleware' => ['web', 'back.auth'],
    'prefix' => 'back/hashtags'
], function () {
    Route::group([
        'namespace' => 'InetStudio\Hashtags\Http\Controllers\Back\Points',
    ], function () {
        Route::any('points/data', 'PointsDataController@data')->name('back.hashtags.points.data');

        Route::resource('points', 'PointsController', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);
    });

    Route::group([
        'namespace' => 'InetStudio\Hashtags\Http\Controllers\Back\Prizes',
    ], function () {
        Route::any('prizes/data', 'PrizesDataController@data')->name('back.hashtags.prizes.data');

        Route::post('prizes/suggestions', 'PrizesUtilityController@getSuggestions')->name('back.hashtags.prizes.getSuggestions');

        Route::resource('prizes', 'PrizesController', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);
    });

    Route::group([
        'namespace' => 'InetStudio\Hashtags\Http\Controllers\Back\Stages',
    ], function () {
        Route::any('stages/data', 'StagesDataController@data')->name('back.hashtags.stages.data');

        Route::post('stages/suggestions', 'StagesUtilityController@getSuggestions')->name('back.hashtags.stages.getSuggestions');

        Route::resource('stages', 'StagesController', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);
    });

    Route::group([
        'namespace' => 'InetStudio\Hashtags\Http\Controllers\Back\Statuses',
    ], function () {
        Route::any('statuses/data', 'StatusesDataController@data')->name('back.hashtags.statuses.data');

        Route::post('statuses/suggestions', 'StatusesUtilityController@getSuggestions')->name('back.hashtags.statuses.getSuggestions');

        Route::resource('statuses', 'StatusesController', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);
    });

    Route::group([
        'namespace' => 'InetStudio\Hashtags\Http\Controllers\Back\Tags',
    ], function () {
        Route::any('tags/data', 'TagsDataController@data')->name('back.hashtags.tags.data');

        Route::post('tags/suggestions', 'TagsUtilityController@getSuggestions')->name('back.hashtags.tags.getSuggestions');

        Route::resource('tags', 'TagsController', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);
    });

    Route::group([
        'namespace' => 'InetStudio\Hashtags\Http\Controllers\Back\Posts',
    ], function () {
        Route::any('posts/data', 'PostsDataController@data')->name('back.hashtags.posts.data');

        Route::post('posts/append', 'PostsModerationController@add')->name('back.hashtags.posts.append');
        Route::post('posts/sort', 'PostsModerationController@sort')->name('back.hashtags.posts.sort');
        Route::any('posts/moderate/{id}/{status}', 'PostsModerationController@moderate')->name('back.hashtags.posts.moderate');

        Route::get('posts/download/{status}/{id?}', 'PostsUtilityController@download')->name('back.hashtags.posts.download');

        Route::get('posts/{status?}', 'PostsController@index')->name('back.hashtags.posts.index');
        Route::resource('posts', 'PostsController', ['except' => [
            'show', 'index', 'create',
        ], 'as' => 'back.hashtags']);
    });
});

Route::group(['namespace' => 'InetStudio\Hashtags\Http\Controllers\Front'], function () {
    Route::any('module/hashtags/gallery/{social?}', 'PostsController@getGallery')->name('front.hashtags.posts.gallery');
    Route::any('module/hashtags/getDaysWinners/{prize?}', 'PostsController@getDaysWinners')->name('front.hashtags.posts.daysWinners');
    Route::any('module/hashtags/getStagesWinners/{stage?}/{prize?}', 'PostsController@getStagesWinners')->name('front.hashtags.posts.stagesWinners');
});
