<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\DisbursementController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\PermissionController;
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

Route::get('/spatie', function () {
    return 'Hello Admin';
})->middleware(['auth', 'verified', 'role:finance1']);

Route::get('/job', [GeneralController::class, 'job'])->name('job');
// Route::get('/bank', [GeneralController::class, 'migrateBank'])->name('migrateBank');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User
    Route::get('/users', [UserController::class, 'index'])->name('user.index')
        ->middleware(['permission:view-user|create-user|update-user|delete-user']);
    Route::get('/users/add', [UserController::class, 'add'])->name('user.add')
        ->middleware(['permission:create-user']);
    Route::post('/users/store', [UserController::class, 'store'])->name('user.store')
        ->middleware(['permission:create-user']);
    Route::get('/users/data', [UserController::class, 'userData'])->name('user.data');
    Route::get('/users/edit/{uuid}', [UserController::class, 'edit'])->name('user.edit')
        ->middleware(['permission:update-user']);
    Route::post('/users/update', [UserController::class, 'update'])->name('user.update')
        ->middleware(['permission:update-user']);
    Route::get('/users/destroy/{uuid}', [UserController::class, 'destroy'])->name('user.destroy')
        ->middleware(['permission:delete-user']);
    Route::get('/aktivasi', [UserController::class, 'aktivasi'])->name('user.aktivasi')
        ->middleware(['permission:activated-user']);
    Route::get('/users/aktivasidata', [UserController::class, 'aktivasiData'])->name('user.aktivasidata');
    Route::get('/users/activated/{uuid}', [UserController::class, 'activated'])->name('user.activated');

    // Permission
    Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index')
        ->middleware(['permission:view-permission|create-permission|update-permission|delete-permission']);
    Route::get('/permission/data', [PermissionController::class, 'data'])->name('permission.data');
    Route::get('/permission/get/{id}', [PermissionController::class, 'get'])->name('permission.get')
        ->middleware(['permission:update-permission']);
    Route::post('/permission/update', [PermissionController::class, 'update'])->name('permission.update')
        ->middleware(['permission:update-permission']);
    Route::post('/permission/store', [PermissionController::class, 'store'])->name('permission.store')
        ->middleware(['permission:create-permission']);
    Route::get('/permission/add', [PermissionController::class, 'add'])->name('permission.add')
        ->middleware(['permission:create-permission']);
    Route::get('/permission/destroy/{id}', [PermissionController::class, 'destroy'])->name('permission.destroy')
        ->middleware(['permission:delete-permission']);

    // Role
    Route::get('/roles', [RoleController::class, 'index'])->name('role.index')
        ->middleware(['permission:view-role|create-role|update-role|delete-role']);
    Route::get('/roles/data', [RoleController::class, 'data'])->name('role.data');
    Route::get('/roles/edit/{id}', [RoleController::class, 'edit'])->name('role.edit')
        ->middleware(['permission:update-role']);
    Route::post('/roles/update', [RoleController::class, 'update'])->name('role.update')
        ->middleware(['permission:update-role']);
    Route::post('/roles/store', [RoleController::class, 'store'])->name('role.store')
        ->middleware(['permission:create-role']);
    Route::get('/roles/add', [RoleController::class, 'add'])->name('role.add')
        ->middleware(['permission:create-role']);
    Route::get('/roles/destroy/{id}', [RoleController::class, 'destroy'])->name('role.destroy')
        ->middleware(['permission:delete-role']);
    Route::get('/role/detail/{slug}', [RoleController::class, 'detail'])->name('role.detail')
        ->middleware(['permission:view-role']);
    Route::post('/privilege/update', [RoleController::class, 'privilege'])->name('role.privilege');
    Route::get('/roles/count/{id}', [RoleController::class, 'countTotalRoles']);



    // Bank
    Route::get('/banks', [BankController::class, 'index'])->name('bank.index')
        ->middleware(['permission:view-channel|create-channel|update-channel|delete-channel']);
    Route::get('/banks/data', [BankController::class, 'data'])->name('bank.data');
    Route::post('/banks/add', [BankController::class, 'store'])->name('bank.add')
        ->middleware(['permission:create-channel']);
    Route::post('/banks/store', [BankController::class, 'store'])->name('bank.store')
        ->middleware(['permission:create-channel']);
    Route::get('/banks/edit/{id}', [BankController::class, 'edit'])->name('bank.edit')
        ->middleware(['permission:update-channel']);
    Route::post('/banks/update', [BankController::class, 'update'])->name('bank.update')
        ->middleware(['permission:update-channel']);
    Route::get('/banks/destroy/{id}', [BankController::class, 'destroy'])->name('bank.destroy')
        ->middleware(['permission:delete-channel']);

    // Parameters
    Route::get('/parameters', [ParameterController::class, 'index'])->name('parameter.index')
        ->middleware(['permission:view-param|create-param|update-param|delete-param']);
    Route::get('/parameters/data', [ParameterController::class, 'data'])->name('parameter.data');
    Route::get('/parameters/edit/{id}', [ParameterController::class, 'edit'])->name('parameter.edit')
        ->middleware(['permission:update-param']);
    Route::post('/parameters/update', [ParameterController::class, 'update'])->name('parameter.update')
        ->middleware(['permission:update-param']);
    Route::post('/parameters/store', [ParameterController::class, 'store'])->name('parameter.store')
        ->middleware(['permission:create-param']);
    Route::get('/parameters/{id}/destroy', [ParameterController::class, 'destroy'])->name('parameter.destroy')
        ->middleware(['permission:delete-param']);



    // Settlement
    Route::get('/settlement', [SettlementController::class, 'index'])->name('settlement.index')
        ->middleware(['permission:view-bs|create-bs|update-bs|delete-bs']);
    Route::get('/settlement/data', [SettlementController::class, 'data'])->name('settlement.data');
    Route::get('/settlement/destroy/{token}', [SettlementController::class, 'destroy'])->name('settlement.destroy')
        ->middleware(['permission:delete-bs']);
    Route::post('/settlement', [SettlementController::class, 'store'])->name('settlement.store')
        ->middleware(['permission:create-bs']);
    Route::get('/settlement/bo/data', [SettlementController::class, 'boSettlement'])->name('settlement.bo');
    Route::get('/settlement/bo/datadraft/{token}', [SettlementController::class, 'bodraftSettlement'])->name('settlement.bodraft')
        ->middleware(['permission:view-bs']);
    Route::get('/settlement/bo-detail/data', [SettlementController::class, 'boDetailSettlement'])->name('settlement.boDetail')
        ->middleware(['permission:view-bs']);
    Route::get('/settlement/bank/data', [SettlementController::class, 'bankSettlement'])->name('settlement.bank');
    Route::get('/settlement/partner/data', [SettlementController::class, 'partnerReport'])->name('settlement.partner');


    // Reconcile
    Route::get('/settlement/detail/{token}', [ReconcileController::class, 'detail'])->name('reconcile.detail')
        ->middleware(['permission:view-bs']);

    Route::get('/reconcile', [ReconcileController::class, 'index'])->name('reconcile.index')
        ->middleware(['permission:view-reconlist|create-reconlist|update-reconlist|delete-reconlist']);

    Route::get('/reconcile-list', [ReconcileController::class, 'reconcilelist'])->name('reconcile.list')
        ->middleware(['permission:view-reconlist|create-reconlist|update-reconlist|delete-reconlist']);
    Route::get('/reconcilelistdata', [ReconcileController::class, 'reconcilelistdata'])->name('reconcile.listdata');
    Route::post('/reconciledraft', [ReconcileController::class, 'draftstore'])->name('reconcile.draftstore')
        ->middleware(['permission:create-reconlist']);
    Route::get('/reconcilelistdestroy/{token}', [ReconcileController::class, 'reconcilelistdestroy'])->name('reconcile.listdestroy')
        ->middleware(['permission:delete-reconlist']);
    Route::get('/reconcile-list/detail/{token}', [ReconcileController::class, 'reconcilelistdetail'])->name('reconcile.listdetail')
        ->middleware(['permission:view-reconlist']);
    Route::get('/reconcile-list/detaildata/{token}', [ReconcileController::class, 'datareconcilelistdetail'])->name('reconcile.datalistdetail');
    Route::get('/reconcileresult/data', [ReconcileController::class, 'resultdata'])->name('reconcile.resultdata');

    Route::get('/reconcile/headerapproveddata', [ReconcileController::class, 'headerapproveddata'])->name('reconcile.headerapproveddata');
    Route::get('/reconcile/data/result', [ReconcileController::class, 'approveddata'])->name('reconcile.reportdata');


    Route::get('/reconcile/unmatch-list', [ReconcileController::class, 'menuunmatchlist'])->name('reconcile.menuunmatchlist')
        ->middleware(['permission:manual-reconlist']);
    Route::get('/reconcile/unmatch-listdata', [ReconcileController::class, 'unmatchlistdata'])->name('reconcile.unmatchlistdata');

    // Route::get('/reconcilereport/data', [ReconcileController::class, 'reportdata'])->name('reconcile.reportdata');
    Route::get('/reconcilereport/data/{token}', [ReconcileController::class, 'reportdata'])->name('reconcile.reportdata');
    Route::post('/reconcilereport/approve/{id}', [ReconcileController::class, 'approveReport'])->name('reconcile.approvereport');
    Route::post('/reconcilereport/draft/{id}', [ReconcileController::class, 'draftReport'])->name('reconcile.approvereport');
    Route::post('/reconcilereport/store/{id}', [ReconcileController::class, 'storeReport'])->name('reconcile.goreport');
    Route::post('/reconcilereport/manual/{id}', [ReconcileController::class, 'manualReport'])->name('reconcile.gomanual');
    Route::post('/reconcilereport/report/{id}', [ReconcileController::class, 'goReport'])->name('reconcile.goReport');

    Route::post('/reconcilereport/checkall/{token}', [ReconcileController::class, 'checkerAll'])->name('reconcile.checkall');
    Route::post('/reconcilereport/approveall', [ReconcileController::class, 'approveAll'])->name('reconcile.approveall');
    // ->middleware(['permission:approve-reconlist']);

    Route::post('/reconciledraft/move/{token}', [ReconcileController::class, 'draftmove'])->name('reconcile.draftmove')
        ->middleware(['permission:auto-reconlist']);

    Route::post('/reconcile-unmatch/store/{id}', [ReconcileController::class, 'unmatchstore'])->name('reconcile.unmatchstore');
    Route::get('/reconcile-unmatch/data', [ReconcileController::class, 'unmatchdata'])->name('reconcile.unmatchdata');

    Route::get('/reconcile-manual', [ReconcileController::class, 'manualrecon'])->name('reconcile.manual');

    Route::post('/reconcile', [ReconcileController::class, 'store'])->name('reconcile.store')
        ->middleware(['permission:manual-reconlist']);

    Route::get('/reconcile/disburstment-list', [ReconcileController::class, 'result'])->name('reconcile.result')
        ->middleware(['permission:download-disburslist|view-disburslist|approve-disburslist|cancel-disburslist']);

    Route::get('/reconcile/{token}/proceed', [ReconcileController::class, 'proceed'])->name('reconcile.proceed');
    Route::get('/reconcile/{token}/show', [ReconcileController::class, 'show'])->name('reconcile.show')
        ->middleware(['permission:download-disburslist|view-disburslist|approve-disburslist|cancel-disburslist']);
    Route::get('/reconcile/data', [ReconcileController::class, 'data'])->name('reconcile.data');
    Route::get('/reconcile/detail/data/{token}', [ReconcileController::class, 'detailData'])->name('reconcile.detailData');
    Route::get('/reconcile/download', [ReconcileController::class, 'download'])->name('reconcile.download');
    Route::get('/reconcile/downloaddisburst', [ReconcileController::class, 'downloaddisburst'])->name('reconcile.downloaddisburst')
        ->middleware(['permission:download-disburslist']);
    Route::get('/reconcile/downloadunmatch', [ReconcileController::class, 'downloadunmatch'])->name('reconcile.downloadunmatch')
        ->middleware(['permission:download-unmatchlist']);
    Route::post('/reconcile/single/{token}', [ReconcileController::class, 'reconcile'])->name('reconcile.single');
    Route::post('/reconcile/proceed/{token}', [ReconcileController::class, 'reconcileproceed'])->name('reconcile.proceed');

    Route::post('/updatebs', [ReconcileController::class, 'updatebs'])->name('updatebs');

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
    Route::get('/CHANGES', [ReconcileController::class, 'changes']);

});

require __DIR__ . '/auth.php';
