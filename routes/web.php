<?php

Route::group(['namespace' => 'InetStudio\Hashtags\Controllers'], function () {
    //Route::get('modules/hashtags/searchTag', 'TagsController@searchCity')->name('front.hashtags.tags.search');
    //Route::post('modules/hashtags/info', 'TagsController@info')->name('front.hashtags.tags.info');
    Route::any('modules/hashtags/gallery/{social?}', 'PostsController@getGallery')->name('front.hashtags.posts.gallery');
    //Route::any('modules/hashtags/getDayWinners', 'PostsController@getDayWinners')->name('front.contest.city.tag.posts.getDayWinners');
    //Route::any('modules/hashtags/getStagesWinners', 'PostsController@getStagesWinners')->name('front.contest.city.tag.posts.getStagesWinners');

    /*
    Route::group(['middleware' => ['web','auth']], function () {
        Route::post('modules/hashtags/vote', 'ContestByCityTagAddressesController@vote')->name('front.contest.city.tag.addresses.vote');
    });
    */
});

Route::group(['middleware' => 'web', 'prefix' => 'back/hashtags'], function () {
    Route::group(['middleware' => 'back.auth', 'namespace' => 'InetStudio\Hashtags\Controllers'], function () {
        Route::any('posts/moderate/{id}/{status}', 'PostsController@moderate')->name('back.hashtags.posts.moderate');
        Route::get('posts/download/{status}/{id?}', 'PostsController@download')->name('back.hashtags.posts.download');
        Route::post('posts/sort', 'PostsController@sort')->name('back.hashtags.posts.sort');
        Route::any('posts/data', 'PostsController@data')->name('back.hashtags.posts.data');
        Route::get('posts/{status?}', 'PostsController@index')->name('back.hashtags.posts.index');
        Route::resource('posts', 'PostsController', ['except' => [
            'show', 'index', 'create',
        ], 'as' => 'back.hashtags']);

        Route::any('prizes/data', 'PrizesController@data')->name('back.hashtags.prizes.data');
        Route::resource('prizes', 'PrizesController', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);

        Route::any('statuses/data', 'StatusesController@data')->name('back.hashtags.statuses.data');
        Route::resource('statuses', 'StatusesController', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);

        Route::any('stages/data', 'StagesController@data')->name('back.hashtags.stages.data');
        Route::resource('stages', 'StagesController', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);

        Route::any('points/data', 'PointsController@data')->name('back.hashtags.points.data');
        Route::resource('points', 'PointsController', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);

        Route::get('tags/searchTag', 'TagsController@searchTag')->name('back.hashtags.tags.search');
        Route::any('tags/data', 'TagsController@data')->name('back.hashtags.tags.data');
        Route::resource('tags', 'TagsController', ['except' => [
            'show',
        ], 'as' => 'back.hashtags']);
    });
});
