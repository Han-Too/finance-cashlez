<?php

namespace App\Http\Controllers;

use App\Exports\ReconcileDisburstExport;
use App\Exports\ReconcileExport;
use App\Exports\ReconcileUnmatchExport;
use App\Helpers\Reconcile;
use App\Helpers\Utils;
use App\Models\Bank;
use App\Models\BankParameter;
use App\Models\Channel;
use App\Models\DraftBackOffice;
use App\Models\InternalBatch;
use App\Models\InternalMerchant;
use App\Models\InternalTransaction;
use App\Models\ReconcileDraft;
use App\Models\ReconcileList;
use App\Models\ReconcileReport;
use App\Models\ReconcileResult;
use App\Models\ReconcileUnmatch;
use App\Models\ReportPartner;
use App\Models\UploadBank;
use App\Models\UploadBankDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReconcileController extends Controller
{
    public function index()
    {
        $banks = Channel::with('parameter')
            ->where('status', 'active')
            ->whereHas('parameter')
            ->get();
        return view('modules.reconcile.index', compact('banks'));
    }
    public function reconcilelist()
    {
        $list = ReconcileList::get();
        $channels = Channel::with('parameter')
            ->where('status', 'active')
            ->whereHas('parameter')
            ->get();
        $file = UploadBank::where('is_reconcile', '0')->get();
        return view('modules.reconcile.list.list', compact('list', 'file', 'channels'));
    }
    public function oldreconcilelistdata(Request $request)
    {
        $query = ReconcileList::get();


        return DataTables::of($query)->addIndexColumn()->make(true);
    }

    public function reconcilelistdata(Request $request)
    {
        $query = ReconcileList::get();

        // Mengambil data berdasarkan settlement_file dari UploadBank
        $query->transform(function ($item) {
            $item->UB = UploadBank::where('url', $item->settlement_file)->first();
            return $item;
        });

        return DataTables::of($query)->addIndexColumn()->make(true);
    }
    public function reportmove($id)
    {
        $data = ReconcileDraft::where('id', $id)->first();
        try {
            if ($data) {
                $reconcile = ReconcileReport::create([
                    'draft_id' => $data->id,
                    'bo_id' => $data->bo_id,
                    'bo_date' => "",
                    'token_applicant' => $data->token_applicant,
                    'statement_date' => $data->settlement_date,
                    'statement_id' => $data->statement_id,
                    'request_id' => $data->request_id,
                    'status' => $data->status,
                    'tid' => $data->tid,
                    'mid' => $data->mid,
                    'batch_fk' => $data->batch_fk,
                    'trx_counts' => $data->trx_counts,
                    'total_sales' => $data->total_sales,
                    'processor_payment' => $data->processor_payment,
                    'internal_payment' => $data->internal_payment,
                    'merchant_payment' => $data->merchant_payment,
                    'merchant_id' => $data->merchant_id,
                    'merchant_name' => $data->merchant_name,
                    'tax_payment' => $data->tax_payment,
                    'fee_mdr_merchant' => $data->fee_mdr_merchant,
                    'fee_bank_merchant' => $data->fee_bank_merchant,
                    'bank_transfer' => $data->bank_transfer,
                    'transfer_amount' => $data->transfer_amount,
                    'bank_settlement_amount' => $data->bank_settlement_amount,
                    // 'dispute_amount' => $data->dispute_amount,
                    'created_by' => $data->created_by,
                    'modified_by' => $data->modified_by,
                    'settlement_date' => $data->settlement_date,
                    'variance' => $data->variance,
                    'bank_id' => $data->bank_id,
                    'category_report' => 'system',
                    'status_manual' => false,
                    'status_reconcile' => "report",
                ]);
                $remove = ReconcileDraft::where('id', $id)->update([
                    "status_manual" => true
                ]);
                if ($reconcile == false) {
                    return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
                } else {
                    return response()->json(['message' => ['Successfully reconcile data!'], 'status' => true], 200);
                }
            } else {
                return response()->json(['message' => ['Error while store reconcile'], 'status' => false], 200);
            }

        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }

    public function draftmove($token)
    {
        try {
            $data = ReconcileDraft::where('token_applicant', $token)->get();

            foreach ($data as $val) {

                // //Log::info($val->statement_id);
                $statementId = $val->statement_id;

                if ($statementId == NULL) {

                    // ReconcileUnmatch::create([
                    //     'draft_id' => $val->id,
                    //     'statement_id' => "-",
                    //     'name' => $val->name,
                    //     'merchant_name' => $val->merchant_name,
                    //     'token_applicant' => $val->token_applicant,
                    //     'status' => $val->status,
                    //     'mid' => $val->mid,
                    //     'trx_counts' => $val->trx_counts,
                    //     'bank_transfer' => $val->bank_transfer,
                    //     'tax_payment' => $val->tax_payment,
                    //     "fee_mdr_merchant" => $val->fee_mdr_merchant,
                    //     "fee_bank_merchant" => $val->fee_bank_merchant,
                    //     'total_sales' => $val->total_sales,
                    //     'processor_payment' => $val->processor_payment,
                    //     'internal_payment' => "0",
                    //     'merchant_payment' => $val->merchant_payment,
                    //     'merchant_id' => $val->merchant_id,
                    //     'transfer_amount' => $val->transfer_amount,
                    //     'bank_settlement_amount' => "0",
                    //     // 'dispute_amount' => $val->dispute_amount,
                    //     'created_by' => $val->created_by,
                    //     'variance' => $val->variance,
                    //     'modified_by' => $val->modified_by,
                    //     'status_parnert' => $val->status_parnert,
                    //     'status_reconcile' => false,
                    //     'settlement_date' => $val->settlement_date,
                    // ]);

                    $val->status_manual = true;
                    $val->status_reconcile = "manual";
                    $val->status = "deleted";

                    DraftBackOffice::where('id', $val->bo_id)->update([
                        'status_reconcile' => NULL,
                        'reconcile_date' => NULL,
                    ]);
                } elseif (strpos($statementId, '//') !== false) {
                    // Jika $statementId mengandung '//', kita lakukan explode
                    $settlementID = explode('//', $statementId);
                } else {
                    // Jika tidak mengandung '//', gunakan nilai asli
                    $settlementID = [$statementId];
                }

                if ($val->status == "MATCH") {
                    ReconcileReport::create([
                        'draft_id' => $val->id,
                        'bo_id' => $val->bo_id,
                        'bo_date' => "",
                        'token_applicant' => $val->token_applicant,
                        'statement_date' => $val->settlement_date,
                        'statement_id' => $val->statement_id,
                        'request_id' => $val->request_id,
                        'status' => $val->status,
                        'tid' => $val->tid,
                        'mid' => $val->mid,
                        'batch_fk' => $val->batch_fk,
                        'trx_counts' => $val->trx_counts,
                        'total_sales' => $val->total_sales,
                        'processor_payment' => $val->processor_payment,
                        'internal_payment' => $val->internal_payment,
                        'merchant_payment' => $val->merchant_payment,
                        'merchant_id' => $val->merchant_id,
                        'merchant_name' => $val->merchant_name,
                        'tax_payment' => $val->tax_payment,
                        'fee_mdr_merchant' => $val->fee_mdr_merchant,
                        'fee_bank_merchant' => $val->fee_bank_merchant,
                        'bank_transfer' => $val->bank_transfer,
                        'transfer_amount' => $val->transfer_amount,
                        'bank_settlement_amount' => $val->bank_settlement_amount,
                        // 'dispute_amount' => $val->dispute_amount,
                        'created_by' => $val->created_by,
                        'modified_by' => $val->modified_by,
                        'settlement_date' => $val->settlement_date,
                        'variance' => $val->variance,
                        'bank_id' => $val->bank_id,
                        'category_report' => 'system',
                        'status_manual' => false,
                        'status_reconcile' => "report",
                        'reconcile_date' => Carbon::now(),
                    ]);
                    $val->status_manual = false;
                    $val->status_reconcile = "report";

                    DraftBackOffice::where('id', $val->bo_id)->update([
                        'status_reconcile' => "reconciled",
                        'reconcile_date' => Carbon::now(),
                    ]);

                } else {
                    foreach ($settlementID as $id) {
                        $settle = UploadBankDetail::where('id', $id)->first();

                        ReconcileUnmatch::create([
                            'draft_id' => $val->id,
                            'statement_id' => $id,
                            'name' => $val->name,
                            'merchant_name' => $val->merchant_name,
                            'token_applicant' => $val->token_applicant,
                            'status' => $val->status,
                            'mid' => $val->mid,
                            'trx_counts' => $val->trx_counts,
                            'bank_transfer' => $val->bank_transfer,
                            'tax_payment' => $val->tax_payment,
                            "fee_mdr_merchant" => $val->fee_mdr_merchant,
                            "fee_bank_merchant" => $val->fee_bank_merchant,
                            'total_sales' => $val->total_sales,
                            'processor_payment' => $val->processor_payment,
                            'internal_payment' => $settle->amount_credit,
                            'merchant_payment' => $val->merchant_payment,
                            'merchant_id' => $val->merchant_id,
                            'transfer_amount' => $val->transfer_amount,
                            'bank_settlement_amount' => $settle->amount_credit,
                            // 'dispute_amount' => $val->dispute_amount,
                            'created_by' => $val->created_by,
                            'variance' => $val->variance,
                            'modified_by' => $val->modified_by,
                            'status_parnert' => $val->status_parnert,
                            'status_reconcile' => false,
                            'settlement_date' => $val->settlement_date,
                        ]);
                    }

                    $val->status_manual = true;
                    $val->status_reconcile = "manual";
                    $val->status = "deleted";

                    DraftBackOffice::where('id', $val->bo_id)->update([
                        'status_reconcile' => NULL,
                        'reconcile_date' => NULL,
                    ]);
                }
                $val->save();
            }
            return response()->json(['message' => ['Successfully moving data!'], 'status' => true], 200);
        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }
    public function manualReport($id)
    {
        $data = ReconcileReport::where('id', $id)->first();
        try {
            if ($data) {
                // $idbo = explode("//", $data->bo_id);
                // $idbank = explode("//", $data->draft_id);
                
                $idbo = $data->bo_id;
                $idbank = $data->draft_id;
                $idmanual = $data->manual_id;

                // Cek apakah $idbo mengandung '//'
                if (strpos($idbo, '//') !== false) {
                    // Jika mengandung '//', lakukan explode
                    $idbo = explode('//', $idbo);
                } else {
                    // Jika tidak, jadikan array berisi nilai asli
                    $idbo = [$idbo];
                }

                // Cek apakah $idbank mengandung '//'
                if (strpos($idbank, '//') !== false) {
                    // Jika mengandung '//', lakukan explode
                    $idbank = explode('//', $idbank);
                } else {
                    // Jika tidak, jadikan array berisi nilai asli
                    $idbank = [$idbank];
                }

                if (strpos($idmanual, '//') !== false) {
                    // Jika mengandung '//', lakukan explode
                    $idmanual = explode('//', $idmanual);
                } else {
                    // Jika tidak, jadikan array berisi nilai asli
                    $idmanual = [$idmanual];
                }

                // Sekarang $idbo dan $idbank sudah diproses dengan explode jika ada '//'

                $list = ReconcileList::where('token_applicant', $data->token_applicant)->first();


                // foreach ($idbank as $val) {
                //     $manual = ReconcileDraft::where('token_applicant', $data->token_applicant)
                //         ->where('id', $val)->update([
                //                 'status' => "deleted",
                //                 'status_reconcile' => "manual",
                //             ]);
                // }
                ReconcileReport::where('id', $id)->update([
                    'status_reconcile' => "report",
                ]);

                foreach ($idbank as $val) {
                    ReconcileDraft::
                        // where('token_applicant', $data->token_applicant)
                        // ->
                        where('id', $val)->update([
                                'status' => "deleted",
                                'status_reconcile' => "manual",
                            ]);

                }
                foreach ($idmanual as $val) {
                    ReconcileUnmatch::
                        // where('token_applicant', $data->token_applicant)
                        // ->
                        where('id', $val)
                        ->update([
                            'status' => 'NOT_MATCH',
                            'status_reconcile' => false,
                            'reconcile_date' => NULL,
                        ]);
                }
                foreach ($idbo as $val) {
                    DraftBackOffice::where('id', $val)
                        ->where('draft_token', $data->token_applicant)
                        ->update([
                            'status_reconcile' => NULL,
                            'reconcile_date' => NULL,
                        ]);
                }

                $data = ReconcileReport::where('id', $id)->delete();

                // ReconcileReport::where('id', $id)->delete();
                return response()->json(['message' => ['Success Draft Data!'], 'status' => true], 200);

            }
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }
    public function goReport($id)
    {
        $data = ReconcileReport::where('id', $id)->first();
        try {
            if ($data) {
                if (strpos($data->draft_id, "//") !== false) {
                    $draftid = explode("//", $data->draft_id);
                    foreach ($draftid as $draft) {
                        ReconcileDraft::where('id', $draft)->update([
                            'status_reconcile' => 'report',
                            'status' => $data->status,
                            // 'reconcile_date' => Carbon::now(),
                            'modified_by' => Auth::user()->username,
                        ]);
                    }
                } else {
                    ReconcileDraft::where('id', $data->draft_id)->update([
                        'status_reconcile' => 'report',
                        'status' => $data->status,
                        // 'reconcile_date' => Carbon::now(),
                        'modified_by' => Auth::user()->username,
                    ]);
                }
                $boIds = explode("//", $data->bo_id);

                foreach ($boIds as $boId) {
                    DraftBackOffice::where('id', $boId)->update([
                        'status_reconcile' => 'reconciled',
                        'reconcile_date' => Carbon::now(),
                    ]);
                }
                $data->status_reconcile = "report";
                $data->save();

                ReconcileList::where("token_applicant", $data->token_applicant)->update([
                    'status' => 'draft',
                ]);
                UploadBank::where('token_applicant', $data->token_applicant)
                    ->update([
                        'is_reconcile' => 3,
                    ]);
                return response()->json(['message' => ['Success Approved Data!'], 'status' => true], 200);
            }
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }
    public function reporttodraft($id)
    {
        try {
            $data = ReconcileReport::where('id', $id)->first();
            $list = ReconcileList::where('token_applicant', $data->token_applicant)->first();
            // //Log::info($data->draft_id);
            // //Log::info(explode('//',$data->draft_id));
            // die();
            if ($data) {
                $draftid = explode('//', $data->draft_id);
                $draftid = array_unique($draftid);
                // $manualid = explode('//', $data->manual_id);
                // //Log::info($draftid);
                // die();
                foreach ($draftid as $draft_id) {
                    $draft = ReconcileDraft::where('id', $draft_id)->first();
                    // //Log::info($draft);
                    if ($draft) {
                        $draft->status = $data->status;
                        $draft->status_reconcile = "report";
                        $draft->save();

                        $data->status_reconcile = "report";
                        $data->save();

                        $list->status = "draft";
                        $list->save();

                        // ReconcileReport::where('id', $id)->delete();
                        return response()->json(['message' => ['Success Draft Data!'], 'status' => true], 200);
                    } else {
                        return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
                    }
                }
            }
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }
    public function draftReport($id)
    {
        $data = ReconcileReport::where('id', $id)->first();
        $list = ReconcileList::where('token_applicant', $data->token_applicant)->first();
        try {
            if ($data) {
                $manual = ReconcileDraft::where('id', $data->draft_id)->first();
                if ($manual) {
                    $manual->status = $data->status;
                    $manual->status_reconcile = "draft";
                    $manual->save();

                    $data->status_reconcile = "draft";
                    $data->save();

                    // ReconcileReport::where('id', $id)->delete();
                    return response()->json(['message' => ['Success Draft Data!'], 'status' => true], 200);
                } else {
                    return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
                }
            }
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }
    public function approveReport($id)
    {
        $data = ReconcileReport::where('id', $id)->first();
        try {
            if ($data) {
                if (strpos($data->draft_id, "//") !== false) {
                    $draftid = explode("//", $data->draft_id);
                    foreach ($draftid as $draft) {
                        ReconcileDraft::where('id', $draft)->update([
                            'status_reconcile' => 'reconciled',
                            'status' => $data->status,
                            'reconcile_date' => Carbon::now(),
                            'modified_by' => Auth::user()->username,
                        ]);
                    }
                } else {
                    ReconcileDraft::where('id', $data->draft_id)->update([
                        'status_reconcile' => 'reconciled',
                        'status' => $data->status,
                        'reconcile_date' => Carbon::now(),
                        'modified_by' => Auth::user()->username,
                    ]);
                }
                $boIds = explode("//", $data->bo_id);

                foreach ($boIds as $boId) {
                    DraftBackOffice::where('id', $boId)->update([
                        'status_reconcile' => 'approved',
                        'reconcile_date' => Carbon::now(),
                    ]);
                }
                $data->status_reconcile = "approved";
                $data->save();
                return response()->json(['message' => ['Success Approved Data!'], 'status' => true], 200);
            }
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }
    public function checkerAll($token)
    {
        try {
            $data = ReconcileReport::where('token_applicant', $token)->get();

            if ($data) {
                // foreach ($data as $val) {
                //     $id = explode("//",$val->draft_id);
                //     if ($val->category_report == "manual") {
                //         ReconcileDraft::where('id', $val->draft_id)->update([
                //             'status_reconcile' => 'reconciled',
                //             'status' => $val->status,
                //             'reconcile_date' => Carbon::now(),
                //             'modified_by' => Auth::user()->username,
                //         ]);
                //     } else {
                //         ReconcileDraft::where('id', $val->draft_id)->update([
                //             'status_reconcile' => 'reconciled',
                //             'status' => $val->status,
                //             'reconcile_date' => Carbon::now(),
                //             'modified_by' => Auth::user()->username,
                //         ]);
                //     }
                //     $val->status_reconcile = "approved";
                //     $val->save();
                // }


                foreach ($data as $val) {
                    if ($val->status_reconcile === "pending") {
                        continue;
                    }
                    // Pisahkan draft_id berdasarkan delimiter '//'
                    $draftIds = explode("//", $val->draft_id);

                    foreach ($draftIds as $draftId) {
                        ReconcileDraft::where('id', $draftId)->update([
                            'status_reconcile' => 'checker',
                            'status' => $val->status,
                            'reconcile_date' => Carbon::now(),
                            'modified_by' => Auth::user()->username,
                        ]);

                    }

                    // Pisahkan bo_id berdasarkan delimiter '//'
                    $boIds = explode("//", $val->bo_id);

                    foreach ($boIds as $boId) {
                        DraftBackOffice::where('id', $boId)->update([
                            'status_reconcile' => 'checker',
                            'reconcile_date' => Carbon::now(),
                        ]);
                    }


                    ReconcileList::where('token_applicant', $val->token_applicant)
                        ->update([
                            'status' => "pending",
                            'reconcile_date' => Carbon::now(),
                        ]);
                    UploadBank::where('token_applicant', $val->token_applicant)
                        ->update([
                            'is_reconcile' => true,
                        ]);
                    UploadBankDetail::where('token_applicant', $val->token_applicant)
                        ->update([
                            'is_reconcile' => true,
                        ]);

                    // Update status_reconcile pada objek $val
                    $val->status_reconcile = "pending";
                    $val->save();
                }
                return response()->json(['message' => ['Success Approve Data!'], 'status' => true], 200);

            }
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }
    public function approveAll()
    {
        $startDate = request()->query('startDate');
        $endDate = request()->query('endDate');
        $data = ReconcileReport::where('status_reconcile', 'pending')
            ->where(DB::raw('DATE(created_at)'), '>=', $startDate)
            ->where(DB::raw('DATE(created_at)'), '<=', $endDate)->get();
        try {
            if ($data) {

                foreach ($data as $val) {
                    // Pisahkan draft_id berdasarkan delimiter '//'
                    $draftIds = explode("//", $val->draft_id);

                    foreach ($draftIds as $draftId) {
                        ReconcileDraft::where('id', $draftId)->update([
                            'status_reconcile' => 'approved',
                            'status' => $val->status,
                            'reconcile_date' => Carbon::now(),
                            'modified_by' => Auth::user()->username,
                        ]);

                    }

                    // Pisahkan bo_id berdasarkan delimiter '//'
                    $boIds = explode("//", $val->bo_id);

                    foreach ($boIds as $boId) {
                        DraftBackOffice::where('id', $boId)->update([
                            'status_reconcile' => 'approved',
                            'reconcile_date' => Carbon::now(),
                        ]);
                    }


                    ReconcileList::where('token_applicant', $val->token_applicant)
                        ->update([
                            'status' => "approved",
                            'reconcile_date' => Carbon::now(),
                        ]);
                    UploadBank::where('token_applicant', $val->token_applicant)
                        ->update([
                            'is_reconcile' => true,
                        ]);
                    UploadBankDetail::where('token_applicant', $val->token_applicant)
                        ->update([
                            'is_reconcile' => true,
                        ]);

                    // Update status_reconcile pada objek $val
                    $val->status_reconcile = "approved";
                    $val->save();
                }
                return response()->json(['message' => ['Success Approve Data!'], 'status' => true], 200);
            }
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }
    public function storeReport($id)
    {
        $data = ReconcileDraft::where('id', $id)->first();
        $rep = ReconcileReport::where('token_applicant', $data->token_applicant)
            ->where('draft_id', $data->id)->first();
        try {
            $statementId = $data->statement_id;
            // //Log::info($statementId);

            if (is_null($statementId)) {
                return response()->json(['message' => ['Statement ID is Null!'], 'status' => false], 200);
            }

            if ($data) {
                if ($rep) {
                    $rep->status_reconcile = "report";
                    $rep->save();
                } else {
                    ReconcileReport::create([
                        'draft_id' => $data->id,
                        'bo_id' => $data->bo_id,
                        'bo_date' => "",
                        'token_applicant' => $data->token_applicant,
                        'statement_date' => $data->settlement_date,
                        'statement_id' => $data->statement_id,
                        'request_id' => $data->request_id,
                        'status' => $data->status,
                        'tid' => $data->tid,
                        'mid' => $data->mid,
                        'batch_fk' => $data->batch_fk,
                        'trx_counts' => $data->trx_counts,
                        'total_sales' => $data->total_sales,
                        'processor_payment' => $data->processor_payment,
                        'internal_payment' => $data->internal_payment,
                        'merchant_payment' => $data->merchant_payment,
                        'merchant_id' => $data->merchant_id,
                        'merchant_name' => $data->merchant_name,
                        'tax_payment' => $data->tax_payment,
                        'fee_mdr_merchant' => $data->fee_mdr_merchant,
                        'fee_bank_merchant' => $data->fee_bank_merchant,
                        'bank_transfer' => $data->bank_transfer,
                        'transfer_amount' => $data->transfer_amount,
                        'bank_settlement_amount' => $data->bank_settlement_amount,
                        // 'dispute_amount' => $data->dispute_amount,
                        'created_by' => $data->created_by,
                        'modified_by' => $data->modified_by,
                        'settlement_date' => $data->settlement_date,
                        'variance' => $data->variance,
                        'bank_id' => $data->bank_id,
                        'category_report' => 'system',
                        'status_manual' => false,
                        'status_reconcile' => "report",
                    ]);
                }
                // $data->status = "deleted";
                $data->status_manual = false;
                $data->status_reconcile = "report";
                $data->save();
                return response()->json(['message' => ['Successfully reconcile data!'], 'status' => true], 200);
            } else {
                return response()->json(['message' => ['Error while store reconcile'], 'status' => false], 200);
            }

        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }
    public function unmatchstore($id)
    {
        try {
            $data = ReconcileDraft::find($id);


            if ($data) {
                $statementId = $data->statement_id;
                // //Log::info($statementId);

                if (is_null($statementId)) {
                    return response()->json(['message' => ['Statement ID is Null!'], 'status' => false], 200);
                }

                // Explode jika ada '//', jika tidak tetap gunakan array dengan satu elemen
                $settlementID = strpos($statementId, '//') !== false ? explode('//', $statementId) : [$statementId];
                // //Log::info($settlementID);
                // die();

                // Array untuk menyimpan data yang akan dimasukkan ke ReconcileUnmatch
                $reconcileData = [];
                foreach ($settlementID as $id) {

                    // Ambil semua data dari UploadBankDetail untuk settlementID yang ditemukan
                    $settlements = UploadBankDetail::where('id', $id)->first();

                    if ($settlements == null) {
                        Log::error("Data UploadBankDetail dengan ID $id tidak ditemukan.");
                        return response()->json(['message' => ['Data Statement dengan ID ini tidak ditemukan.'], 'status' => false], 200);
                    } else {
                        $reconcileData[] = [
                            'draft_id' => $data->id,
                            'statement_id' => $id,
                            'name' => $data->name,
                            'merchant_name' => $data->merchant_name,
                            'token_applicant' => $data->token_applicant,
                            'status' => "NOT_MATCH",
                            'mid' => $data->mid,
                            'trx_counts' => $data->trx_counts,
                            'bank_transfer' => $data->bank_transfer,
                            'tax_payment' => $data->tax_payment,
                            'fee_mdr_merchant' => $data->fee_mdr_merchant,
                            'fee_bank_merchant' => $data->fee_bank_merchant,
                            'total_sales' => $data->total_sales,
                            'processor_payment' => $data->processor_payment,
                            'internal_payment' => $settlements->amount_credit, // Ambil dari pluck hasil query
                            'merchant_payment' => $data->merchant_payment,
                            'merchant_id' => $data->merchant_id,
                            'transfer_amount' => $data->transfer_amount,
                            'bank_settlement_amount' => $data->bank_settlement_amount,
                            'created_by' => $data->created_by,
                            'variance' => $data->variance,
                            'modified_by' => $data->modified_by,
                            'status_parnert' => $data->status_parnert,
                            'status_reconcile' => false,
                            'settlement_date' => $data->settlement_date,
                        ];
                    }
                }
                $oldRec = ReconcileReport::where('mid', $data->mid)
                    ->whereDate('settlement_date', Carbon::parse($data->settlement_date))
                    ->first();

                if ($oldRec) {
                    $oldRec->delete();
                }

                // Batch insert untuk mempercepat proses
                ReconcileUnmatch::insert($reconcileData);

                // Update status di DraftBackOffice
                DraftBackOffice::where('id', $data->bo_id)->update([
                    'status_reconcile' => NULL,
                    'reconcile_date' => NULL,
                ]);

                // Update status di ReconcileDraft
                $data->update([
                    'status_manual' => true,
                    'status_reconcile' => 'manual',
                    'status' => 'deleted',
                ]);

                return response()->json(['message' => ['Successfully reconcile data!'], 'status' => true], 200);

            } else {
                return response()->json(['message' => ['Error while store reconcile'], 'status' => false], 200);
            }


        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }

    public function draftstore(Request $request)
    {
        // dd($request->all());
        if ($request->filled('bo_date')) {
            $splitDate = explode(' - ', $request->bo_date);
            $BoStartDate = date('Y-m-d', strtotime($splitDate[0]));
            $BoEndDate = date('Y-m-d', strtotime($splitDate[1]));

            $start = date('d-m-Y', strtotime($splitDate[0]));
            $end = date('d-m-Y', strtotime($splitDate[1]));
        }
        $fileset = $request->filesettle;
        $channel = $request->channel;
        // //Log::info($channel);
        // $bank = Channel::where('channel', $channel)->first();
        $name = $request->name;
        $filehead = UploadBank::where('url', $fileset)->first();
        $user = Auth::user();
        // $detail = UploadBankDetail::where('token_applicant',$filehead->token_applicant)->get();
        // dd($filehead);

        $reconResult = false;
        try {

            $boData = InternalBatch::selectRaw('
                    SUM(transaction_count) as transaction_count,
                    SUM(bank_transfer) as bank_transfer,
                    SUM(fee_mdr_merchant) as fee_mdr_merchant,
                    SUM(fee_bank_merchant) as fee_bank_merchant,
                    SUM(tax_payment) as tax_payment,
                    SUM(transaction_amount) as transaction_amount,
                    SUM(total_sales_amount) as total_sales_amount,
                    merchant_id,
                    merchant_name,
                    mid,
                    DATE(created_at) as created_date,
                    id,bank_id,tid,merchant_name,processor,batch_running_no,merchant_id,mid_ppn,
                    settlement_audit_id,tax_payment,created_by,created_at,status,settlement_date
                ')
                ->where(DB::raw('DATE(settlement_date)'), '>=', $BoStartDate)
                ->where(DB::raw('DATE(settlement_date)'), '<=', $BoEndDate)
                ->where('bank_id', $channel)
                ->where('status', 'SUCCESSFUL')
                ->groupBy(
                    'mid',
                    'merchant_id',
                    'created_date',
                    'merchant_name',
                    'id',
                    'bank_id',
                    'tid',
                    'merchant_name',
                    'processor',
                    'batch_running_no',
                    'merchant_id',
                    'mid_ppn',
                    'settlement_audit_id',
                    'tax_payment',
                    'created_by',
                    'created_at',
                    'status',
                    'settlement_date'
                )
                ->get();

            if ($boData->isEmpty()) {
                //Log::info('Data BO EMPTY = ' . $boData->isEmpty());
                // ReconcileList::where('token_applicant', $list->token_applicant)->delete();
                return response()->json(
                    [
                        'message' => ['Data Back Office ' . $start . ' until ' . $end . ' Not Found '],
                        'status' => false
                    ],
                    200
                );
            } else {


                // $reconResult = Reconcile::midBoBankDraft($BoStartDate, $BoEndDate, $filehead->token_applicant, $name, $list->token_applicant);
                if ($channel == '7' || $channel == '5') {
                    $list = ReconcileList::create([
                        'name' => $name,
                        'type' => "mid",
                        // 'token_applicant' => Str::uuid(),
                        'token_applicant' => $filehead->token_applicant,
                        'settlement_file' => $filehead->url,
                        'bo_date' => $request->bo_date,
                        'status' => "draft",
                        "is_parnert" => false,
                        "reconcile_date" => Carbon::now(),
                        "reconcile_by" => $user->name,
                    ]);
                    $reconResult = Reconcile::midBoBankDraft($BoStartDate, $BoEndDate, $filehead->token_applicant, $name, $list->token_applicant, $channel);
                } elseif ($channel == '36') {
                    $list = ReconcileList::create([
                        'name' => $name,
                        'type' => "vlookup",
                        // 'token_applicant' => Str::uuid(),
                        'token_applicant' => $filehead->token_applicant,
                        'settlement_file' => $filehead->url,
                        'bo_date' => $request->bo_date,
                        'status' => "draft",
                        "is_parnert" => false,
                        "reconcile_date" => Carbon::now(),
                        "reconcile_by" => $user->name,
                    ]);
                    $reconResult = Reconcile::vlookupBoBank($BoStartDate, $BoEndDate, $filehead->token_applicant, $name, $list->token_applicant, $channel);
                }
                // dd($reconResult);

                if ($reconResult == false) {
                    return response()->json(['message' => ['Error while reconciling, please try again'], 'status' => false], 200);
                } else {
                    $filehead->is_reconcile = true;
                    $filehead->save();
                    return response()->json(['message' => ['Successfully reconcile data!'], 'status' => true], 200);
                }
            }
        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }

    public function reconcilelistdestroy($token)
    {
        // try {

        //     $up = ReconcileList::where('token_applicant', $token)->first();

        //     $ub = UploadBank::where('url', $up->settlement_file)->select('token_applicant')->first();

        //     $getUp = UploadBank::where('url', $up->settlement_file)->update([
        //         'is_reconcile' => 0,
        //     ]);

        //     $getUD = UploadBankDetail::where('token_applicant', $ub->token_applicant)->update([
        //         'is_reconcile' => false,
        //     ]);


        //     if ($up) {
        //         // Menghapus data dari tabel UploadBank
        //         $uploadBank = ReconcileList::where('token_applicant', $token)->delete();
        //         $uploadReport = ReconcileReport::where('token_applicant', $token)->delete();
        //         $draftBO = DraftBackOffice::where('draft_token', $token)->delete();

        //         // Menghapus data dari tabel UploadBankDetail
        //         $uploadDetail = ReconcileDraft::where('token_applicant', $token)->delete();
        //         ReconcileUnmatch::where('token_applicant', $token)->delete();
        //         ReconcileReport::where('token_applicant', $token)->delete();

        //         // Mengembalikan respons sukses jika kedua operasi penghapusan berhasil
        //         if ($uploadBank && $uploadDetail && $draftBO && $uploadReport) {
        //             UploadBankDetail::where('token_applicant', $getUp->token_applicant)->update([
        //                 'is_reconcile' => false
        //             ]);

        //             return response()->json(['success' => true, 'message' => 'Berhasil Hapus Data'], 200);
        //         }
        //     } else {
        //         // Mengembalikan respons kesalahan jika salah satu operasi penghapusan gagal
        //         return response()->json(['message' => 'Error while deleting data', 'status' => false], 200);
        //     }

        // } 


        try {
            $up = ReconcileList::where('token_applicant', $token)->first();

            if ($up) {
                $ub = UploadBank::where('url', $up->settlement_file)->select('token_applicant')->first();

                if ($ub) {
                    // Update 'is_reconcile' pada UploadBankDetail
                    UploadBankDetail::where('token_applicant', $ub->token_applicant)->update([
                        'is_reconcile' => false,
                    ]);

                    // Update 'is_reconcile' pada UploadBank
                    UploadBank::where('url', $up->settlement_file)->update([
                        'is_reconcile' => 0,
                    ]);

                    // Hapus data dari tabel yang relevan
                    ReconcileList::where('token_applicant', $token)->delete();
                    ReconcileReport::where('token_applicant', $token)->delete();
                    DraftBackOffice::where('draft_token', $token)->delete();
                    ReconcileDraft::where('token_applicant', $token)->delete();
                    ReconcileUnmatch::where('token_applicant', $token)->delete();

                    return response()->json(['success' => true, 'message' => 'Berhasil Hapus Data'], 200);
                } else {
                    return response()->json(['message' => 'UploadBank tidak ditemukan', 'status' => false], 200);
                }
            } else {
                return response()->json(['message' => 'ReconcileList tidak ditemukan', 'status' => false], 200);
            }

        } catch (\Throwable $th) {
            // Mencatat kesalahan jika terjadi kesalahan
            //Log::info($th);

            // Mengembalikan respons kesalahan
            return response()->json(['message' => 'Error while deleting data', 'status' => false], 200);
        }
    }
    public function store(Request $request)
    {
        if ($request->filled('bo_date') && $request->filled('bs_date')) {
            $splitDate = explode(' - ', $request->bo_date);
            $BoStartDate = date('Y-m-d', strtotime($splitDate[0]));
            $BoEndDate = date('Y-m-d', strtotime($splitDate[1]));

            $BsSplitDate = explode(' - ', $request->bs_date);
            $BsStartDate = date('Y-m-d', strtotime($BsSplitDate[0]));
            $BsEndDate = date('Y-m-d', strtotime($BsSplitDate[1]));

        }

        // $channel = Channel::where('channel', $request->bank)->first();
        // $bankId = Utils::getChannelBankId($request->bank);
        $bankId = $request->bank;
        $parameter = BankParameter::where('channel_id', $bankId)->first();

        $reconResult = false;
        try {
            if (!$parameter) {
                return response()->json(['message' => ['Reconcile Parameter is not setting yet.'], 'status' => false], 200);
            }
            if ($parameter->bo_summary == 'mid' && $parameter->bank_statement == 'mid') {
                $reconResult = Reconcile::midBoBank($BoStartDate, $BoEndDate, $bankId, $BsStartDate, $BsEndDate);
            } else if ($parameter->bo_summary == 'vlookup' && $parameter->bank_statement == 'vlookup') {
                $reconResult = Reconcile::vlookupBoBank($BoStartDate, $BoEndDate, $bankId, $BsStartDate, $BsEndDate);
            } else if ($parameter->report_partner == 'rrn' && $parameter->bo_detail_transaction == 'rrn') {
                $reconResult = Reconcile::rrnBoPartner($BoStartDate, $BoEndDate, $bankId, $BsStartDate, $BsEndDate);
            } else {
                return response()->json(['message' => ['Reconcile Parameter is not setting yet.'], 'status' => false], 200);
            }

            if (!$reconResult) {
                return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
            }
            return response()->json(['message' => ['Successfully reconcile data!'], 'status' => true], 200);
        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }

    public function reportapprovedetail($token)
    {
        $banks = Channel::with('parameter')
            ->where('status', 'active')
            ->whereHas('parameter')
            ->get();

        $disp = ReconcileReport::where('token_applicant', $token)
            ->where('status_reconcile', 'approved')
            ->where('variance', '>', 0)
            ->pluck('variance')->sum();
        $dispcount = ReconcileReport::where('token_applicant', $token)
            ->where('status_reconcile', 'approved')
            ->where('variance', '>', 0)
            ->pluck('variance')->count();

        $status = request()->query('status');

        $query1 = ReconcileReport::query();
        $query2 = ReconcileReport::query();
        $query3 = ReconcileReport::query();
        $query4 = ReconcileReport::query();
        $query5 = ReconcileReport::query();
        $query6 = ReconcileReport::query();

        $report = ReconcileReport::where('status_reconcile', 'approved')->pluck('id');

        if ($token) {
            $query1->where('token_applicant', $token);
            $query2->where('token_applicant', $token);
            $query3->where('token_applicant', $token);
            $query4->where('token_applicant', $token);
            $query5->where('token_applicant', $token);
            $query6->where('token_applicant', $token);
        }

        $resmatch = $query1->where('status', 'MATCH')->count();
        $resdispute = $quer->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])->count();
        $resonHold = $query3querwhere('status', 'ON_HOLD')->count();

        $ressumMatch = $query4->where('status', 'MATCH')->sum('total_sales');
        $ressumDispute = $query5->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])->sum('total_sales');
        $ressumHold = $query6->where('status', 'ON_HOLD')->sum('total_sales');


        return view(
            'modules.reconcile.list.detail',
            compact(
                'disp',
                'dispcount',
                'banks',
                'resmatch',
                'resdispute',
                'resonHold',
                'ressumMatch',
                'ressumDispute',
                'ressumHold',
                'report'
            )
        );
    }
    public function reconcilelistdetail($token)
    {
        $banks = Channel::with('parameter')
            ->where('status', 'active')
            ->whereHas('parameter')
            ->get();

        $data = ReconcileDraft::where('token_applicant', $token)->where('status', '!=', 'deleted')->get();

        $countdata = ReconcileDraft::where('token_applicant', $token)
            // ->where('status_manual', '0')
            // ->where('status_reconcile', '!=', 'reconciled')
            // ->where('status_reconcile', '!=', 'checker')
            // ->where('status', '!=', 'deleted')
            ->count();
        $syaratauto = ReconcileDraft::where('token_applicant', $token)
            ->where('status_manual', '0')
            ->where('status_reconcile', '!=', 'approved')
            ->where('status_reconcile', '!=', 'pending')
            ->where('status_reconcile', '!=', 'checker')
            ->where('status_reconcile', '!=', 'report')
            ->where('status', '!=', 'deleted')
            ->count();

        $countapprov = ReconcileDraft::where('token_applicant', $token)
            ->where('status', '!=', 'deleted')->where('status_reconcile', '!=', 'reconciled')->count();

        $approved = ReconcileReport::where('token_applicant', $token)
            ->where('status_reconcile', 'approved')
            ->where('status', 'MATCH')
            ->count();

        $checker = ReconcileReport::where('token_applicant', $token)
            ->where('status_reconcile', 'pending')
            ->where('status', 'MATCH')
            ->count();

        $reportapprov = ReconcileReport::where('token_applicant', $token)
            ->where('status', 'MATCH')->where('status_reconcile', 'approved')->count();

        $reportcount = ReconcileReport::where('token_applicant', $token)
            ->where('status', 'MATCH')
            ->where('status_reconcile', 'report')
            ->count();

        $checkcount = ReconcileReport::where('token_applicant', $token)
            ->where('status', 'MATCH')
            ->where('status_reconcile', 'pending')
            ->count();

        $match = ReconcileDraft::where('token_applicant', $token)
            ->where('status', 'MATCH')
            ->count();

        $disp = ReconcileReport::where('token_applicant', $token)
            ->where('variance', '>', 0)
            ->pluck('variance')->sum();
        $dispcount = ReconcileReport::where('token_applicant', $token)
            ->where('variance', '>', 0)
            ->pluck('variance')->count();

        $status = request()->query('status');

        $query1 = ReconcileReport::query();
        $query2 = ReconcileReport::query();
        $query3 = ReconcileReport::query();
        $query4 = ReconcileReport::query();
        $query5 = ReconcileReport::query();
        $query6 = ReconcileReport::query();

        $report = ReconcileReport::where('token_applicant', $token)->pluck('id');

        if ($token) {
            $query1->where('token_applicant', $token);
            $query2->where('token_applicant', $token);
            $query3->where('token_applicant', $token);
            $query4->where('token_applicant', $token);
            $query5->where('token_applicant', $token);
            $query6->where('token_applicant', $token);
        }

        $resmatch = $query1->where('status', 'MATCH')->count();
        $resdispute = $query2->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])->count();
        $resonHold = $query3->where('status', 'ON_HOLD')->count();

        $ressumMatch = $query4->where('status', 'MATCH')->sum('total_sales');
        $ressumDispute = $query5->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])->sum('total_sales');
        $ressumHold = $query6->where('status', 'ON_HOLD')->sum('total_sales');

        $totalTransfer = ReconcileDraft::where('token_applicant', $token)->sum('transfer_amount');
        $variance = ReconcileDraft::where('token_applicant', $token)->sum('variance');
        $sales = ReconcileDraft::where('token_applicant', $token)->sum('total_sales');
        $totalBankTransfer = ReconcileDraft::where('token_applicant', $token)->sum('bank_transfer');

        $unmatch = ReconcileDraft::where('token_applicant', $token)
            ->where('status', 'NOT_MATCH')
            ->orWhere('status_reconcile', 'manual')
            ->count();
        $onhold = ReconcileDraft::where('token_applicant', $token)->where('status', 'ONHOLD')->count();
        $draft = ReconcileDraft::where('token_applicant', $token)->where('status_reconcile', 'draft')->count();
        $approv = ReconcileDraft::where('token_applicant', $token)->where('status_reconcile', 'approved')->count();

        return view(
            'modules.reconcile.list.detail',
            compact(
                'token',
                'checkcount',
                'syaratauto',
                'approved',
                'reportcount',
                'reportapprov',
                'countapprov',
                'disp',
                'dispcount',
                'sales',
                'variance',
                'totalTransfer',
                'totalBankTransfer',
                'data',
                'match',
                'approv',
                'unmatch',
                'draft',
                'onhold',
                'countdata',
                'banks',
                'resmatch',
                'resdispute',
                'resonHold',
                'ressumMatch',
                'ressumDispute',
                'ressumHold',
                'report'
            )
        );
    }
    public function datareconcilelistdetail($token, Request $request)
    {
        // //Log::info('Custom filter:'.$request->status);
        $token_applicant = request()->query('token');
        $status = request()->query('status');

        $query = ReconcileDraft::where('status', '!=', 'deleted')
            ->where('status_reconcile', '!=', 'reconciled')
            ->where('status_reconcile', '!=', 'checker')
            ->where('status_reconcile', '!=', 'report')
            ->where('token_applicant', $token);

        if ($token_applicant) {
            $query->where('token_applicant', $token_applicant);
        }

        if ($request->input('startDate') && $request->input('endDate')) {
            $startDate = $request->startDate;
            $endDate = $request->endDate;

            $query->whereDate('settlement_date', '>=', $startDate)
                ->whereDate('settlement_date', '<=', $endDate);
        } else {

        }

        if ($request->input('status')) {
            // dd($request->all());
            $query->where('status', $request->status);
        }


        return DataTables::of($query->get())->addIndexColumn()->make(true);
    }

    public function Oldestreconcile(Request $request)
    {
        $user = Auth::user();
        if (!isset($request->selectedBo)) {
            return response()->json(['message' => ["Please select Back Office Settlement!"], 'status' => false], 200);
        }
        if (!isset($request->selectedBank)) {
            return response()->json(['message' => ["Please select Bank Settlement!"], 'status' => false], 200);
        }

        $selectedBo = explode(',', $request->selectedBo);
        $selectedBank = explode(',', $request->selectedBank);

        $trxCount = 0;
        $boSettlement = 0;
        $feeMdrMerchant = 0;
        $feeBankMerchant = 0;
        $taxPayment = 0;
        $totalSales = 0;
        $sumTransaction = 0;
        $merchantPayment = 0;
        $bankSettlement = 0;
        $batchMid = '';


        foreach ($selectedBo as $key => $value) {
            // $transaction = InternalTransaction::with('header')->where('id', $value)->first();
            $internalBatch = InternalBatch::where('id', $value)->first();

            $trxCount = $trxCount + $internalBatch->transaction_count;
            $boSettlement = $boSettlement + $internalBatch->bank_transfer;
            $feeMdrMerchant = $feeMdrMerchant + $internalBatch->fee_mdr_merchant;
            $feeBankMerchant = $feeBankMerchant + $internalBatch->fee_bank_merchant;
            $taxPayment = $taxPayment + $internalBatch->tax_payment;
            $totalSales = $totalSales + $internalBatch->transaction_amount;
            $merchant_id = $internalBatch->merchant_id;
            $sumTransaction = $sumTransaction + $internalBatch->transaction_amount;
            $batchMid = $internalBatch->mid;

            $merchantPayment = $merchantPayment + Utils::calculateMerchantPayment($boSettlement, $feeMdrMerchant, $feeBankMerchant, $taxPayment);
        }

        foreach ($selectedBank as $key => $value) {
            $bank = UploadBankDetail::where('id', $value)->first();
            // $sumBank = $sumBank + (float)$bank->amount_credit;
            // $amount_credit = $amount_credit + $bank->amount_credit;
            $bankSettlement = $bankSettlement + (float) $bank->amount_credit;
        }

        $rounded_value = round((int) $bankSettlement);
        $amount_credit = number_format($rounded_value, 0, '', '');

        $diff = abs((float) $boSettlement - (float) $bankSettlement);

        $treshold = Utils::calculateTreshold($trxCount);
        $status = Utils::getStatusReconcile($treshold, $boSettlement, $bankSettlement);

        $diff = abs((float) $boSettlement - (float) $bankSettlement);

        if ($status == "MATCH") {
            foreach ($selectedBank as $key => $value) {
                $det = UploadBankDetail::with('header')->where('id', $value)->first();
                // $internalBatch = InternalBatch::where('mid', 'like', '%' . $value->mid . '%')->get();
                $carbonDate = $det->transfer_date;
                // dd(date('Y-m-d', $carbonDate));
                $carbonDateParsed = Carbon::parse($carbonDate);
                $oldRec = ReconcileResult::where('mid', $batchMid)
                    ->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])
                    ->whereDate('settlement_date', $carbonDateParsed)
                    ->first();
                if ($oldRec) {
                    $oldRec->status = 'deleted';
                    $oldRec->modified_by = $user->name;
                    $oldRec->save();
                }
                $reconcile = ReconcileResult::create([
                    'token_applicant' => $det->token_applicant,
                    'statement_id' => $det->id,
                    'request_id' => $det->header->id,
                    'status' => $status,
                    'mid' => $batchMid,
                    'trx_counts' => $trxCount, // total transaksi 1 batch
                    'total_sales' => $totalSales, // sum transaction_amout di internal_taransaction 
                    'processor_payment' => $det->description2,
                    'internal_payment' => $boSettlement, // bank_payment
                    'merchant_payment' => $merchantPayment, // bank_payment - merchant_fee_amount
                    'merchant_id' => $merchant_id,
                    'transfer_amount' => $sumTransaction, // transaction_amount di internal_batch
                    'bank_settlement_amount' => $amount_credit, // bank_settlement
                    // 'dispute_amount' => $diff, // dispute_amount
                    'created_by' => $user->name,
                    'modified_by' => $user->name,
                    'settlement_date' => $carbonDate
                ]);
                if ($reconcile) {
                    $det->is_reconcile = true;
                    $det->save();
                }
                return response()->json(['message' => 'Successfully Reconcile data!', 'status' => true], 200);
            }
            return response()->json(['message' => ['Failed Reconcile Data!'], 'status' => false], 200);
        }
        return response()->json(['message' => ['Data Not Match!'], 'status' => false], 200);
    }

    public function reconcile19Agus($token, Request $request)
    {
        $user = Auth::user();

        if (!isset($request->selectedBo)) {
            return response()->json(['message' => ["Please select Back Office Settlement!"], 'status' => false], 200);
        }

        if (!isset($request->selectedBank)) {
            return response()->json(['message' => ["Please select Bank Settlement!"], 'status' => false], 200);
        }

        $selectedBo = explode(',', $request->selectedBo);
        $selectedBank = explode(',', $request->selectedBank);

        // Initialize variables
        $trxCount = 0;
        $boSettlement = 0;
        $feeMdrMerchant = 0;
        $feeBankMerchant = 0;
        $taxPayment = 0;
        $totalSales = 0;
        $sumTransaction = 0;
        $merchantPayment = 0;
        $bankSettlement = 0;

        $boIds = [];
        $bankIds = [];
        $merchant_id = null;
        $bank_id = null;

        foreach ($selectedBo as $boKey => $boValue) {
            $internalBatch = DraftBackOffice::where('id', $boValue)->first();
            $batchMid = $internalBatch->mid;

            foreach ($selectedBank as $bankKey => $bankValue) {
                $bank = ReconcileUnmatch::where('id', $bankValue)->first();

                // Check if MID matches
                if ($bank->mid == $batchMid) {
                    // Collect data
                    $trxCount += $internalBatch->transaction_count;
                    $boSettlement += $internalBatch->bank_transfer;
                    $feeMdrMerchant += $internalBatch->fee_mdr_merchant;
                    $feeBankMerchant += $internalBatch->fee_bank_merchant;
                    $taxPayment += $internalBatch->tax_payment;
                    $totalSales += $internalBatch->transaction_amount;
                    $sumTransaction += $internalBatch->transaction_amount;
                    $merchant_id = $internalBatch->merchant_id;
                    $bank_id = $internalBatch->bank_id;

                    $merchantPayment += Utils::calculateMerchantPayment($boSettlement, $feeMdrMerchant, $feeBankMerchant, $taxPayment);
                    $bankSettlement += (float) $bank->bank_transfer;

                    $boIds[] = $boValue;
                    $bankIds[] = $bankValue;

                    // Calculate reconciliation status
                    $rounded_value = round((int) $bankSettlement);
                    $amount_credit = number_format($rounded_value, 0, '', '');
                    $diff = abs((float) $boSettlement - (float) $bankSettlement);
                    $treshold = Utils::calculateTreshold($trxCount);
                    $status = Utils::getStatusReconcile($treshold, $boSettlement, $bankSettlement);

                    // Handle reconciliation report creation
                    $carbonDate = Carbon::parse($bank->settlement_date);
                    $oldRec = ReconcileReport::where('mid', $batchMid)
                        ->where('mid', $bank->mid)
                        // ->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])
                        ->whereDate('settlement_date', $carbonDate)
                        ->first();

                    if ($oldRec) {
                        $oldRec->draft_id = $oldRec->draft_id . "//" . $bankValue;
                        $oldRec->bo_id = $oldRec->bo_id . "//" . $boValue;
                        $oldRec->total_sales += $totalSales;
                        $oldRec->internal_payment += $boSettlement;
                        $oldRec->merchant_payment += $merchantPayment;
                        $oldRec->tax_payment += $taxPayment;
                        $oldRec->fee_mdr_merchant += $feeMdrMerchant;
                        $oldRec->fee_bank_merchant += $feeBankMerchant;
                        $oldRec->bank_transfer += $boSettlement;
                        $oldRec->transfer_amount += $sumTransaction;
                        $oldRec->bank_settlement_amount += $amount_credit;
                        // $oldRec->dispute_amount += $diff;
                        $oldRec->variance += $diff;
                        $oldRec->status = $status;
                        $oldRec->modified_by = $user->name;
                        $oldRec->save();
                    } else {
                        // Create reconciliation report
                        $reconcile = ReconcileReport::create([
                            'draft_id' => $bankValue,
                            'bo_id' => $boValue,
                            'token_applicant' => $bank->token_applicant,
                            'statement_date' => $bank->settlement_date,
                            'status' => $status,
                            'tid' => $bank->tid,
                            'mid' => $bank->mid,
                            'trx_counts' => $trxCount,
                            'total_sales' => $totalSales,
                            'processor_payment' => $bank->processor_payment,
                            'internal_payment' => $boSettlement,
                            'merchant_payment' => $merchantPayment,
                            'merchant_id' => $merchant_id,
                            'merchant_name' => $bank->merchant_name,
                            'tax_payment' => $taxPayment,
                            'fee_mdr_merchant' => $feeMdrMerchant,
                            'fee_bank_merchant' => $feeBankMerchant,
                            'bank_transfer' => $boSettlement,
                            'transfer_amount' => $sumTransaction,
                            'bank_settlement_amount' => $amount_credit,
                            // 'dispute_amount' => $diff,
                            'created_by' => $user->name,
                            'modified_by' => $user->name,
                            'settlement_date' => $carbonDate,
                            'variance' => $diff,
                            'bank_id' => $bank_id,
                            'category_report' => 'manual',
                            'status_manual' => true,
                            'status_reconcile' => 'report',
                            'reconcile_date' => Carbon::now(),
                        ]);
                    }


                    if ($reconcile) {
                        // Update status of DraftBackOffice and ReconcileUnmatch
                        DraftBackOffice::where('id', $boValue)->update([
                            'status_reconcile' => 'reconciled',
                            'reconcile_date' => Carbon::now(),
                        ]);

                        ReconcileUnmatch::where('id', $bankValue)->update([
                            'status' => $status,
                            'modified_by' => $user->name,
                            'status_reconcile' => true,
                            'reconcile_date' => Carbon::now(),
                        ]);

                        // Remove the used bo_id and bank_id from the lists
                        unset($selectedBo[$boKey]);
                        unset($selectedBank[$bankKey]);

                        // Reset the variables after inserting into ReconcileReport
                        $trxCount = 0;
                        $boSettlement = 0;
                        $feeMdrMerchant = 0;
                        $feeBankMerchant = 0;
                        $taxPayment = 0;
                        $totalSales = 0;
                        $sumTransaction = 0;
                        $merchantPayment = 0;
                        $bankSettlement = 0;
                    }

                    // Break the inner loop as we've processed the matching pair
                    break;
                }
            }
        }

        return response()->json(['message' => 'Successfully Reconcile data!', 'status' => true], 200);
    }

    public function reconcilenew($token, Request $request)
    {
        $user = Auth::user();

        if (!isset($request->selectedBo)) {
            return response()->json(['message' => ["Please select Back Office Settlement!"], 'status' => false], 200);
        }

        if (!isset($request->selectedBank)) {
            return response()->json(['message' => ["Please select Bank Settlement!"], 'status' => false], 200);
        }

        $selectedBo = explode(',', $request->selectedBo);
        $selectedBank = explode(',', $request->selectedBank);

        // Initialize variables
        $trxCount = 0;
        $boSettlement = 0;
        $feeMdrMerchant = 0;
        $feeBankMerchant = 0;
        $taxPayment = 0;
        $totalSales = 0;
        $sumTransaction = 0;
        $merchantPayment = 0;
        $bankSettlement = 0;

        $boIds = [];
        $bankIds = [];
        $merchant_id = null;
        $bank_id = null;

        foreach ($selectedBo as $boKey => $boValue) {
            $internalBatch = DraftBackOffice::where('id', $boValue)->first();
            $batchMid = $internalBatch->mid;

            // Find a matching bank record by MID
            $matchingBankKey = null;
            foreach ($selectedBank as $bankKey => $bankValue) {
                $bank = ReconcileUnmatch::where('id', $bankValue)->first();

                if ($bank->mid == $batchMid) {
                    $matchingBankKey = $bankKey;
                    break;
                }
            }

            if ($matchingBankKey !== null) {
                $bankValue = $selectedBank[$matchingBankKey];
                $bank = ReconcileUnmatch::where('id', $bankValue)->first();

                // Collect data
                $trxCount += $internalBatch->transaction_count;
                $boSettlement += $internalBatch->bank_transfer;
                $feeMdrMerchant += $internalBatch->fee_mdr_merchant;
                $feeBankMerchant += $internalBatch->fee_bank_merchant;
                $taxPayment += $internalBatch->tax_payment;
                $totalSales += $internalBatch->transaction_amount;
                $sumTransaction += $internalBatch->transaction_amount;
                $merchant_id = $internalBatch->merchant_id;
                $bank_id = $internalBatch->bank_id;

                $merchantPayment += Utils::calculateMerchantPayment($boSettlement, $feeMdrMerchant, $feeBankMerchant, $taxPayment);
                $bankSettlement += (float) $bank->bank_transfer;

                $boIds[] = $boValue;
                $bankIds[] = $bankValue;

                // Calculate reconciliation status
                $rounded_value = round((int) $bankSettlement);
                $amount_credit = number_format($rounded_value, 0, '', '');
                $diff = abs((float) $boSettlement - (float) $bankSettlement);
                $treshold = Utils::calculateTreshold($trxCount);
                $status = Utils::getStatusReconcile($treshold, $boSettlement, $bankSettlement);

                // Handle reconciliation report creation
                $carbonDate = Carbon::parse($bank->settlement_date);
                $oldRec = ReconcileReport::where('mid', $batchMid)
                    ->where('mid', $bank->mid)
                    ->whereDate('settlement_date', $carbonDate)
                    ->first();

                if ($oldRec) {
                    $oldRec->draft_id = $oldRec->draft_id . "//" . $bankValue;
                    $oldRec->bo_id = $oldRec->bo_id . "//" . $boValue;
                    $oldRec->total_sales += $totalSales;
                    $oldRec->internal_payment += $boSettlement;
                    $oldRec->merchant_payment += $merchantPayment;
                    $oldRec->tax_payment += $taxPayment;
                    $oldRec->fee_mdr_merchant += $feeMdrMerchant;
                    $oldRec->fee_bank_merchant += $feeBankMerchant;
                    $oldRec->bank_transfer += $boSettlement;
                    $oldRec->transfer_amount += $sumTransaction;
                    $oldRec->bank_settlement_amount += $amount_credit;
                    // $oldRec->dispute_amount += $diff;
                    $oldRec->variance += $diff;
                    $oldRec->status = $status;
                    $oldRec->modified_by = $user->name;
                    $oldRec->save();
                } else {
                    // Create reconciliation report
                    $reconcile = ReconcileReport::create([
                        'draft_id' => $bankValue,
                        'bo_id' => $boValue,
                        'token_applicant' => $bank->token_applicant,
                        'statement_date' => $bank->settlement_date,
                        'status' => $status,
                        'tid' => $bank->tid,
                        'mid' => $bank->mid,
                        'trx_counts' => $trxCount,
                        'total_sales' => $totalSales,
                        'processor_payment' => $bank->processor_payment,
                        'internal_payment' => $boSettlement,
                        'merchant_payment' => $merchantPayment,
                        'merchant_id' => $merchant_id,
                        'merchant_name' => $bank->merchant_name,
                        'tax_payment' => $taxPayment,
                        'fee_mdr_merchant' => $feeMdrMerchant,
                        'fee_bank_merchant' => $feeBankMerchant,
                        'bank_transfer' => $boSettlement,
                        'transfer_amount' => $sumTransaction,
                        'bank_settlement_amount' => $amount_credit,
                        // 'dispute_amount' => $diff,
                        'created_by' => $user->name,
                        'modified_by' => $user->name,
                        'settlement_date' => $carbonDate,
                        'variance' => $diff,
                        'bank_id' => $bank_id,
                        'category_report' => 'manual',
                        'status_manual' => true,
                        'status_reconcile' => 'report',
                        'reconcile_date' => Carbon::now(),
                    ]);
                }

                if ($reconcile) {
                    // Update status of DraftBackOffice and ReconcileUnmatch
                    DraftBackOffice::where('id', $boValue)->update([
                        'status_reconcile' => 'reconciled',
                        'reconcile_date' => Carbon::now(),
                    ]);

                    ReconcileUnmatch::where('id', $bankValue)->update([
                        'status' => $status,
                        'modified_by' => $user->name,
                        'status_reconcile' => true,
                        'reconcile_date' => Carbon::now(),
                    ]);

                    // Remove the used bo_id and bank_id from the lists
                    unset($selectedBo[$boKey]);
                    unset($selectedBank[$matchingBankKey]);

                    // Reset the variables after inserting into ReconcileReport
                    $trxCount = 0;
                    $boSettlement = 0;
                    $feeMdrMerchant = 0;
                    $feeBankMerchant = 0;
                    $taxPayment = 0;
                    $totalSales = 0;
                    $sumTransaction = 0;
                    $merchantPayment = 0;
                    $bankSettlement = 0;
                }
            }
        }

        return response()->json(['message' => 'Successfully Reconcile data!', 'status' => true], 200);
    }

    public function reconcile($token, Request $request)
    {
        $user = Auth::user();
        $approved = ReconcileReport::where('token_applicant', $token)
            ->where('status_reconcile', 'approved')
            ->where('status', 'MATCH')
            ->count();

        $checker = ReconcileReport::where('token_applicant', $token)
            ->where('status_reconcile', 'pending')
            ->where('status', 'MATCH')
            ->count();

        $listcheck = ReconcileList::where('token_applicant', $token)
            ->where('status', 'pending')
            ->count();

        if ($approved > 0) {
            return response()->json(['message' => ["This data has been approved! Select Other Data List!"], 'status' => "3"], 200);
        }
        if (
            // $checker > 0 && 
            $listcheck > 0
        ) {
            return response()->json(['message' => ["This data has been sent to Checker! Select Other Data List!"], 'status' => "3"], 200);
        }

        if (!isset($request->selectedBo)) {
            return response()->json(['message' => ["Please select Back Office Settlement!"], 'status' => "3"], 200);
        }

        if (!isset($request->selectedBank)) {
            return response()->json(['message' => ["Please select Bank Settlement!"], 'status' => "3"], 200);
        }

        $selectedBo = explode(',', $request->selectedBo);
        $selectedBank = explode(',', $request->selectedBank);

        $selectedBos = array_unique($selectedBo);
        $selectedBanks = array_unique($selectedBank);

        $selectedBos = array_values($selectedBos);
        $selectedBanks = array_values($selectedBanks);

        // //Log::info($selectedBos);
        // //Log::info("==========================");
        // //Log::info($selectedBanks);

        // Jika ingin melihat hasil setelah elemen yang sama dihapus
        // //Log::info($selectedBo);
        // //Log::info($selectedBank);

        // Initialize variables
        $boData = [];
        $bankData = [];

        foreach ($selectedBos as $boValue) {
            $internalBatch = DraftBackOffice::where('id', $boValue)->first();

            $batchMid = $internalBatch->mid;

            if (!isset($boData[$batchMid])) {
                $boData[$batchMid] = [
                    'bank_id' => $internalBatch->bank_id,
                    'trxCount' => 0,
                    'boSettlement' => 0,
                    'feeMdrMerchant' => 0,
                    'feeBankMerchant' => 0,
                    'taxPayment' => 0,
                    'totalSales' => 0,
                    'sumTransaction' => 0,
                    'merchantPayment' => 0,
                    'boIds' => [],
                ];
            }

            // Collect data
            $boData[$batchMid]['trxCount'] += $internalBatch->transaction_count;
            $boData[$batchMid]['boSettlement'] += $internalBatch->bank_transfer;
            $boData[$batchMid]['feeMdrMerchant'] += $internalBatch->fee_mdr_merchant;
            $boData[$batchMid]['feeBankMerchant'] += $internalBatch->fee_bank_merchant;
            $boData[$batchMid]['taxPayment'] += $internalBatch->tax_payment;
            $boData[$batchMid]['totalSales'] += $internalBatch->transaction_amount;
            $boData[$batchMid]['sumTransaction'] += $internalBatch->transaction_amount;
            $boData[$batchMid]['merchantPayment'] += Utils::calculateMerchantPayment(
                $boData[$batchMid]['boSettlement'],
                $boData[$batchMid]['feeMdrMerchant'],
                $boData[$batchMid]['feeBankMerchant'],
                $boData[$batchMid]['taxPayment']
            );
            $boData[$batchMid]['boIds'][] = $boValue;

        }

        foreach ($selectedBanks as $bankValue) {
            $bank = ReconcileUnmatch::where('id', $bankValue)->first();
            // //Log::info($bank);
            // $draftid = $bank->draft_id;
            $batchMid = $bank->mid;

            if (!isset($bankData[$batchMid])) {
                $bankData[$batchMid] = [
                    'bankSettlement' => 0,
                    'sales' => 0,
                    'bankIds' => [],
                    'StatementIds' => [],
                    // 'bankIds' => $bankValue,
                    'settlement_date' => Carbon::parse($bank->settlement_date),
                    'merchant_name' => $bank->merchant_name,
                    // 'bank_id' => $bank->bank_id,
                    'processor_payment' => $bank->processor_payment,
                    'statement_id' => $bank->statement_id,
                    'tid' => $bank->tid,
                ];
            }

            // Collect data
            $bankData[$batchMid]['bankSettlement'] += $bank->internal_payment;
            $bankData[$batchMid]['sales'] += $bank->total_sales;
            $bankData[$batchMid]['draftIds'][] = $bank->draft_id;
            $bankData[$batchMid]['bankIds'][] = $bankValue;
            $bankData[$batchMid]['StatementIds'][] = $bank->statement_id;
        }


        $messages = [];
        // Process the reconciled data
        foreach ($boData as $mid => $bo) {
            if (isset($bankData[$mid])) {
                $rounded_value = round((int) $bankData[$mid]['bankSettlement']);
                $amount_credit = number_format($rounded_value, 0, '', '');
                $diff = ($bankData[$mid]['bankSettlement'] - $bo['boSettlement']);
                $treshold = Utils::calculateTreshold($bo['trxCount']);
                // $status = Utils::getStatusReconcile($treshold, $bo['boSettlement'], $bankData[$mid]['bankSettlement']);
                // $status = Utils::getStatusReconcile($bo['boSettlement'], $bankData[$mid]['bankSettlement'], $bankData[$mid]['sales']);
                $status = Utils::getNewStatusReconcile2($diff, $bankData[$mid]['sales']);

                // //Log::info($bankData[$mid]);
                // die();

                if ($status == "NOT_MATCH") {
                    // //Log::info($batchMid);
                    // //Log::info($bankData[$batchMid]['bankSettlement']);
                    // //Log::info($bo['boSettlement']);
                    $messages[] = [
                        "<b style='color:#D8000C'>Data Not Match</b> <br> MID: " . $mid . "<br>" .
                        "Variance: Rp. " . $diff . "<br>" .
                        "Percentage: " . number_format(abs($diff / $bank->total_sales) * 100, 2) . " % <br>"
                    ];
                    // return response()->json([
                    //     'message' => [
                    //         "Data Not Match!",
                    //         "Variance = Rp. " . $diff,
                    //         "Percentage = " . (abs($diff / $bank->total_sales) * 100) . "  %",
                    //     ],
                    //     'status' => false
                    // ], 200);
                } else if ($status == "MATCH") {

                    $messages[] = [
                        "<b style='color:#4F8A10'>Data Match</b>" .
                        "<br> MID: " . $mid . "<br>"
                        // . "Variance: Rp. " . $diff . "<br>" .
                        // "Percentage: " . number_format(abs($diff / $bank->total_sales) * 100, 2) . " % <br>"
                    ];

                    // $oldRec = ReconcileReport::where('mid', $mid)
                    //     ->whereDate('settlement_date', Carbon::parse($bank->settlement_date))
                    //     ->first();

                    // // //Log::info($oldRec);
                    // // //Log::info($bankData[$mid]);
                    // // die();
                    // if ($oldRec) {

                    //     if ($oldRec->total_sales == $bankData[$mid]["sales"] || $oldRec->bank_settlement_amount == $bankData[$mid]["bankSettlement"]) {
                    //         $oldRec->variance += $diff;
                    //         $oldRec->status = $status;
                    //         $oldRec->modified_by = $user->name;
                    //         $oldRec->save();
                    //     } else {
                    //         $oldRec->draft_id = $oldRec->draft_id . "//" . implode('//', $bankData[$mid]['bankIds']);
                    //         $oldRec->bo_id = $oldRec->bo_id . "//" . implode('//', $bo['boIds']);
                    //         $oldRec->total_sales += $bo['totalSales'];
                    //         $oldRec->internal_payment += $bo['boSettlement'];
                    //         $oldRec->merchant_payment += $bo['merchantPayment'];
                    //         $oldRec->tax_payment += $bo['taxPayment'];
                    //         $oldRec->fee_mdr_merchant += $bo['feeMdrMerchant'];
                    //         $oldRec->fee_bank_merchant += $bo['feeBankMerchant'];
                    //         $oldRec->bank_transfer += $bo['boSettlement'];
                    //         $oldRec->transfer_amount += $bo['sumTransaction'];
                    //         $oldRec->bank_settlement_amount += $amount_credit;
                    //         // $oldRec->dispute_amount += $diff;
                    //         $oldRec->variance += $diff;
                    //         $oldRec->status = $status;
                    //         $oldRec->modified_by = $user->name;
                    //         $oldRec->save();
                    //     }
                    //     // Update existing record
                    // } else {
                    //     // Create new record
                    //     ReconcileReport::create([
                    //         'draft_id' => implode('//', $bankData[$mid]['bankIds']),
                    //         // 'draft_id' => $bankData[$mid]['bankIds'],
                    //         'bo_id' => implode('//', $bo['boIds']),
                    //         'token_applicant' => $token,
                    //         'statement_id' => implode('//', $bankData[$mid]['StatementIds']),
                    //         // 'statement_id' => $bankData[$mid]['statement_id'],
                    //         // 'token_applicant' => $bank->token_applicant,
                    //         'statement_date' => $bank->settlement_date,
                    //         'status' => $status,
                    //         'tid' => $bank->tid,
                    //         'mid' => $mid,
                    //         'trx_counts' => $bo['trxCount'],
                    //         'total_sales' => $bo['totalSales'],
                    //         'processor_payment' => $bank->processor_payment,
                    //         'internal_payment' => $bo['boSettlement'],
                    //         'merchant_payment' => $bo['merchantPayment'],
                    //         'merchant_id' => $internalBatch->merchant_id,
                    //         'merchant_name' => $bank->merchant_name,
                    //         'tax_payment' => $bo['taxPayment'],
                    //         'fee_mdr_merchant' => $bo['feeMdrMerchant'],
                    //         'fee_bank_merchant' => $bo['feeBankMerchant'],
                    //         'bank_transfer' => $bo['boSettlement'],
                    //         'transfer_amount' => $bo['sumTransaction'],
                    //         'bank_settlement_amount' => $amount_credit,
                    //         // 'dispute_amount' => $diff,
                    //         'created_by' => $user->name,
                    //         'modified_by' => $user->name,
                    //         'settlement_date' => Carbon::parse($bank->settlement_date),
                    //         'variance' => $diff,
                    //         'bank_id' => $bo['bank_id'],
                    //         'category_report' => 'manual',
                    //         'status_manual' => true,
                    //         'status_reconcile' => 'report',
                    //         'reconcile_date' => Carbon::now(),
                    //     ]);
                    // }

                    // // Update status of DraftBackOffice and ReconcileUnmatch
                    // DraftBackOffice::where('id', $bo['boIds'])->update([
                    //     'status_reconcile' => 'reconciled',
                    //     'reconcile_date' => Carbon::now(),
                    // ]);


                    // ReconcileDraft::where('mid', $mid)
                    //     ->whereDate('settlement_date', Carbon::parse($bank->settlement_date))
                    //     ->update([
                    //         // 'token_applicant' => $token,
                    //         'status' => $status,
                    //         'modified_by' => $user->name,
                    //         'status_reconcile' => "report",
                    //         'status_manual' => false,
                    //         'reconcile_date' => Carbon::now(),
                    //     ]);

                    // foreach ($bankData[$mid]['bankIds'] as $val) {
                    //     ReconcileUnmatch::where('id', $val)->update([
                    //         // 'token_applicant' => $token,
                    //         'status' => $status,
                    //         'modified_by' => $user->name,
                    //         'status_reconcile' => true,
                    //         'reconcile_date' => Carbon::now(),
                    //     ]);
                    //     // //Log::info("Value : " . $val);
                    // };

                    // return response()->json(['message' => 'Successfully Reconcile data!', 'status' => true], 200);
                } else {
                    return response()->json(['message' => 'Error', 'status' => true], 200);
                }


            }
        }


        if (!empty($messages)) {
            return response()->json([
                'message' => $messages,
                'status' => false
            ], 200);
        } else {
            return response()->json([
                'message' => 'Successfully Reconcile data!',
                'status' => true
            ], 200);
        }
    }
    public function reconcileproceed($token, Request $request)
    {
        $user = Auth::user();
        $approved = ReconcileReport::where('token_applicant', $token)
            ->where('status_reconcile', 'approved')
            ->where('status', 'MATCH')
            ->count();

        $checker = ReconcileReport::where('token_applicant', $token)
            ->where('status_reconcile', 'pending')
            ->where('status', 'MATCH')
            ->count();

        $listcheck = ReconcileList::where('token_applicant', $token)
            ->where('status', 'pending')
            ->count();

        if ($approved > 0) {
            return response()->json(['message' => ["This data has been approved! Select Other Data List!"], 'status' => "3"], 200);
        }
        if (
            // $checker > 0 && 
            $listcheck > 0
        ) {
            return response()->json(['message' => ["This data has been sent to Checker! Select Other Data List!"], 'status' => "3"], 200);
        }

        if (!isset($request->selectedBo)) {
            return response()->json(['message' => ["Please select Back Office Settlement!"], 'status' => "3"], 200);
        }

        if (!isset($request->selectedBank)) {
            return response()->json(['message' => ["Please select Bank Settlement!"], 'status' => "3"], 200);
        }

        $selectedBo = explode(',', $request->selectedBo);
        $selectedBank = explode(',', $request->selectedBank);

        $selectedBo = array_unique($selectedBo);
        $selectedBank = array_unique($selectedBank);

        // Initialize variables
        $boData = [];
        $bankData = [];

        foreach ($selectedBo as $boValue) {
            $internalBatch = DraftBackOffice::where('id', $boValue)->first();
            $batchMid = $internalBatch->mid;

            if (!isset($boData[$batchMid])) {
                $boData[$batchMid] = [
                    'trxCount' => 0,
                    'boSettlement' => 0,
                    'feeMdrMerchant' => 0,
                    'feeBankMerchant' => 0,
                    'taxPayment' => 0,
                    'totalSales' => 0,
                    'sumTransaction' => 0,
                    'merchantPayment' => 0,
                    'boIds' => [],
                    'merchant_id' => $internalBatch->merchant_id,
                    'bank_id' => $internalBatch->bank_id,
                ];
            }

            // Collect data
            $boData[$batchMid]['trxCount'] += $internalBatch->transaction_count;
            $boData[$batchMid]['boSettlement'] += $internalBatch->bank_transfer;
            $boData[$batchMid]['feeMdrMerchant'] += $internalBatch->fee_mdr_merchant;
            $boData[$batchMid]['feeBankMerchant'] += $internalBatch->fee_bank_merchant;
            $boData[$batchMid]['taxPayment'] += $internalBatch->tax_payment;
            $boData[$batchMid]['totalSales'] += $internalBatch->transaction_amount;
            $boData[$batchMid]['sumTransaction'] += $internalBatch->transaction_amount;
            $boData[$batchMid]['merchantPayment'] += Utils::calculateMerchantPayment(
                $boData[$batchMid]['boSettlement'],
                $boData[$batchMid]['feeMdrMerchant'],
                $boData[$batchMid]['feeBankMerchant'],
                $boData[$batchMid]['taxPayment']
            );
            $boData[$batchMid]['boIds'][] = $boValue;
        }


        foreach ($selectedBank as $bankValue) {
            $bank = ReconcileUnmatch::where('id', $bankValue)->first();
            $batchMid = $bank->mid;

            if (!isset($bankData[$batchMid])) {
                $bankData[$batchMid] = [
                    'bankSettlement' => 0,
                    'sales' => 0,
                    'bankIds' => [],
                    'settlement_date' => Carbon::parse($bank->settlement_date),
                    'merchant_name' => $bank->merchant_name,
                    // 'bank_id' => $bank->bank_id,
                    'StatementIds' => [],
                    'statement_id' => $bank->statement_id,
                    'processor_payment' => $bank->processor_payment,
                    'tid' => $bank->tid,
                ];
            }

            // Collect data
            $bankData[$batchMid]['bankSettlement'] += $bank->internal_payment;
            $bankData[$batchMid]['sales'] += $bank->total_sales;
            $bankData[$batchMid]['draftIds'][] = $bank->draft_id;
            $bankData[$batchMid]['bankIds'][] = $bankValue;
            $bankData[$batchMid]['StatementIds'][] = $bank->statement_id;
        }



        foreach ($boData as $mid => $bo) {


            if (isset($bankData[$mid])) {
                $rounded_value = round((int) $bankData[$mid]['bankSettlement']);
                $amount_credit = number_format($rounded_value, 0, '', '');
                $diff = ($bankData[$mid]['bankSettlement'] - $bo['boSettlement']);
                $treshold = Utils::calculateTreshold($bo['trxCount']);

                // $status = Utils::getNewStatusReconcile2($diff, $bankData[$mid]['sales']);
                $status = "MATCH";

                $settlementDate = Carbon::parse($bank->settlement_date); // Parsing sekali
                $oldRec = ReconcileReport::where('mid', $mid)
                    ->whereDate('settlement_date', $settlementDate)
                    ->first();

                if ($oldRec) {
                    // Siapkan nilai yang sering digunakan
                    $bankIds = implode('//', $bankData[$mid]['draftIds']);
                    $boIds = implode('//', $bo['boIds']);

                    // Update existing record dengan penggabungan dan penambahan nilai
                    $oldRec->draft_id .= "//" . $bankIds;
                    $oldRec->bo_id .= "//" . $boIds;
                    $oldRec->total_sales += $bo['totalSales'];
                    $oldRec->internal_payment += $bo['boSettlement'];
                    $oldRec->merchant_payment += $bo['merchantPayment'];
                    $oldRec->tax_payment += $bo['taxPayment'];
                    $oldRec->fee_mdr_merchant += $bo['feeMdrMerchant'];
                    $oldRec->fee_bank_merchant += $bo['feeBankMerchant'];
                    $oldRec->bank_transfer += $bo['boSettlement'];
                    $oldRec->transfer_amount += $bo['sumTransaction'];
                    $oldRec->bank_settlement_amount += $amount_credit;
                    $oldRec->variance += $diff; // Asumsikan diff sudah dihitung sebelumnya
                    $oldRec->status = $status;
                    $oldRec->modified_by = $user->name;

                    // Simpan perubahan
                    $oldRec->save();
                } else {
                    // Create new record
                    ReconcileReport::create([
                        'manual_id' => implode('//', $bankData[$mid]['bankIds']),
                        'draft_id' => implode('//', $bankData[$mid]['draftIds']),
                        'bo_id' => implode('//', $bo['boIds']),
                        'token_applicant' => $token,
                        'statement_id' => implode('//', $bankData[$mid]['StatementIds']),
                        // 'token_applicant' => $bank->token_applicant,
                        // 'statement_id' => $bankData[$mid]['statement_id'],
                        'statement_date' => $bankData[$mid]['settlement_date'],
                        'status' => $status,
                        'tid' => $bankData[$mid]['tid'],
                        'mid' => $mid,
                        'trx_counts' => $bo['trxCount'],
                        'total_sales' => $bo['totalSales'],
                        'processor_payment' => $bankData[$mid]['processor_payment'],
                        'internal_payment' => $bo['boSettlement'],
                        'merchant_payment' => $bo['merchantPayment'],
                        'merchant_id' => $bo['merchant_id'],
                        'merchant_name' => $bankData[$mid]['merchant_name'],
                        'tax_payment' => $bo['taxPayment'],
                        'fee_mdr_merchant' => $bo['feeMdrMerchant'],
                        'fee_bank_merchant' => $bo['feeBankMerchant'],
                        'bank_transfer' => $bo['boSettlement'],
                        'transfer_amount' => $bo['sumTransaction'],
                        'bank_settlement_amount' => $amount_credit,
                        // 'dispute_amount' => $diff,
                        'created_by' => $user->name,
                        'modified_by' => $user->name,
                        'settlement_date' => $bankData[$mid]['settlement_date'],
                        'variance' => $diff,
                        'bank_id' => $bo['bank_id'],
                        'category_report' => 'manual',
                        'status_manual' => true,
                        'status_reconcile' => 'report',
                        'reconcile_date' => Carbon::now(),
                    ]);
                }

                // Update status of DraftBackOffice and ReconcileUnmatch
                DraftBackOffice::where('id', $bo['boIds'])->update([
                    'status_reconcile' => 'reconciled',
                    'reconcile_date' => Carbon::now(),
                ]);

                foreach ($bankData[$mid]['bankIds'] as $val) {
                    ReconcileUnmatch::where('id', $val)->update([
                        // 'token_applicant' => $token,
                        'status' => $status,
                        'modified_by' => $user->name,
                        'status_reconcile' => true,
                        'reconcile_date' => Carbon::now(),
                    ]);
                    // //Log::info("Value : " . $val);
                }
                ;


            }
        }

        return response()->json(['message' => 'Successfully Reconcile data!', 'status' => true], 200);
    }

    public function reconcilePartner(Request $request)
    {
        $user = Auth::user();
        if (!isset($request->selectedBo)) {
            return response()->json(['message' => ["Please select Back Office Settlement!"], 'status' => false], 200);
        }
        if (!isset($request->selectedBank)) {
            return response()->json(['message' => ["Please select Partner Report!"], 'status' => false], 200);
        }

        $selectedBo = explode(',', $request->selectedBo);
        $selectedBank = explode(',', $request->selectedBank);

        $trxCount = 0;
        $boSettlement = 0;
        $feeMdrMerchant = 0;
        $feeBankMerchant = 0;
        $taxPayment = 0;
        $totalSales = 0;
        $sumTransaction = 0;
        $merchantPayment = 0;
        $bankSettlement = 0;
        $batchMid = '';


        foreach ($selectedBo as $key => $value) {
            $internalTransaction = InternalTransaction::with('header')->where('id', $value)->first();

            $trxCount = $trxCount + 1;
            $boSettlement = $boSettlement + $internalTransaction->bank_payment;
            $feeMdrMerchant = $feeMdrMerchant + $internalTransaction->merchant_fee_amount;
            $feeBankMerchant = $feeBankMerchant + $internalTransaction->bank_fee_amount;
            $taxPayment = $taxPayment + $internalTransaction->tax_amount;
            $totalSales = $totalSales + $internalTransaction->transaction_amount;
            $merchant_id = $internalTransaction->header->merchant_id;
            $sumTransaction = $sumTransaction + $internalTransaction->transaction_amount;
            $batchMid = $internalTransaction->header->mid;

            $merchantPayment = $merchantPayment + Utils::calculateMerchantPayment($boSettlement, $feeMdrMerchant, $feeBankMerchant, $taxPayment);
        }


        foreach ($selectedBank as $key => $value) {
            $bank = ReportPartner::where('id', $value)->first();
            $bankSettlement = $bankSettlement + (float) $bank->net_amount;
        }

        $rounded_value = round((int) $bankSettlement);
        $amount_credit = number_format($rounded_value, 0, '', '');

        $diff = abs((float) $boSettlement - (float) $bankSettlement);

        $treshold = Utils::calculateTreshold($trxCount);
        $status = Utils::getStatusReconcile($treshold, $boSettlement, $bankSettlement);

        $diff = abs((float) $boSettlement - (float) $bankSettlement);

        if ($status == "MATCH") {
            foreach ($selectedBank as $key => $value) {
                $det = ReportPartner::with('header')->where('id', $value)->first();

                $carbonDate = $det->settlement_date;

                $carbonDateParsed = Carbon::parse($carbonDate);
                $oldRec = ReconcileResult::where('mid', $batchMid)
                    ->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])
                    ->whereDate('settlement_date', $carbonDateParsed)
                    ->first();

                if ($oldRec) {
                    $oldRec->status = 'deleted';
                    $oldRec->modified_by = $user->name;
                    $oldRec->save();
                }

                $reconcile = ReconcileResult::create([
                    'token_applicant' => $det->token_applicant,
                    'statement_id' => $det->id,
                    'request_id' => $det->header->id,
                    'status' => $status,
                    'mid' => $batchMid,
                    'trx_counts' => $trxCount, // total transaksi 1 batch
                    'total_sales' => $totalSales, // sum transaction_amout di internal_taransaction 
                    'processor_payment' => $det->channel,
                    'internal_payment' => $boSettlement, // bank_payment
                    'merchant_payment' => $merchantPayment, // bank_payment - merchant_fee_amount
                    'merchant_id' => $merchant_id,
                    'transfer_amount' => $sumTransaction, // transaction_amount di internal_batch
                    'bank_settlement_amount' => $amount_credit, // bank_settlement
                    // 'dispute_amount' => $diff, // dispute_amount
                    'created_by' => $user->name,
                    'modified_by' => $user->name,
                    'settlement_date' => $carbonDate
                ]);
                if ($reconcile) {
                    $det->is_reconcile = true;
                    $det->save();
                }
                return response()->json(['message' => 'Successfully Reconcile data!', 'status' => true], 200);
            }
            return response()->json(['message' => ['Failed Reconcile Data!'], 'status' => false], 200);
        }
        return response()->json(['message' => ['Data Not Match!'], 'status' => false], 200);
    }

    public function result()
    {
        $banks = Channel::with('parameter')
            ->where('status', 'active')
            ->whereHas('parameter')
            ->get();

        $disp = ReconcileReport::where('variance', '>', 0)
            ->where('status_reconcile', 'approved')
            ->pluck('variance')->sum();
        $dispcount = ReconcileReport::where('variance', '>', 0)
            ->where('status_reconcile', 'approved')
            ->pluck('variance')->count();
        $checkapprove = ReconcileReport::where('status_reconcile', 'pending')
            ->count();



        $status = request()->query('status');

        $query1 = ReconcileReport::whereIn('status_reconcile', ['approved', 'pending']);
        $query2 = ReconcileReport::whereIn('status_reconcile', ['approved', 'pending']);
        $query3 = ReconcileReport::whereIn('status_reconcile', ['approved', 'pending']);
        $query4 = ReconcileReport::whereIn('status_reconcile', ['approved', 'pending']);
        $query5 = ReconcileReport::whereIn('status_reconcile', ['approved', 'pending']);
        $query6 = ReconcileReport::whereIn('status_reconcile', ['approved', 'pending']);

        $report = ReconcileReport::pluck('id')->where('status_reconcile', 'approved');

        $resmatch = $query1->where('status', 'MATCH')->count();
        $resdispute = $query2->whereIn('status', ['MATCH'])->count();
        $resonHold = $query3->where('status', 'ON_HOLD')->count();

        $ressumMatch = $query4->where('status', 'MATCH')->sum('total_sales');
        $ressumDispute = $query5->whereIn('status', ['MATCH'])->sum('variance');
        $ressumHold = $query6->where('status', 'ON_HOLD')->sum('total_sales');


        return view(
            'modules.reconcile.show',
            compact(
                'checkapprove',
                'disp',
                'dispcount',
                'banks',
                'resmatch',
                'resdispute',
                'resonHold',
                'ressumMatch',
                'ressumDispute',
                'ressumHold',
                'report'
            )
        );
    }

    public function menuunmatchlist()
    {
        $banks = Channel::with('parameter')
            ->where('status', 'active')
            ->whereHas('parameter')
            ->get();

        $disp = ReconcileDraft::
            where('status_reconcile', '!=', 'approved')
            ->pluck('variance')->sum();

        $status = request()->query('status');

        $query1 = ReconcileDraft::
            where('status_reconcile', '!=', 'approved');
        $query2 = ReconcileDraft::
            where('status_reconcile', '!=', 'approved');
        $query3 = ReconcileDraft::
            where('status_reconcile', '!=', 'approved');
        $query4 = ReconcileDraft::
            where('status_reconcile', '!=', 'approved');
        $query5 = ReconcileDraft::
            where('status_reconcile', '!=', 'approved');
        $query6 = ReconcileDraft::
            where('status_reconcile', '!=', 'approved');

        $report = ReconcileReport::pluck('id')->where('status_reconcile', 'approved');

        $resmatch = $query1->where('status', '!=', 'MATCH')->count();
        $resdispute = $query4->where('status', '!=', 'MATCH')->count();

        $ressumMatch = $query4->where('status', '!=', 'MATCH')->sum('total_sales');
        $ressumDispute = $query4->where('status', '!=', 'MATCH')->sum('variance');


        return view(
            'modules.reconcile.unmatch',
            compact(
                'disp',
                'banks',
                'resmatch',
                'resdispute',
                'ressumMatch',
                'ressumDispute',
                'report'
            )
        );
    }


    public function unmatchlistdata(Request $request)
    {
        $status = $request->query('status');
        $startDate = $request->input('startDate') ?? date('Y-m-d');
        $endDate = $request->input('endDate') ?? date('Y-m-d');

        $token_applicant = request()->query('token');
        $status = request()->query('status');

        $query = ReconcileDraft::with('merchant', 'bank_account')
            ->where('status', '!=', 'MATCH')
            ->where('status_reconcile', '!=', ['reconciled', 'checker'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        // if ($request->input('startDate') && $request->input('endDate')) {
        //     $startDate = $request->startDate;
        //     $endDate = $request->endDate;

        //     $query->whereDate('created_at', '>=', $startDate)
        //         ->whereDate('created_at', '<=', $endDate);
        // }

        if ($request->input('channel') !== null) {
            $query->where('processor_payment', $request->channel);
        }

        $data = $query->get();


        // return DataTables::of($query->get())->addIndexColumn()->make(true);
        $ressumMatch = $data->sum('total_sales');
        $resmatch = $data->count();
        $resdispute = $data->where('variance', '!=', '0')->count();
        $ressumDispute = $data->where('variance', '!=', '0')->sum('variance');

        // //Log::info($ressumMatch);
        // //Log::info($resmatch);
        // //Log::info($resdispute);
        // //Log::info($ressumDispute);

        // return DataTables::of($query)->addIndexColumn()->make(true);

        if ($data->isEmpty()) {
            // Jika data kosong, pastikan recordsTotal dan recordsFiltered diatur ke 0
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => 0,       // Tidak ada data
                'recordsFiltered' => 0,    // Tidak ada data yang difilter
                'data' => [],              // Array kosong
                'ressumMatch' => 0,        // Data lain diset ke 0 atau nilai default
                'resmatch' => 0,
                'resdispute' => 0,
                'ressumDispute' => 0,
            ]);
        }

        // Jika data ada
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $data->count(),  // Jumlah total data
            'recordsFiltered' => $data->count(),  // Jumlah data setelah filter
            'data' => DataTables::of($data)->addIndexColumn()->make(true)->original['data'],
            'ressumMatch' => $ressumMatch,
            'resmatch' => $resmatch,
            'resdispute' => $resdispute,
            'ressumDispute' => $ressumDispute,
        ]);
    }
    public function show($token_applicant)
    {
        $match = ReconcileResult::where('token_applicant', $token_applicant)->where('status', 'MATCH')->count();
        $dispute = ReconcileResult::where('token_applicant', $token_applicant)->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])->count();
        $onHold = ReconcileResult::where('token_applicant', $token_applicant)->where('status', 'NOT_FOUND')->count();

        $sumMatch = ReconcileResult::where('token_applicant', $token_applicant)->where('status', 'MATCH')->sum('total_sales');
        $sumDispute = ReconcileResult::where('token_applicant', $token_applicant)->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])->sum('total_sales');
        $sumHold = ReconcileResult::where('token_applicant', $token_applicant)->where('status', 'NOT_FOUND')->sum('total_sales');

        $banks = Bank::where('status', 'active')->get();

        return view('modules.reconcile.show', compact('banks', 'match', 'dispute', 'onHold', 'token_applicant', 'sumMatch', 'sumDispute', 'sumHold'));
    }

    public function data(Request $request)
    {
        $token_applicant = request()->query('token');
        $status = request()->query('status');

        $query = ReconcileResult::with('merchant', 'bank_account');
        if ($token_applicant) {
            $query->where('token_applicant', $token_applicant);
        }
        if ($status) {
            if ($status == "match") {
                $query->where('status', 'MATCH');
            } elseif ($status == "dispute") {
                $query->whereIn('status', ['NOT_MATCH', 'NOT_FOUND']);
            }
        }

        if ($request->input('status') !== null) {
            switch ($request->input('status')) {
                case 'match':
                    $status = ['MATCH'];
                    break;
                case 'dispute':
                    $status = ['NOT_MATCH', 'NOT_FOUND'];
                    break;
                case 'onHold':
                    $status = ['NOT_FOUND'];
                    break;
                default:
                    $status = ['NOT_FOUND'];
                    break;
            }
            $query->whereIn('status', $status);
        }

        if ($request->input('startDate') && $request->input('endDate')) {
            $startDate = $request->startDate;
            $endDate = $request->endDate;

            $query->whereDate('settlement_date', '>=', $startDate)
                ->whereDate('settlement_date', '<=', $endDate);
        }

        if ($request->input('channel') !== null) {
            $query->where('processor_payment', $request->channel);
        }

        $query->where('status', '!=', 'deleted');

        return DataTables::of($query->get())->addIndexColumn()->make(true);
    }
    public function oldapproveddata(Request $request)
    {
        $status = request()->query('status');
        $startDate = $request->input('startDate') ?? date('Y-m-d');
        $endDate = $request->input('endDate') ?? date('Y-m-d');

        $query = ReconcileReport::with('merchant', 'bank_account', 'channel')
            ->where('status_reconcile', '!=', 'deleted')
            ->where('status_reconcile', '!=', 'report')
            ->where('status_reconcile', '!=', 'draft')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($status) {
            if ($status == "match") {
                $query->where('status', 'MATCH');
            } elseif ($status == "dispute") {
                $query->whereIn('status', ['NOT_MATCH', 'NOT_FOUND']);
            }
        }

        if ($request->input('status') !== null) {
            switch ($request->input('status')) {
                case 'match':
                    $status = ['MATCH'];
                    break;
                case 'dispute':
                    $status = ['NOT_MATCH', 'NOT_FOUND'];
                    break;
                case 'onHold':
                    $status = ['NOT_FOUND'];
                    break;
                default:
                    $status = ['NOT_FOUND'];
                    break;
            }
            $query->whereIn('status', $status);
        }

        if ($request->input('channel') !== null) {
            $query->where('processor_payment', $request->channel);
        }

        $query->where('status', '!=', 'deleted');

        return DataTables::of($query->get())->addIndexColumn()->make(true);
    }

    public function approveddata(Request $request)
    {
        $status = $request->query('status');
        $startDate = $request->input('startDate') ?? date('Y-m-d');
        $endDate = $request->input('endDate') ?? date('Y-m-d');

        // Membuat query awal
        $query = ReconcileReport::with('merchant', 'bank_account', 'channel')
            ->where('status_reconcile', '!=', 'deleted')
            ->where('status_reconcile', '!=', 'report')
            ->where('status_reconcile', '!=', 'draft')
            ->whereDate('reconcile_date', '>=', $startDate)
            ->whereDate('reconcile_date', '<=', $endDate);

        // Filter berdasarkan status
        if ($status) {
            if ($status == "match") {
                $query->where('status', 'MATCH');
            } elseif ($status == "dispute") {
                $query->whereIn('status', ['NOT_MATCH', 'NOT_FOUND']);
            }
        }

        // Jika ada input status, filter sesuai input
        if ($request->input('status') !== null) {
            switch ($request->input('status')) {
                case 'match':
                    $status = ['MATCH'];
                    break;
                case 'dispute':
                    $status = ['NOT_MATCH', 'NOT_FOUND'];
                    break;
                case 'onHold':
                    $status = ['NOT_FOUND'];
                    break;
                default:
                    $status = ['NOT_FOUND'];
                    break;
            }
            $query->whereIn('status', $status);
        }

        // Filter berdasarkan channel jika ada
        if ($request->input('channel') !== null) {
            $query->where('processor_payment', $request->channel);
        }

        // Pastikan tidak menampilkan data yang statusnya 'deleted'
        $query->where('status', '!=', 'deleted');

        // Eksekusi query dan dapatkan datanya
        $data = $query->get();

        // //Log::info($query->first());

        // Misal kita ingin mendapatkan jumlah data yang match sebagai variabel tambahan
        $ressumMatch = $data->where('status', 'MATCH')->sum('total_sales');
        $resmatch = $data->where('status', 'MATCH')->count();
        $resdispute = $data->where('status', 'MATCH')->where('variance', '!=', '0')->count();
        $ressumDispute = $data->where('status', 'MATCH')->where('variance', '!=', '0')->sum('variance');



        // Kembalikan data dalam format DataTables dan tambahkan variabel tambahan
        if ($data->isEmpty()) {
            // Jika data kosong, pastikan recordsTotal dan recordsFiltered diatur ke 0
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => 0,       // Tidak ada data
                'recordsFiltered' => 0,    // Tidak ada data yang difilter
                'data' => [],              // Array kosong
                'ressumMatch' => 0,        // Data lain diset ke 0 atau nilai default
                'resmatch' => 0,
                'resdispute' => 0,
                'ressumDispute' => 0,
            ]);
        }

        // Jika data ada
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $data->count(),  // Jumlah total data
            'recordsFiltered' => $data->count(),  // Jumlah data setelah filter
            'data' => DataTables::of($data)->addIndexColumn()->make(true)->original['data'],
            'ressumMatch' => $ressumMatch,
            'resmatch' => $resmatch,
            'resdispute' => $resdispute,
            'ressumDispute' => $ressumDispute,
        ]);

        // return DataTables::of($query->get())->addIndexColumn()->make(true);
    }
    public function headerapproveddata(Request $request)
    {
        $status = $request->query('status');
        $startDate = $request->input('startDate') ?? date('Y-m-d');
        $endDate = $request->input('endDate') ?? date('Y-m-d');

        // Membuat query awal
        $query = ReconcileReport::with('merchant', 'bank_account', 'channel')
            ->where('status_reconcile', '!=', 'deleted')
            ->where('status_reconcile', '!=', 'report')
            ->where('status_reconcile', '!=', 'draft')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        // Filter berdasarkan status
        if ($status) {
            if ($status == "match") {
                $query->where('status', 'MATCH');
            } elseif ($status == "dispute") {
                $query->whereIn('status', ['NOT_MATCH', 'NOT_FOUND']);
            }
        }

        // Jika ada input status, filter sesuai input
        if ($request->input('status') !== null) {
            switch ($request->input('status')) {
                case 'match':
                    $status = ['MATCH'];
                    break;
                case 'dispute':
                    $status = ['NOT_MATCH', 'NOT_FOUND'];
                    break;
                case 'onHold':
                    $status = ['NOT_FOUND'];
                    break;
                default:
                    $status = ['NOT_FOUND'];
                    break;
            }
            $query->whereIn('status', $status);
        }

        // Filter berdasarkan channel jika ada
        if ($request->input('channel') !== null) {
            $query->where('processor_payment', $request->channel);
        }

        // Pastikan tidak menampilkan data yang statusnya 'deleted'
        $query->where('status', '!=', 'deleted');

        // Eksekusi query dan dapatkan datanya
        $data = $query->get();

        // //Log::info($query->first());

        // Misal kita ingin mendapatkan jumlah data yang match sebagai variabel tambahan
        $ressumMatch = $data->where('status', 'MATCH')->sum('total_sales');
        $resmatch = $data->where('status', 'MATCH')->count();
        $resdispute = $data->where('status', 'MATCH')->where('variance', '!=', '0')->count();
        $ressumDispute = $data->where('status', 'MATCH')->where('variance', '!=', '0')->sum('variance');



        // Kembalikan data dalam format DataTables dan tambahkan variabel tambahan
        return response()->json([
            'ressumMatch' => $ressumMatch, // Variabel tambahan
            'resmatch' => $resmatch, // Variabel tambahan
            'resdispute' => $resdispute, // Variabel tambahan
            'ressumDispute' => $ressumDispute, // Variabel tambahan
            // Anda bisa tambahkan variabel lain di sini untuk digunakan di halaman Anda
        ]);

    }


    public function reportdata($token, Request $request)
    {
        $token_applicant = request()->query('token');
        $status = request()->query('channel');

        $query = ReconcileReport::with('merchant', 'bank_account')->where('status_reconcile', '!=', 'draft')->where('token_applicant', $token);
        if ($token_applicant) {
            $query->where('token_applicant', $token_applicant);
        }
        // if ($status) {
        //     if ($status == "check") {
        //         $query->where('status_reconcile', 'pending');
        //     } elseif ($status == "approve") {
        //         $query->where('status_reconcile', 'approved');
        //     } elseif ($status == "pending") {
        //         $query->where('status_reconcile', 'report');
        //     }
        // }

        if ($request->input('startDate') && $request->input('endDate')) {
            $startDate = $request->startDate;
            $endDate = $request->endDate;

            $query->whereDate('settlement_date', '>=', $startDate)
                ->whereDate('settlement_date', '<=', $endDate);
        }

        // if ($request->input('channel') !== null) {
        //     $query->where('processor_payment', $request->channel);
        // }
        if ($request->input('channel') !== null) {
            if ($status == "check") {
                $query->where('status_reconcile', 'pending');
            } elseif ($status == "approve") {
                $query->where('status_reconcile', 'approved');
            } elseif ($status == "pending") {
                $query->where('status_reconcile', 'report');
            }
        }

        $query->where('status', '!=', 'deleted');

        return DataTables::of($query->get())->addIndexColumn()->make(true);
    }
    public function resultdata(Request $request)
    {
        $token_applicant = request()->query('token');
        $status = request()->query('status');

        $query = ReconcileResult::with('merchant', 'bank_account');
        if ($token_applicant) {
            $query->where('token_applicant', $token_applicant);
        }
        if ($status) {
            if ($status == "match") {
                $query->where('status', 'MATCH');
            } elseif ($status == "dispute") {
                $query->whereIn('status', ['NOT_MATCH', 'NOT_FOUND']);
            }
        }

        if ($request->input('status') !== null) {
            switch ($request->input('status')) {
                case 'match':
                    $status = ['MATCH'];
                    break;
                case 'dispute':
                    $status = ['NOT_MATCH', 'NOT_FOUND'];
                    break;
                case 'onHold':
                    $status = ['NOT_FOUND'];
                    break;
                default:
                    $status = ['NOT_FOUND'];
                    break;
            }
            $query->whereIn('status', $status);
        }

        if ($request->input('startDate') && $request->input('endDate')) {
            $startDate = $request->startDate;
            $endDate = $request->endDate;

            $query->whereDate('settlement_date', '>=', $startDate)
                ->whereDate('settlement_date', '<=', $endDate);
        }

        if ($request->input('channel') !== null) {
            $query->where('processor_payment', $request->channel);
        }

        $query->where('status', '!=', 'deleted');

        return DataTables::of($query->get())->addIndexColumn()->make(true);
    }

    public function olddownload()
    {
        $token_applicant = request()->query('token');
        $status = request()->query('status');
        // $channel = request()->query('bank');
        $channel = "5";

        $startDate = request()->query('startDate');
        $endDate = request()->query('endDate');

        if (!$status) {
            $text = 'all';
        } else {
            $text = $status;
        }

        $filename = $channel . '-' . $startDate . '-to-' . $endDate . '-' . $text;

        return Excel::download(new ReconcileExport($token_applicant, $status, $startDate, $endDate, $channel), 'reconcile-' . $filename . '.xlsx');
    }
    public function download()
    {
        $token_applicant = request()->query('token');
        $status = request()->query('status');
        // $channel = request()->query('bank');
        // $channel = "5";

        $startDate = request()->query('startDate');
        $endDate = request()->query('endDate');

        if (!$status) {
            $text = 'all';
        } else {
            $text = $status;
        }

        // $filename = $channel . '-' . $startDate . '-to-' . $endDate . '-' . $text;
        $filename = Carbon::now() . '-' . $text;

        // return Excel::download(new ReconcileExport($token_applicant, $status, $startDate, $endDate, $channel), 'reconcile-' . $filename . '.xlsx');
        return Excel::download(new ReconcileExport($token_applicant), 'reconcile result-' . $filename . '.xlsx');
    }
    public function downloadunmatch()
    {
        // $token_applicant = request()->query('token');
        // $status = request()->query('status');
        // $channel = request()->query('bank');
        // $channel = "5";

        $startDate = request()->query('startDate');
        $endDate = request()->query('endDate');



        // if (!$status) {
        //     $text = 'all';
        // } else {
        //     $text = $status;
        // }

        // $filename = $channel . '-' . $startDate . '-to-' . $endDate . '-' . $text;
        // $filename = Carbon::now() . '-' . $text;

        // return Excel::download(new ReconcileExport($token_applicant, $status, $startDate, $endDate, $channel), 'reconcile-' . $filename . '.xlsx');
        return Excel::download(new ReconcileUnmatchExport($startDate, $endDate), 'reconcile unmach-' . Carbon::now() . '.xlsx');
    }
    public function downloaddisburst(Request $request)
    {
        // //Log::info($date);
        $token_applicant = request()->query('token');
        // $status = request()->query('status');
        // $channel = request()->query('bank');
        // $channel = "5";

        $startDate = request()->query('startDate');
        $endDate = request()->query('endDate');
        // //Log::info($startDate);
        // //Log::info($endDate);

        $data = ReconcileReport::with('merchant', 'bank_account')
            ->where(DB::raw('DATE(created_at)'), '>=', $startDate)
            ->where(DB::raw('DATE(created_at)'), '<=', $endDate)
            ->get();

        // if (!$status) {
        //     $text = 'all';
        // } else {
        //     $text = $status;
        // }

        // $filename = $channel . '-' . $startDate . '-to-' . $endDate . '-' . $text;
        // $filename = Carbon::now() . '-' . $text;

        if ($data->isEmpty()) {
            return response()->json(['message' => ['Data Not Found'], 'status' => false], 200);
        }
        // return Excel::download(new ReconcileExport($token_applicant, $status, $startDate, $endDate, $channel), 'reconcile-' . $filename . '.xlsx');
        return Excel::download(new ReconcileDisburstExport($startDate, $endDate), 'Disburstment List-' . Carbon::now() . '.xlsx');
    }

    public function reportmrcDetail($token_applicant)
    {
        $data = ReconcileReport::with('merchant', 'bank_account')->where('token_applicant', $token_applicant)->first();

        return response()->json(['data' => $data, 'message' => 'Successfully get data!', 'status' => true], 200);
    }
    public function mrcDetail($token_applicant)
    {
        $data = ReconcileResult::with('merchant', 'bank_account')->where('token_applicant', $token_applicant)->first();

        return response()->json(['data' => $data, 'message' => 'Successfully get data!', 'status' => true], 200);
    }
    public function draftDetail($id)
    {
        $data = ReconcileDraft::where('id', $id)->first();

        return response()->json(['data' => $data, 'message' => 'Successfully get data!', 'status' => true], 200);
    }

    public function proceed($token_applicant)
    {
        $data = UploadBank::where('token_applicant', $token_applicant)->first();

        if ($data) {
            DB::beginTransaction();
            try {
                $boData = InternalBatch::selectRaw('
                            SUM(transaction_count) as transaction_count,
                            SUM(bank_transfer) as bank_transfer,
                            SUM(fee_mdr_merchant) as fee_mdr_merchant,
                            SUM(fee_bank_merchant) as fee_bank_merchant,
                            SUM(tax_payment) as tax_payment,
                            SUM(transaction_amount) as transaction_amount,
                            merchant_id,
                            mid
                        ')
                    ->where(DB::raw('DATE(created_at)'), '>=', date('Y-m-d', strtotime($data->start_date)))
                    ->where(DB::raw('DATE(created_at)'), '<=', date('Y-m-d', strtotime($data->end_date)))
                    ->where('processor', $data->processor)
                    ->groupBy('mid', 'merchant_id')
                    ->get();
                dd($boData[2]->mid);

                foreach ($boData as $key => $value) {
                    dd($value->mid);
                    $details = UploadBankDetail::selectRaw('
                                    SUM(amount_credit) as amount_credit,
                                    mid
                                ')
                        ->where('token_applicant', $token_applicant)
                        ->where('mid', 'like', '%' . $value->mid . '%')
                        ->where('type_code', '001')
                        ->where('is_reconcile', false)
                        ->groupBy('mid')->first();
                    dd($details);
                }
                $details = UploadBankDetail::where('token_applicant', $token_applicant)->where('type_code', '001')->where('is_reconcile', false)->get();

                foreach ($details as $key => $value) {
                    $carbonDate = Carbon::createFromFormat('d/m/Y', $value->transfer_date);
                    $formattedDate = $carbonDate->format('Y-m-d');
                    $internalBatch = InternalBatch::selectRaw('
                                    SUM(transaction_count) as transaction_count,
                                    SUM(bank_transfer) as bank_transfer,
                                    SUM(fee_mdr_merchant) as fee_mdr_merchant,
                                    SUM(fee_bank_merchant) as fee_bank_merchant,
                                    SUM(tax_payment) as tax_payment,
                                    SUM(transaction_amount) as transaction_amount,
                                    merchant_id,
                                    mid
                                ')
                        ->where('mid', 'like', '%' . $value->mid . '%')
                        ->where(DB::raw('DATE(created_at)'), '=', $formattedDate)
                        ->groupBy('mid', 'merchant_id')
                        ->first();

                    $bankSettlement = $value->amount_credit;
                    $rounded_value = round((int) $bankSettlement);
                    $amount_credit = number_format($rounded_value, 0, '', '');
                    if ($internalBatch) {
                        $trxCount = $internalBatch->transaction_count;
                        $boSettlement = $internalBatch->bank_transfer;
                        $feeMdrMerchant = $internalBatch->fee_mdr_merchant;
                        $feeBankMerchant = $internalBatch->fee_bank_merchant;
                        $taxPayment = $internalBatch->tax_payment;
                        $totalSales = $internalBatch->bank_transfer + $internalBatch->fee_bank_merchant;
                        $merchant_id = $internalBatch->merchant_id;
                        $sumTransaction = $internalBatch->transaction_amount;

                        $merchantPayment = Utils::calculateMerchantPayment($boSettlement, $feeMdrMerchant, $feeBankMerchant, $taxPayment); // tanya mas tri

                        $diff = abs((float) $boSettlement - (float) $bankSettlement);
                        $treshold = Utils::calculateTreshold($trxCount);
                        $status = Utils::getStatusReconcile($treshold, $boSettlement, $bankSettlement);
                    } else {
                        $status = 'NOT_FOUND';
                        $trxCount = 0;
                        $totalSales = 0;
                        $boSettlement = 0;
                        $merchantPayment = 0;
                        $sumTransaction = 0;
                        $diff = 0 - (float) $bankSettlement;
                    }

                    $reconcile = ReconcileResult::create([
                        'token_applicant' => $token_applicant,
                        'statement_id' => $value->id,
                        'request_id' => $data->id,
                        'status' => $status,
                        // 'tid' => $tid,
                        'mid' => $value->mid,
                        // 'batch_fk' => $batch_fk,
                        'trx_counts' => $trxCount, // total transaksi 1 batch
                        'total_sales' => $totalSales, // sum transaction_amout di internal_taransaction 
                        'processor_payment' => $data->processor,
                        'internal_payment' => $boSettlement, // bank_payment
                        'merchant_payment' => $merchantPayment, // bank_payment - merchant_fee_amount
                        'merchant_id' => $merchant_id,
                        'transfer_amount' => $sumTransaction, // transaction_amount di internal_batch
                        'bank_settlement_amount' => $amount_credit, // bank_settlement
                        'dispute_amount' => $diff, // dispute_amount
                        // 'tax_payment',
                        // 'fee_mdr_merchant',
                        // 'fee_bank_merchant',
                        // 'bank_transfer',
                        'created_by' => 'System',
                        'modified_by' => null,
                        'settlement_date' => $carbonDate
                    ]);

                    $det = UploadBankDetail::where('id', $value->id)->first();

                    if ($status == 'MATCH') {
                        $det->is_reconcile = true;
                    } else {
                        $det->is_reconcile = false;
                    }
                    $det->save();

                    $data->is_reconcile = true;
                    $data->save();
                }

                DB::commit();
                return response()->json(['message' => 'Successfully reconcile data!', 'status' => true], 200);
            } catch (\Throwable $th) {
                dd($th);
                DB::rollBack();
                return response()->json(['message' => 'Error while reconcile, try again', 'status' => false], 200);
            }
        }
    }

    public function detail($token_applicant)
    {
        $token = $token_applicant;
        $channels = UploadBankDetail::select('description2')->where('token_applicant', $token_applicant)->where('description2', '!=', '')->groupBy('description2')->get();

        // $sumCredit = UploadBankDetail::where('token_applicant', $token_applicant)->sum('amount_credit');
        $sumCredit = UploadBankDetail::selectRaw("SUM(amount_credit) as amount_credit")->where('token_applicant', $token_applicant)->first();

        $totalCredit = UploadBankDetail::where('token_applicant', $token_applicant)->where('amount_credit', '>', 0)->count();

        $sumDebit = UploadBankDetail::where('token_applicant', $token_applicant)->sum('amount_debit');
        $totalDebit = UploadBankDetail::where('token_applicant', $token_applicant)->where('amount_debit', '>', 0)->count();
        return view('modules.reconcile.detail.index', compact('channels', 'sumCredit', 'totalCredit', 'sumDebit', 'totalDebit', 'token'));
    }

    public function detailData(Request $request, $token_applicant)
    {
        $query = UploadBankDetail::where('token_applicant', $token_applicant);
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = date('d/m/Y', strtotime($request->startDate));
            $endDate = date('d/m/Y', strtotime($request->endDate));

            $query->where('transfer_date', '>=', $startDate);
            $query->where('transfer_date', '<=', $endDate);
        }
        if ($request->filled('channel')) {
            $query->where('description2', $request->channel);
        }

        return DataTables::of($query->get())->addIndexColumn()->make(true);
    }


    public function detailstatus($token_applicant, $data)
    {
        $status = $data;
        $token = $token_applicant;
        $channels = UploadBankDetail::select('description2')->where('token_applicant', $token_applicant)->where('description2', '!=', '')->groupBy('description2')->get();

        $sumCredit = UploadBankDetail::where('token_applicant', $token_applicant)->sum('amount_credit');
        $totalCredit = UploadBankDetail::where('token_applicant', $token_applicant)->where('amount_credit', '>', 0)->count();

        $sumDebit = UploadBankDetail::where('token_applicant', $token_applicant)->sum('amount_debit');
        $totalDebit = UploadBankDetail::where('token_applicant', $token_applicant)->where('amount_debit', '>', 0)->count();
        return view('modules.reconcile.detail.status', compact('token', 'status', 'channels', 'sumCredit', 'totalCredit', 'sumDebit', 'totalDebit'));
    }
    public function detailstatusdata(Request $request, $token_applicant, $data)
    {

        if ($data == "credit") {
            $query = UploadBankDetail::where('token_applicant', $token_applicant)->where('amount_credit', '>', 0);
        } else {
            $query = UploadBankDetail::where('token_applicant', $token_applicant)->where('amount_debit', '>', 0);
        }
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = date('d/m/Y', strtotime($request->startDate));
            $endDate = date('d/m/Y', strtotime($request->endDate));

            $query->where('transfer_date', '>=', $startDate);
            $query->where('transfer_date', '<=', $endDate);
        }
        if ($request->filled('channel')) {
            $query->where('description2', $request->channel);
        }

        return DataTables::of($query->get())->addIndexColumn()->make(true);
    }

    public function channel(Request $request)
    {
        $token_applicant = $request->token_applicant;

        // if ($request->filled('bo_date') && $request->filled('bs_date')) {
        if ($request->filled('bo_date')) {
            $splitDate = explode(' - ', $request->bo_date);
            $BoStartDate = date('Y-m-d', strtotime($splitDate[0]));
            $BoEndDate = date('Y-m-d', strtotime($splitDate[0]));

            // $BsSplitDate = explode(' - ', $request->bo_date);
            // $BsStartDate = date('d/m/Y', strtotime($BsSplitDate[0]));
            // $BsEndDate = date('d/m/Y', strtotime($BsSplitDate[1]));
        }

        DB::beginTransaction();
        try {
            $uploadBank = UploadBank::where('token_applicant', $token_applicant)->first();
            $getName = UploadBank::with('channel')->where('token_applicant', $token_applicant)->first();
            $nameBank = $getName->channel->channel;
            $boData = InternalBatch::selectRaw('
                        SUM(transaction_count) as transaction_count,
                        SUM(bank_transfer) as bank_transfer,
                        SUM(fee_mdr_merchant) as fee_mdr_merchant,
                        SUM(fee_bank_merchant) as fee_bank_merchant,
                        SUM(tax_payment) as tax_payment,
                        SUM(transaction_amount) as transaction_amount,
                        SUM(total_sales_amount) as total_sales_amount,
                        merchant_id,
                        mid,
                        DATE(created_at) as created_date
                    ')
                ->where(DB::raw('DATE(created_at)'), '>=', $BoStartDate)
                ->where(DB::raw('DATE(created_at)'), '<=', $BoEndDate)
                ->where('processor', 'MANDIRI')
                ->where('bank_id', 5)
                ->where('status', 'SUCCESSFUL')
                ->groupBy('mid', 'merchant_id', 'created_date')
                ->get();
            foreach ($boData as $key => $value) {
                $modMid = substr($value->mid, 5);
                $bsData = UploadBankDetail::selectRaw('
                    SUM(amount_credit) as amount_credit,
                    mid, token_applicant
                    ')
                    ->with('header')
                    // ->whereHas('header', function ($query) use ($uploadBank) {
                    //     $query->where('processor', $uploadBank->processor);
                    // })
                    ->where('mid', 'like', '%' . $modMid . '%')
                    ->where('token_applicant', $token_applicant)
                    ->where('description2', $request->channel)
                    ->where('type_code', '001')
                    ->where('is_reconcile', false)
                    ->groupBy('mid', 'token_applicant')
                    ->first();

                if ($bsData) {
                    $bankSettlement = $bsData->amount_credit;
                    $token_applicant = $bsData->header->token_applicant;

                    $trxCount = $value->transaction_count;
                    $boSettlement = Utils::customRound($value->bank_transfer);

                    $feeMdrMerchant = $value->fee_mdr_merchant;
                    $feeBankMerchant = $value->fee_bank_merchant;
                    $taxPayment = $value->tax_payment;
                    $totalSales = $value->total_sales_amount;

                    $merchant_id = $value->merchant_id;
                    $sumTransaction = $value->transaction_amount;

                    $merchantPayment = Utils::calculateMerchantPayment(
                        $boSettlement,
                        $feeMdrMerchant,
                        $feeBankMerchant,
                        $taxPayment
                    ); // tanya mas tri

                    $rounded_value = round((int) $bankSettlement);
                    $amount_credit = number_format($rounded_value, 0, '', '');

                    $diff = abs((float) $boSettlement - (float) $bankSettlement);
                    $treshold = Utils::calculateTreshold($trxCount);
                    $status = Utils::getStatusReconcile($treshold, $boSettlement, $bankSettlement);

                    $reconcile = ReconcileResult::create([
                        'token_applicant' => $token_applicant,
                        'statement_id' => $bsData ? $bsData->id : null,
                        'request_id' => $bsData ? $bsData->header->id : null,
                        'status' => $status,
                        'mid' => $value->mid,
                        'trx_counts' => $trxCount, // total transaksi 1 batch
                        'total_sales' => $totalSales, // sum transaction_amout di internal_taransaction 
                        'processor_payment' => $nameBank,
                        'internal_payment' => $boSettlement, // bank_payment
                        'merchant_payment' => $merchantPayment, // bank_payment - merchant_fee_amount
                        'merchant_id' => $merchant_id,
                        'transfer_amount' => $sumTransaction, // transaction_amount di internal_batch
                        'bank_settlement_amount' => $amount_credit, // bank_settlement
                        'dispute_amount' => $diff, // dispute_amount
                        'created_by' => 'System',
                        'modified_by' => null,
                        'settlement_date' => $value->created_date
                    ]);
                    if ($token_applicant) {
                        $uploadBank = UploadBank::where('token_applicant', $token_applicant)->update([
                            'is_reconcile' => true
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => 'Successfully reconcile data!', 'status' => true], 200);
        } catch (\Throwable $th) {
            //Log::info($th);
            DB::rollBack();
            return response()->json(['message' => 'Error while reconcile, try again', 'status' => false], 200);
        }
    }

    public function partner()
    {
        $banks = Channel::with('parameter')
            ->where('status', 'active')
            ->whereHas('parameter')
            ->get();

        return view('modules.reconcile.partner.index', compact('banks'));
    }
    public function manualrecon()
    {
        $banks = Channel::with('parameter')
            ->where('status', 'active')
            ->whereHas('parameter')
            ->get();

        return view('modules.reconcile.manual.index', compact('banks'));
    }

    public function unmatchdata(Request $request)
    {
        $query = ReconcileUnmatch::where('status', 'NOT_MATCH')
            ->where('status_reconcile', false)
            ->where('bank_transfer', '>', 0);
        if ($request->filled('bank')) {
            $query->where('processor_payment', $request->bank);
        }
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = $request->startDate;
            $endDate = $request->endDate;

            // $query->where('settlement_date', '>=', $startDate);
            // $query->where('settlement_date', '<=', $endDate);
            $query->where(DB::raw('DATE(settlement_date)'), '>=', $startDate);
            $query->where(DB::raw('DATE(settlement_date)'), '<=', $endDate);
        }
        $query->orderByDesc('id');
        return DataTables::of($query)->addIndexColumn()->make(true);
    }

    public function changes()
    {
        DB::statement(`UPDATE internal_batches AS b
JOIN (
    SELECT batch_id, MIN(settlement_date) AS settlement_date
    FROM internal_transactions
    GROUP BY batch_id
) AS t ON b.id = t.batch_id
SET b.settlement_date = t.settlement_date;`);
    }

    public function updatebs(Request $request)
    {
        try {
            $id = $request->query('id');
            $mid = $request->query('mid');
            $updatebs = $request->query('updatebs');
            $newbs = $request->query('newbs');
            $mid = $request->query('mid');
            $merchantbs = $request->query('merchantbs');

            $data = ReconcileUnmatch::where('id', $id)->orderBy('id', 'ASC')->first();
            $list = ReconcileList::where('token_applicant', $data->token_applicant)->first();
            $draft = ReconcileDraft::where('id', $data->draft_id)->first();
            $settleDate = ReconcileUnmatch::where('id', $id)->pluck('settlement_date')->first();
            $tfDate = UploadBankDetail::where('id', $data->statement_id)->pluck('transfer_date')->first();
            // //Log::info($tfDate);

            if ($data) {
                $uf = UploadBankDetail::where('id', $data->statement_id)->first();
                // if ($uf) {

                $newUB = UploadBankDetail::create([
                    'token_applicant' => $uf->token_applicant,
                    'account_no' => $uf->account_no,
                    'transfer_date' => $uf->transfer_date,
                    'description2' => $uf->description2,
                    'description1' => $uf->description1,
                    'type_code' => $uf->type_code,
                    'amount_debit' => $uf->amount_debit,
                    'amount_credit' => $updatebs,
                    'mid' => $uf->mid,
                    'created_by' => $uf->created_by,
                    'modified_by' => $uf->modified_by,
                    'bank_id' => $uf->bank_id
                ]);

                // $newRU = ReconcileUnmatch::create([

                DB::table('reconcile_unmatches')->insert([
                    // 'settlement_date' => $newUB->transfer_date,
                    'settlement_date' => $tfDate,
                    'draft_id' => $data->draft_id,
                    'statement_id' => $newUB->id,
                    'name' => $data->name,
                    'merchant_name' => $data->merchant_name,
                    'token_applicant' => $data->token_applicant,
                    'status' => $data->status,
                    'mid' => $data->mid,
                    'trx_counts' => $data->trx_counts,
                    'bank_transfer' => $data->bank_transfer,
                    'tax_payment' => $data->tax_payment,
                    "fee_mdr_merchant" => $data->fee_mdr_merchant,
                    "fee_bank_merchant" => $data->fee_bank_merchant,
                    'total_sales' => $data->total_sales,
                    'processor_payment' => $data->processor_payment,
                    'internal_payment' => $updatebs,
                    'merchant_payment' => $data->merchant_payment,
                    'merchant_id' => $data->merchant_id,
                    'transfer_amount' => $data->transfer_amount,
                    'bank_settlement_amount' => $updatebs,
                    // 'dispute_amount' => $data->dispute_amount,
                    'created_by' => $data->created_by,
                    'variance' => $data->variance,
                    'modified_by' => $data->modified_by,
                    'status_parnert' => $data->status_parnert,
                    'status_reconcile' => $data->status_reconcile,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at,
                ]);

                // $newdraft = ReconcileDraft::create([
                DB::table('reconcile_drafts')->insert([
                    'name' => $draft->name,
                    'merchant_name' => $draft->merchant_name,
                    'token_applicant' => $draft->token_applicant,
                    'status' => $draft->status,
                    'mid' => $draft->mid,
                    'trx_counts' => $draft->trx_counts,
                    'bank_transfer' => $draft->bank_transfer,
                    'tax_payment' => $draft->tax_payment,
                    "fee_mdr_merchant" => $draft->fee_mdr_merchant,
                    "fee_bank_merchant" => $draft->fee_bank_merchant,
                    'total_sales' => $draft->total_sales,
                    'processor_payment' => $draft->processor_payment,
                    'internal_payment' => $updatebs,
                    'merchant_payment' => $draft->merchant_payment,
                    'merchant_id' => $draft->merchant_id,
                    'transfer_amount' => $draft->transfer_amount,
                    'bank_settlement_amount' => $updatebs,
                    'created_by' => $draft->created_by,
                    'variance' => $draft->variance,
                    'modified_by' => $draft->modified_by,
                    'status_parnert' => $draft->status_parnert,
                    'status_reconcile' => $draft->status_reconcile,
                    'status_manual' => $draft->status_manual,
                    'reconcile_date' => $draft->reconcile_date,
                    'settlement_date' => $tfDate,
                    'statement_id' => $newUB->id,
                    'bo_id' => $draft->bo_id,
                    'bank_id' => $draft->bank_id,
                    'bo_date' => $draft->bo_date,
                ]);

                UploadBankDetail::where('id', $data->statement_id)->update([
                    'amount_credit' => $newbs
                ]);

                ReconcileUnmatch::where('id', $id)->update([
                    "modified_by" => Auth::user()->name,
                    "internal_payment" => $newbs,
                    "bank_settlement_amount" => $newbs,
                ]);

                ReconcileDraft::where('id', $data->draft_id)->update([
                    "internal_payment" => $newbs,
                ]);

                // $uf->amount_credit = $newbs;
                // $uf->save();

                // $draft->internal_payment = $newbs;
                // $draft->save();

                // $data->internal_payment = $newbs;
                // $data->bank_settlement_amount = $newbs;
                // $data->save();



                // ReconcileReport::where('id', $id)->delete();
                return response()->json(['message' => ['Success Add Data!'], 'status' => true], 200);
                // } else {
                //     return response()->json(['message' => ['Error while Add Data, try again'], 'status' => false], 200);
                // }
            } else {
                return response()->json(['message' => ['Error while Add Data, try again'], 'status' => false], 200);
            }
        } catch (\Throwable $th) {
            //Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }

}
