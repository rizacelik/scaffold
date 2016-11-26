<?php
return "

// $app_name Routes
Route::get('$app_var', '{$app_name}Controller@index')->name('$app_var.index');
Route::get('$app_var/create', '{$app_name}Controller@create')->name('$app_var.create');
Route::post('$app_var/store', '{$app_name}Controller@store');
Route::get('$app_var/show/{{$id}}', '{$app_name}Controller@show');
Route::get('$app_var/edit/{{$id}}', '{$app_name}Controller@edit');
Route::put('$app_var/update/{{$id}}', '{$app_name}Controller@update');
Route::get('$app_var/delete/{{$id}}', '{$app_name}Controller@destroy');

";
