<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\DisbursementController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\ReconcileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('test', function () {
    Log::info("test success");
});

Route::get('/job', [GeneralController::class, 'job'])->name('job');
// Route::get('/bank', [GeneralController::class, 'migrateBank'])->name('migrateBank');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User
    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::get('/users/data', [UserController::class, 'userData'])->name('user.data');
    Route::get('/users/edit/{uuid}', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/users/update', [UserController::class, 'update'])->name('user.update');
    Route::get('/users/destroy/{uuid}', [UserController::class, 'destroy'])->name('user.destroy');

    // Role
    Route::get('/roles', [RoleController::class, 'index'])->name('role.index');
    Route::get('/roles/data', [RoleController::class, 'data'])->name('role.data');
    Route::get('/roles/edit/{id}', [RoleController::class, 'edit'])->name('role.edit');
    Route::post('/roles/update', [RoleController::class, 'update'])->name('role.update');
    Route::get('/roles/destroy/{id}', [RoleController::class, 'destroy'])->name('role.destroy');
    Route::get('/role/detail/{slug}', [RoleController::class, 'detail'])->name('role.detail');
    Route::post('/privilege/update', [RoleController::class, 'privilege'])->name('role.privilege');

    // Bank
    Route::get('/banks', [BankController::class, 'index'])->name('bank.index');
    Route::get('/banks/data', [BankController::class, 'data'])->name('bank.data');
    Route::post('/banks/store', [BankController::class, 'store'])->name('bank.store');
    Route::get('/banks/edit/{id}', [BankController::class, 'edit'])->name('bank.edit');
    Route::post('/banks/update', [BankController::class, 'update'])->name('bank.update');
    Route::get('/banks/destroy/{id}', [BankController::class, 'destroy'])->name('bank.destroy');

    // Parameters
    Route::get('/parameters', [ParameterController::class, 'index'])->name('parameter.index');
    Route::get('/parameters/data', [ParameterController::class, 'data'])->name('parameter.data');
    Route::get('/parameters/edit/{id}', [ParameterController::class, 'edit'])->name('parameter.edit');
    Route::post('/parameters/update', [ParameterController::class, 'update'])->name('parameter.update');
    Route::post('/parameters/store', [ParameterController::class, 'store'])->name('parameter.store');
    Route::get('/parameters/{id}/destroy', [ParameterController::class, 'destroy'])->name('parameter.destroy');



    // Settlement
    Route::get('/settlement', [SettlementController::class, 'index'])->name('settlement.index');
    Route::get('/settlement/data', [SettlementController::class, 'data'])->name('settlement.data');
    Route::get('/settlement/destroy/{token}', [SettlementController::class, 'destroy'])->name('settlement.destroy');
    Route::post('/settlement', [SettlementController::class, 'store'])->name('settlement.store');
    Route::get('/settlement/bo/data', [SettlementController::class, 'boSettlement'])->name('settlement.bo');
    Route::get('/settlement/bo/datadraft/{token}', [SettlementController::class, 'bodraftSettlement'])->name('settlement.bodraft');
    Route::get('/settlement/bo-detail/data', [SettlementController::class, 'boDetailSettlement'])->name('settlement.boDetail');
    Route::get('/settlement/bank/data', [SettlementController::class, 'bankSettlement'])->name('settlement.bank');
    Route::get('/settlement/partner/data', [SettlementController::class, 'partnerReport'])->name('settlement.partner');
    
    
    // Reconcile
    Route::get('/settlement/detail/{token}', [ReconcileController::class, 'detail'])->name('reconcile.detail');
    
    Route::get('/reconcile', [ReconcileController::class, 'index'])->name('reconcile.index');
    
    Route::get('/reconcile-list', [ReconcileController::class, 'reconcilelist'])->name('reconcile.list');
    Route::get('/reconcilelistdata', [ReconcileController::class, 'reconcilelistdata'])->name('reconcile.listdata');
    Route::post('/reconciledraft', [ReconcileController::class, 'draftstore'])->name('reconcile.draftstore');
    Route::get('/reconcilelistdestroy/{token}', [ReconcileController::class, 'reconcilelistdestroy'])->name('reconcile.listdestroy');
    Route::get('/reconcile-list/detail/{token}', [ReconcileController::class, 'reconcilelistdetail'])->name('reconcile.listdetail');
    Route::get('/reconcile-list/detaildata/{token}', [ReconcileController::class, 'datareconcilelistdetail'])->name('reconcile.datalistdetail');
    Route::get('/reconcileresult/data', [ReconcileController::class, 'resultdata'])->name('reconcile.resultdata');

    Route::get('/reconcile/data/result', [ReconcileController::class, 'approveddata'])->name('reconcile.reportdata');

    Route::get('/reconcilereport/data', [ReconcileController::class, 'reportdata'])->name('reconcile.reportdata');
    Route::post('/reconcilereport/approve/{id}', [ReconcileController::class, 'approveReport'])->name('reconcile.approvereport');
    Route::post('/reconcilereport/draft/{id}', [ReconcileController::class, 'draftReport'])->name('reconcile.approvereport');
    Route::post('/reconcilereport/store/{id}', [ReconcileController::class, 'storeReport'])->name('reconcile.goreport');
    Route::post('/reconcilereport/manual/{id}', [ReconcileController::class, 'manualReport'])->name('reconcile.gomanual');


    Route::post('/reconciledraft/move/{token}', [ReconcileController::class, 'draftmove'])->name('reconcile.draftmove');

    Route::post('/reconcile-unmatch/store/{id}', [ReconcileController::class, 'unmatchstore'])->name('reconcile.unmatchstore');
    Route::get('/reconcile-unmatch/data', [ReconcileController::class, 'unmatchdata'])->name('reconcile.unmatchdata');
    
    Route::get('/reconcile-manual', [ReconcileController::class, 'manualrecon'])->name('reconcile.manual');
    
    Route::post('/reconcile', [ReconcileController::class, 'store'])->name('reconcile.store');
    Route::get('/reconcile/result', [ReconcileController::class, 'result'])->name('reconcile.result');
    Route::get('/reconcile/{token}/proceed', [ReconcileController::class, 'proceed'])->name('reconcile.proceed');
    Route::get('/reconcile/{token}/show', [ReconcileController::class, 'show'])->name('reconcile.show');
    Route::get('/reconcile/data', [ReconcileController::class, 'data'])->name('reconcile.data');
    Route::get('/reconcile/detail/data/{token}', [ReconcileController::class, 'detailData'])->name('reconcile.detailData');
    Route::get('/reconcile/download', [ReconcileController::class, 'download'])->name('reconcile.download');
    Route::post('/reconcile/single', [ReconcileController::class, 'reconcile'])->name('reconcile.single');
    
    Route::post('/reconcile/channel', [ReconcileController::class, 'channel'])->name('reconcile.channel');
    Route::get('/reconcile/partner', [ReconcileController::class, 'partner'])->name('reconcile.partner');
    Route::post('/reconcile/partner', [ReconcileController::class, 'reconcilePartner'])->name('reconcile.reconcilePartner');

    Route::get('/settlement/detail/{token}/{status}', [ReconcileController::class, 'detailstatus'])->name('reconcile.detailstatus');
    Route::get('/settlement/detail/{token}/{status}/data', [ReconcileController::class, 'detailstatusdata'])->name('reconcile.detailstatusdata');



    // Modal Show
    Route::get('/reportmrc/{token}/detail', [ReconcileController::class, 'reportmrcDetail'])->name('reconcile.mrcDetail');
    Route::get('/mrc/{token}/detail', [ReconcileController::class, 'mrcDetail'])->name('reconcile.mrcDetail');
    Route::get('/draft/{id}/detail', [ReconcileController::class, 'draftDetail'])->name('reconcile.draftDetail');

    // Disbursement
    Route::get('/disbursement', [DisbursementController::class, 'index'])->name('disbursment.index');

    
});

require __DIR__.'/auth.php';
