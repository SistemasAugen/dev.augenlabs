<?php

Route::group(['prefix' => 'app'], function() {
    Route::group(['middleware' => ['cors']], function () {
        Route::get('', 'AppController@index');
        Route::any('login', 'AppController@login');
        Route::group(['middleware' => 'jwt.auth'], function () {
            Route::any('rx/actives', 'AppController@rxActives');
            Route::any('rx/pendings', 'AppController@rxPendings');
        });
    });
    Route::any('{path}', 'AppController@notFound')->where('path', '.*');;
});

?>
