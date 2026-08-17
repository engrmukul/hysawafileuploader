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

use App\Http\Controllers\ExportController;
use App\Http\Controllers\ExportControllerSat;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\WaterPointController;
use App\Http\Controllers\SPReportController;


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
//for getting previous institute (INS) images by institution id
Route::get('/get-institution-images/{institution_id}', [FileUploadController::class, 'getInstitutionImages'])->name('get-institution-images');

//get-inspaction-images
Route::get('/get-inspaction-images/{infrastructure_id}/{inspaction_date}', [FileUploadController::class, 'getInspactionImages'])->name('get-inspaction-images');

//get-inspaction-dates
Route::get('/get-inspaction-dates/{infrastructure_id}', [FileUploadController::class, 'getInspectionDate'])->name('get-inspaction-dates');

//update-current-image
Route::post('/update-current-image', [FileUploadController::class, 'updateCurrentImage'])->name('update-current-image');

//update-image-status (active/inactive)
Route::post('/update-image-status', [FileUploadController::class, 'updateImageStatus'])->name('update-image-status');


//institution-edit
Route::get('/institution-edit/', [InstitutionController::class, 'edit'])->name('institution.edit');
//institution-update
Route::post('/institution-update/', [InstitutionController::class, 'update'])->name('institution.update');

//water-point create
Route::get('/water-point-create/', [WaterPointController::class, 'create'])->name('water-point.create');
//water-point-store
Route::post('/water-point-store/', [WaterPointController::class, 'store'])->name('water-point.store');
//water-point-edit
Route::get('/water-point-edit/', [WaterPointController::class, 'edit'])->name('water-point.edit');
//water-point-update
Route::post('/water-point-update/', [WaterPointController::class, 'update'])->name('water-point.update');
Route::get('/download-survey-data-rY37J9/', [ExportController::class, 'download'])->name('survey-download');
Route::get('/download-khl-institution-t66Y4f/', [ExportController::class, 'exportInstitutionsKhl']);
Route::get('/download-sat-institution-t29Y4f/', [ExportController::class, 'exportInstitutionsSat']);


Route::get('/weekly-wq-and-si-report-export/', [SPReportController::class, 'weeklyWqAndSiReportExport']);
