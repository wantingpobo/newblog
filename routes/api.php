<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('Items', 'App\Http\Controllers\Api\ItemController');

Route::namespace ('App\Http\Controllers\Api')->prefix('Items/query')->group(function () {
    //CRUD練習
    Route::get('querySelect', 'ItemController@querySelect');
    Route::get('querySpecific', 'ItemController@querySpecific');
    Route::get('queryPagination', 'ItemController@queryPagination');
    Route::get('queryRange/{min}/{max}', 'ItemController@queryRange');
    Route::get('queryByCgy/{cgy}', 'ItemController@queryByCgy');
    Route::get('queryPluck', 'ItemController@queryPluck');
    Route::get('enabledCount', 'ItemController@enabledCount');

    //關聯練習
    Route::get('queryCgyRelation/{cgy}', 'ItemController@queryCgyRelation');
    Route::get('changeCgy/{old_cgy_id}/{new_cgy_id}', 'ItemController@changeCgy');
    Route::get('getItemCgy/{item}', 'ItemController@getItemCgy');
    Route::get('changeAllCgy/{old_cgy_id}/{new_cgy_id}', 'ItemController@changeAllCgy');
    Route::get('queryTags/{item}', 'ItemController@queryTags');
    Route::get('addTag/{item}/{tag_id}', 'ItemController@addTag');
    Route::get('removeTag/{item}/{tag_id}', 'ItemController@removeTag');
    Route::get('syncTag/{item}', 'ItemController@syncTag');
    Route::get('addTagWithColor/{item}/{tag_id}/{color}', 'ItemController@addTagWithColor');
    Route::get('queryTagsWithColor/{item}', 'ItemController@queryTagsWithColor');
    Route::get('getItemWithTags/{item}', 'ItemController@getItemWithTags');

});

//實作API的驗證機制
Route::group(['prefix' => 'auth', 'namespace' => 'App\Http\Controllers\Api'], function () {
    Route::get('/', 'AuthController@me')->name('me'); //找到目前的登入者
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('logout', 'AuthController@logout')->name('logout');
});