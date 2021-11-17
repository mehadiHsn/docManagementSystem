<?php

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::group(['middleware' => ['web']], function () {
    Route::get('/type-group-status', 'HomeController@getdoctypeandgroup');
    Route::get('/get-active-users', 'HomeController@getActiveUsers');
    //Route::get('/auto-suggest','DocumentMainController@autosuggest');
});

//Route::middleware('auth')->group(function () {
Route::get('/new-document', 'DocumentMainController@index')->name('newDoc');
Route::get('/notice', 'HomeController@notice');
Route::post('/notice/change-request-action', 'ChangeRequestController@crAction');
Route::post('/add-new-document', 'DocumentMainController@addNewDoc')->name('addNewDoc');
Route::get('/get-my-docs/{id}', 'HomeController@getMyDocs')->name('getMyDocs');
Route::get('/user-permissions', 'UserPermissionController@getUserPermissions');
Route::get('/notice/get-created-docs', 'HomeController@ownerNotices');
Route::post('/notice/approve', 'DocumentMainController@approveDoc');
Route::post('/notice/reject', 'DocumentMainController@rejectDoc');
Route::get('/make-change-request/{id}', 'ChangeRequestController@index');
Route::post('/make-change-request/{id}/create', 'ChangeRequestController@changeRequest');
Route::get('/get-docinfo/{id?}', 'ChangeRequestController@getDocInfo');
Route::get('/get-user-info', 'UserController@getUserInfo');
Route::get('/settings', 'UserController@index');
Route::get('/settings/requested-permissions', 'UserController@getCurrentRequest');
Route::post('/settings/make-request', 'UserController@makePermissionRequest');
Route::post('/settings/delete-old-request', 'UserController@deletePermissionRequest');
Route::get('/settings/update-profile', 'UserController@updateProfileGet');
Route::post('/settings/update-profile', 'UserController@updateProfile');
Route::get('/search/{q?}', 'HomeController@searchResult');
//});



Route::get('/admin/login', 'Auth\LoginController@showAdminLoginForm')->name('adminLogin');
Route::post('/admin/login', 'Admin\AdminController@adminLogin')->name('adminLoginNow');
Route::get('/admin/register', 'Admin\AdminController@showAdminRegisterForm')->name('adminRegister');
Route::post('/admin/register', 'Admin\AdminController@createAdmin')->name('adminRegistration');
Route::post('/admin/logout', 'Admin\AdminController@adminLogout')->name('adminLogout');

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::get('/', 'Admin\AdminController@index')->name('admin');
    Route::get('/get-admin-info', 'Admin\AdminController@getAdminInfo');
    Route::post('/update-profile', 'Admin\AdminController@updateProfile');
    Route::post('/update-password', 'Admin\AdminController@updatePassword');
    Route::group(['prefix' => 'add-new'], function () {
        Route::get('/', 'Admin\AddnewController@index');
        Route::post('/group', 'Admin\AddnewController@newgroup');
        Route::post('/type', 'Admin\AddnewController@newtype');
        Route::post('/status', 'Admin\AddnewController@newstatus');
        Route::post('/access', 'Admin\AddnewController@newaccess');
    });
    /* update */
    Route::group(['prefix' => 'update-old'], function () {
        Route::get('/', 'Admin\AddnewController@index');
        Route::post('/group', 'Admin\UpdateoldController@updategroup');
        Route::post('/type', 'Admin\UpdateoldController@updatetype');
        Route::post('/status', 'Admin\UpdateoldController@updatestatus');
        Route::post('/permission', 'Admin\UpdateoldController@updatePermissionList');
    });

    /* user manage */
    Route::group(['prefix' => '/user-management', 'middleware' => 'admin'], function () {
        Route::get('/', 'Admin\UserManagementController@index');
        Route::post('/permision-request-action', 'Admin\UserManagementController@permissionAction');
        Route::post('/user-access-modify', 'Admin\UserManagementController@changeAccess');
        Route::post('/user-access-remove', 'Admin\UserManagementController@removeAccess');
    });
    /* Owner manager */
    Route::group(['prefix' => '/owner-management', 'middleware' => 'admin'], function () {
        Route::get('/', 'Admin\UserManagementController@ownerGroupIndex');
        Route::get('/get-relevant-data', 'Admin\UserManagementController@getRelevantData');
        Route::post('/create-owner', 'Admin\UserManagementController@createOwner');
        Route::post('/make-user-owner', 'Admin\UserManagementController@makeUserOwner');
        Route::post('/remove-owner', 'Admin\UserManagementController@removeOwner');
    });
});
