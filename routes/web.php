<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\FileUploadController;

Route::get('/file-upload', [FileUploadController::class, 'showForm'])->name('file-upload.form');
Route::post('/file-upload', [FileUploadController::class, 'upload'])->name('file-upload.upload');

//for getting upazilas by district id
Route::get('/get-upazilas/{district_id}', [FileUploadController::class, 'getUpazilas'])->name('get-upazilas');
//for getting unions by upazila id
Route::get('/get-unions/{upazila_id}', [FileUploadController::class, 'getUnions'])->name('get-unions');
//for getting institutions by union id, institute type
Route::get('/get-institutions/{union_id}/{institution_type}/{user_id}', [FileUploadController::class, 'getInstitutions'])->name('get-institutions');
//for getting infrastructures by institution id
Route::get('/get-infrastructures/{institution_id}', [FileUploadController::class, 'getInfrastructures'])->name('get-infrastructures');

//get-inspaction-images
Route::get('/get-inspaction-images/{infrastructure_id}/{inspaction_date}', [FileUploadController::class, 'getInspactionImages'])->name('get-inspaction-images');
