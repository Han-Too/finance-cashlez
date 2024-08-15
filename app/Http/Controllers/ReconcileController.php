<?php

namespace App\Http\Controllers;

use App\Exports\ReconcileExport;
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
        $file = UploadBank::where('is_reconcile', '0')->get();
        return view('modules.reconcile.list.list', compact('list', 'file'));
    }
    public function reconcilelistdata(Request $request)
    {
        $query = ReconcileList::get();


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
                    'dispute_amount' => $data->dispute_amount,
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
            Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }

    public function draftmove($token)
    {
        try {
            $data = ReconcileDraft::where('token_applicant', $token)->get();
            foreach ($data as $val) {
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
                        'dispute_amount' => $val->dispute_amount,
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
                    ReconcileUnmatch::create([
                        'draft_id' => $val->id,
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
                        'internal_payment' => $val->internal_payment,
                        'merchant_payment' => $val->merchant_payment,
                        'merchant_id' => $val->merchant_id,
                        'transfer_amount' => $val->transfer_amount,
                        'bank_settlement_amount' => $val->bank_settlement_amount,
                        'dispute_amount' => $val->dispute_amount,
                        'created_by' => $val->created_by,
                        'variance' => $val->variance,
                        'modified_by' => $val->modified_by,
                        'status_parnert' => $val->status_parnert,
                        'status_reconcile' => false,
                        'settlement_date' => $val->settlement_date,
                    ]);
                    $val->status_manual = true;
                    $val->status_reconcile = "manual";

                    DraftBackOffice::where('id', $val->bo_id)->update([
                        'status_reconcile' => NULL,
                        'reconcile_date' => NULL,
                    ]);
                }
                $val->status = "deleted";
                $val->save();
            }
            return response()->json(['message' => ['Successfully moving data!'], 'status' => true], 200);
        } catch (\Throwable $th) {
            Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }
    public function manualReport($id)
    {
        try {
            $data = ReconcileReport::where('id', $id)->first();
            if ($data) {
                $idbo = explode("//", $data->bo_id);
                $idbank = explode("//", $data->draft_id);

                $list = ReconcileList::where('token_applicant', $data->token_applicant)->first();


                // foreach ($idbank as $val) {
                //     $manual = ReconcileDraft::where('token_applicant', $data->token_applicant)
                //         ->where('id', $val)->update([
                //                 'status' => "deleted",
                //                 'status_reconcile' => "manual",
                //             ]);
                // }

                foreach ($idbank as $val) {
                    ReconcileDraft::where('token_applicant', $data->token_applicant)
                        ->where('id', $val)->update([
                                'status' => "deleted",
                                'status_reconcile' => "manual",
                            ]);
                    ReconcileUnmatch::where('token_applicant', $list->token_applicant)
                        ->where('id', $val)
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
            Log::info($th);
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
            Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }
    public function approveReport($id)
    {
        $data = ReconcileReport::where('id', $id)->first();
        try {
            if ($data) {
                ReconcileDraft::where('id', $data->draft_id)->update([
                    'status_reconcile' => 'reconciled',
                    'status' => $data->status,
                    'reconcile_date' => Carbon::now(),
                    'modified_by' => Auth::user()->username,
                ]);
                $data->status_reconcile = "approved";
                $data->save();
                return response()->json(['message' => ['Success Draft Data!'], 'status' => true], 200);
            }
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        } catch (\Throwable $th) {
            Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }
    public function approveAll()
    {
        $data = ReconcileReport::get();
        try {
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
                    // Pisahkan draft_id berdasarkan delimiter '//'
                    $draftIds = explode("//", $val->draft_id);

                    foreach ($draftIds as $draftId) {
                        ReconcileDraft::where('id', $draftId)->update([
                            'status_reconcile' => 'reconciled',
                            'status' => $val->status,
                            'reconcile_date' => Carbon::now(),
                            'modified_by' => Auth::user()->username,
                        ]);
                    }

                    // Pisahkan bo_id berdasarkan delimiter '//'
                    $boIds = explode("//", $val->bo_id);

                    foreach ($boIds as $boId) {
                        DraftBackOffice::where('id', $boId)->update([
                            'status_reconcile' => 'reconciled',
                            'reconcile_date' => Carbon::now(),
                        ]);
                    }

                    // Update status_reconcile pada objek $val
                    $val->status_reconcile = "approved";
                    $val->save();
                }
                return response()->json(['message' => ['Success Approve Data!'], 'status' => true], 200);
            }
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        } catch (\Throwable $th) {
            Log::info($th);
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
                        'dispute_amount' => $data->dispute_amount,
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
                $data->status = "deleted";
                $data->status_manual = false;
                $data->status_reconcile = "report";
                $data->save();
                return response()->json(['message' => ['Successfully reconcile data!'], 'status' => true], 200);
            } else {
                return response()->json(['message' => ['Error while store reconcile'], 'status' => false], 200);
            }

        } catch (\Throwable $th) {
            Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }
    public function unmatchstore($id)
    {
        $data = ReconcileDraft::where('id', $id)->first();
        try {
            if ($data) {
                $reconcile = ReconcileUnmatch::create([
                    'draft_id' => $data->id,
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
                    'internal_payment' => $data->internal_payment,
                    'merchant_payment' => $data->merchant_payment,
                    'merchant_id' => $data->merchant_id,
                    'transfer_amount' => $data->transfer_amount,
                    'bank_settlement_amount' => $data->bank_settlement_amount,
                    'dispute_amount' => $data->dispute_amount,
                    'created_by' => $data->created_by,
                    'variance' => $data->variance,
                    'modified_by' => $data->modified_by,
                    'status_parnert' => $data->status_parnert,
                    'status_reconcile' => false,
                    'settlement_date' => $data->settlement_date,
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
            Log::info($th);
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
        $name = $request->name;
        $filehead = UploadBank::where('name', $fileset)->first();
        $user = Auth::user();
        // $detail = UploadBankDetail::where('token_applicant',$filehead->token_applicant)->get();
        // dd($filehead);

        $reconResult = false;
        try {

            $list = ReconcileList::create([
                'name' => $name,
                'token_applicant' => Str::uuid(),
                'type' => "mid",
                'settlement_file' => $filehead->url,
                'bo_date' => $request->bo_date,
                'status' => "draft",
                "is_parnert" => false,
                "reconcile_date" => Carbon::now(),
                "reconcile_by" => $user->name,
            ]);

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
                ->where(DB::raw('DATE(created_at)'), '>=', $BoStartDate)
                ->where(DB::raw('DATE(created_at)'), '<=', $BoEndDate)
                // ->where('bank_id', $channel)
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
                Log::info('Data BO EMPTY = '.$boData->isEmpty());
                ReconcileList::where('token_applicant', $list->token_applicant)->delete();
                return response()->json(
                    ['message' => ['Data Back Office '.$start.' until '.$end.' Not Found '],
                    'status' => false], 200);
            } else {

                $reconResult = Reconcile::midBoBankDraft($BoStartDate, $BoEndDate, $filehead->token_applicant, $name, $list->token_applicant);
                // dd($reconResult);

                if ($reconResult == false) {
                    return response()->json(['message' => ['Error while reconciling, please try again'], 'status' => false], 200);
                } else {
                    return response()->json(['message' => ['Successfully reconcile data!'], 'status' => true], 200);
                }
            }
        } catch (\Throwable $th) {
            Log::info($th);
            dd($th);
            return response()->json(['message' => ['Error while reconcile, try again'], 'status' => false], 200);
        }
    }

    public function reconcilelistdestroy(Request $request, $token)
    {
        try {

            $up = ReconcileList::where('token_applicant', $token)->first();

            $ub = UploadBank::where('url', $up->settlement_file)->select('token_applicant')->first();

            $getUp = UploadBank::where('url', $up->settlement_file)->update([
                'is_reconcile' => 0,
            ]);

            $getUD = UploadBankDetail::where('token_applicant', $ub->token_applicant)->update([
                'is_reconcile' => false,
            ]);

            if ($up) {
                // Menghapus data dari tabel UploadBank
                $uploadBank = ReconcileList::where('token_applicant', $token)->delete();
                $uploadReport = ReconcileReport::where('token_applicant', $token)->delete();
                $draftBO = DraftBackOffice::where('draft_token', $token)->delete();

                // Menghapus data dari tabel UploadBankDetail
                $uploadDetail = ReconcileDraft::where('token_applicant', $token)->delete();
                ReconcileUnmatch::where('token_applicant', $token)->delete();
                ReconcileReport::where('token_applicant', $token)->delete();

                // Mengembalikan respons sukses jika kedua operasi penghapusan berhasil
                if ($uploadBank && $uploadDetail && $draftBO && $uploadReport) {
                    UploadBankDetail::where('token_applicant', $getUp->token_applicant)->update([
                        'is_reconcile' => false
                    ]);

                    return response()->json(['success' => true, 'message' => 'Berhasil Hapus Data'], 200);
                }
            } else {
                // Mengembalikan respons kesalahan jika salah satu operasi penghapusan gagal
                return response()->json(['message' => 'Error while deleting data', 'status' => false], 200);
            }

        } catch (\Throwable $th) {
            // Mencatat kesalahan jika terjadi kesalahan
            Log::info($th);

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
            Log::info($th);
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
            ->where('dispute_amount', '>', 0)
            ->pluck('dispute_amount')->sum();
        $dispcount = ReconcileReport::where('token_applicant', $token)
            ->where('status_reconcile', 'approved')
            ->where('dispute_amount', '>', 0)
            ->pluck('dispute_amount')->count();

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
        $resdispute = $query2->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])->count();
        $resonHold = $query3->where('status', 'ON_HOLD')->count();

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

        $countdata = ReconcileDraft::where('token_applicant', $token)->where('status_manual', '0')
            ->where('status', '!=', 'deleted')->count();

        $countapprov = ReconcileDraft::where('token_applicant', $token)
            ->where('status', '!=', 'deleted')->where('status_reconcile', '!=', 'reconciled')->count();
        $reportapprov = ReconcileReport::where('token_applicant', $token)
            ->where('status', 'MATCH')->where('status_reconcile', 'approved')->count();
        $reportcount = ReconcileReport::where('token_applicant', $token)
            ->where('status', 'MATCH')->where('status_reconcile', '!=', 'approved')->count();

        $match = ReconcileDraft::where('token_applicant', $token)->where('status', 'MATCH')->count();

        $disp = ReconcileReport::where('token_applicant', $token)
            ->where('dispute_amount', '>', 0)
            ->pluck('dispute_amount')->sum();
        $dispcount = ReconcileReport::where('token_applicant', $token)
            ->where('dispute_amount', '>', 0)
            ->pluck('dispute_amount')->count();

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

        $unmatch = ReconcileDraft::where('token_applicant', $token)->where('status', 'NOT_MATCH')->count();
        $onhold = ReconcileDraft::where('token_applicant', $token)->where('status', 'ONHOLD')->count();
        $draft = ReconcileDraft::where('token_applicant', $token)->where('status_reconcile', 'draft')->count();
        $approv = ReconcileDraft::where('token_applicant', $token)->where('status_reconcile', 'approved')->count();

        return view(
            'modules.reconcile.list.detail',
            compact(
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
    public function datareconcilelistdetail(Request $request)
    {
        // Log::info('Custom filter:'.$request->status);
        $token_applicant = request()->query('token');
        $status = request()->query('status');

        $query = ReconcileDraft::where('status', '!=', 'deleted');

        if ($token_applicant) {
            $query->where('token_applicant', $token_applicant);
        }

        if ($request->input('startDate') && $request->input('endDate')) {
            $startDate = $request->startDate;
            $endDate = $request->endDate;

            $query->whereDate('settlement_date', '>=', $startDate)
                ->whereDate('settlement_date', '<=', $endDate);
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
                    'dispute_amount' => $diff, // dispute_amount
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
    public function reconcileold(Request $request)
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
        $merchant_id = null;
        $bank_id = null;

        $boIds = [];
        $bankIds = [];

        // Loop untuk selectedBo
        foreach ($selectedBo as $value) {
            $internalBatch = DraftBackOffice::where('id', $value)->first();

            $trxCount += $internalBatch->transaction_count;
            $boSettlement += $internalBatch->bank_transfer;
            $feeMdrMerchant += $internalBatch->fee_mdr_merchant;
            $feeBankMerchant += $internalBatch->fee_bank_merchant;
            $taxPayment += $internalBatch->tax_payment;
            $totalSales += $internalBatch->transaction_amount;
            $merchant_id = $internalBatch->merchant_id;
            $bank_id = $internalBatch->bank_id;
            $sumTransaction += $internalBatch->transaction_amount;
            $batchMid = $internalBatch->mid;

            $merchantPayment += Utils::calculateMerchantPayment($boSettlement, $feeMdrMerchant, $feeBankMerchant, $taxPayment);

            // Simpan bo_id untuk digunakan nanti
            $boIds[] = $value;
        }

        // Loop untuk selectedBank
        foreach ($selectedBank as $value) {
            $bank = ReconcileUnmatch::where('id', $value)->first();
            $bankIds[] = $value;
            $bankSettlement += (float) $bank->bank_transfer;
        }

        $rounded_value = round((int) $bankSettlement);
        $amount_credit = number_format($rounded_value, 0, '', '');
        $diff = abs((float) $boSettlement - (float) $bankSettlement);
        $treshold = Utils::calculateTreshold($trxCount);
        $status = Utils::getStatusReconcile($treshold, $boSettlement, $bankSettlement);

        if ($status == "MATCH") {
            foreach ($selectedBank as $key => $value) {
                $det = ReconcileUnmatch::with('header')->where('id', $value)->first();
                $carbonDate = Carbon::parse($det->settlement_date);

                // Menghapus rekaman lama jika ditemukan
                $oldRec = ReconcileReport::where('mid', $batchMid)
                    ->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])
                    ->whereDate('settlement_date', $carbonDate)
                    ->first();

                if ($oldRec) {
                    $oldRec->status = 'deleted';
                    $oldRec->modified_by = $user->name;
                    $oldRec->save();
                }

                // Mengupdate bo_id pada $det dengan nilai dari DraftBackOffice
                // $det->bo_id = $boIds[$key] ?? null;

                // Membuat laporan rekonsiliasi
                $reconcile = ReconcileReport::create([
                    'draft_id' => implode("//", $bankIds),
                    'bo_id' => implode("//", $boIds),
                    'token_applicant' => $det->token_applicant,
                    'statement_date' => $det->settlement_date,
                    'status' => $status,
                    'tid' => $det->tid,
                    'mid' => $det->mid,
                    'trx_counts' => $trxCount,
                    'total_sales' => $totalSales,
                    'processor_payment' => $det->processor_payment,
                    'internal_payment' => $boSettlement,
                    'merchant_payment' => $merchantPayment,
                    'merchant_id' => $merchant_id,
                    'merchant_name' => $det->merchant_name,
                    'tax_payment' => $taxPayment,
                    'fee_mdr_merchant' => $feeMdrMerchant,
                    'fee_bank_merchant' => $feeBankMerchant,
                    'bank_transfer' => $boSettlement,
                    'transfer_amount' => $sumTransaction,
                    'bank_settlement_amount' => $amount_credit,
                    'dispute_amount' => $diff,
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
                // foreach ($boIds as $key => $value) {
                // }

                if ($reconcile) {
                    // Update status DraftBackOffice
                    foreach ($selectedBo as $value) {
                        DraftBackOffice::where('id', $value)->update([
                            'status_reconcile' => 'reconciled',
                            'reconcile_date' => Carbon::now(),
                        ]);
                    }

                    // Update status ReconcileUnmatch
                    foreach ($selectedBank as $value) {
                        ReconcileUnmatch::where('id', $value)->update([
                            'status' => $status,
                            'modified_by' => $user->name,
                            'status_reconcile' => true,
                            'reconcile_date' => Carbon::now(),
                        ]);
                    }
                }

                return response()->json(['message' => 'Successfully Reconcile data!', 'status' => true], 200);
            }
        } else {
            return response()->json(['message' => ['Data Not Match!'], 'status' => false], 200);
        }
    }

    public function reconcile14Agus(Request $request)
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

        foreach ($selectedBo as $boValue) {
            $internalBatch = DraftBackOffice::where('id', $boValue)->first();
            $batchMid = $internalBatch->mid;

            foreach ($selectedBank as $bankValue) {
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
                        ->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])
                        ->whereDate('settlement_date', $carbonDate)
                        ->first();

                    if ($oldRec) {
                        $oldRec->status = 'deleted';
                        $oldRec->modified_by = $user->name;
                        $oldRec->save();
                    }

                    // Create reconciliation report
                    $reconcile = ReconcileReport::create([
                        'draft_id' => implode("//", $bankIds),
                        'bo_id' => implode("//", $boIds),
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
                        'dispute_amount' => $diff,
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
                    }

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

                    // Break the inner loop as we've processed the matching pair
                    break;
                }
            }

            // If no matching MID found, create a separate entry
            if (empty($bankIds)) {
                // Handle separate entry for unmatched MID
                $bankSettlement = 0;  // Reset bankSettlement

                foreach ($selectedBank as $bankValue) {
                    $bank = ReconcileUnmatch::where('id', $bankValue)->first();

                    // Create reconciliation report with only the unmatched bank entry
                    $carbonDate = Carbon::parse($bank->settlement_date);

                    $reconcile = ReconcileReport::create([
                        'draft_id' => $bankValue,
                        'bo_id' => $boValue,
                        'token_applicant' => $bank->token_applicant,
                        'statement_date' => $bank->settlement_date,
                        'status' => 'NOT_MATCH',
                        'tid' => $bank->tid,
                        'mid' => $bank->mid,
                        'trx_counts' => $internalBatch->transaction_count,
                        'total_sales' => $internalBatch->transaction_amount,
                        'processor_payment' => $bank->processor_payment,
                        'internal_payment' => $internalBatch->bank_transfer,
                        'merchant_payment' => Utils::calculateMerchantPayment($internalBatch->bank_transfer, $internalBatch->fee_mdr_merchant, $internalBatch->fee_bank_merchant, $internalBatch->tax_payment),
                        'merchant_id' => $internalBatch->merchant_id,
                        'merchant_name' => $bank->merchant_name,
                        'tax_payment' => $internalBatch->tax_payment,
                        'fee_mdr_merchant' => $internalBatch->fee_mdr_merchant,
                        'fee_bank_merchant' => $internalBatch->fee_bank_merchant,
                        'bank_transfer' => $internalBatch->bank_transfer,
                        'transfer_amount' => $internalBatch->transaction_amount,
                        'bank_settlement_amount' => $internalBatch->bank_transfer,
                        'dispute_amount' => abs((float) $internalBatch->bank_transfer - (float) $bank->bank_transfer),
                        'created_by' => $user->name,
                        'modified_by' => $user->name,
                        'settlement_date' => $carbonDate,
                        'variance' => abs((float) $internalBatch->bank_transfer - (float) $bank->bank_transfer),
                        'bank_id' => $internalBatch->bank_id,
                        'category_report' => 'manual',
                        'status_manual' => true,
                        'status_reconcile' => 'report',
                        'reconcile_date' => Carbon::now(),
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Successfully Reconcile data!', 'status' => true], 200);
    }

    public function reconcileFIXING(Request $request)
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
                        ->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])
                        ->whereDate('settlement_date', $carbonDate)
                        ->first();

                    if ($oldRec) {
                        $oldRec->status = 'deleted';
                        $oldRec->modified_by = $user->name;
                        $oldRec->save();
                    }

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
                        'dispute_amount' => $diff,
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

    public function reconcile(Request $request)
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
                        $oldRec->dispute_amount += $diff;
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
                            'dispute_amount' => $diff,
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






    // public function reconcile12August(Request $request)
    // {
    //     // return response()->json(['message' => [$var], 'status' => false], 200);
    //     $user = Auth::user();
    //     if (!isset($request->selectedBo)) {
    //         return response()->json(['message' => ["Please select Back Office Settlement!"], 'status' => false], 200);
    //     }
    //     if (!isset($request->selectedBank)) {
    //         return response()->json(['message' => ["Please select Bank Settlement!"], 'status' => false], 200);
    //     }

    //     $selectedBo = explode(',', $request->selectedBo);
    //     $selectedBank = explode(',', $request->selectedBank);

    //     $trxCount = 0;
    //     $boSettlement = 0;
    //     $feeMdrMerchant = 0;
    //     $feeBankMerchant = 0;
    //     $taxPayment = 0;
    //     $totalSales = 0;
    //     $sumTransaction = 0;
    //     $merchantPayment = 0;
    //     $bankSettlement = 0;
    //     $batchMid = '';


    //     foreach ($selectedBo as $key => $value) {
    //         // $transaction = InternalTransaction::with('header')->where('id', $value)->first();
    //         $internalBatch = DraftBackOffice::where('id', $value)->first();

    //         $trxCount = $trxCount + $internalBatch->transaction_count;
    //         $boSettlement = $boSettlement + $internalBatch->bank_transfer;
    //         $feeMdrMerchant = $feeMdrMerchant + $internalBatch->fee_mdr_merchant;
    //         $feeBankMerchant = $feeBankMerchant + $internalBatch->fee_bank_merchant;
    //         $taxPayment = $taxPayment + $internalBatch->tax_payment;
    //         $totalSales = $totalSales + $internalBatch->transaction_amount;
    //         $merchant_id = $internalBatch->merchant_id;
    //         $bank_id = $internalBatch->bank_id;
    //         $sumTransaction = $sumTransaction + $internalBatch->transaction_amount;
    //         $batchMid = $internalBatch->mid;

    //         $merchantPayment = $merchantPayment + Utils::calculateMerchantPayment($boSettlement, $feeMdrMerchant, $feeBankMerchant, $taxPayment);
    //     }

    //     foreach ($selectedBank as $key => $value) {
    //         $bank = ReconcileUnmatch::where('id', $value)->first();
    //         // $sumBank = $sumBank + (float)$bank->amount_credit;
    //         // $amount_credit = $amount_credit + $bank->amount_credit;
    //         $bankSettlement = $bankSettlement + (float) $bank->bank_transfer;
    //     }
    //     // $trxCount = 1
    //     // $boSettlement = 4620259
    //     // $feeMdrMerchant = 86821
    //     // $feeBankMerchant = 72741.5
    //     // $taxPayment = 9550
    //     // $totalSales = 4596629
    //     // $merchant_id = 4782
    //     // $sumTransaction = 4596629
    //     // $batchMid = 000002187013268
    //     // $merchantPayment =  4596629.5
    //     // return response()->json(['message' => [json_encode($internalBatch)], 'status' => false], 200);
    //     $rounded_value = round((int) $bankSettlement);
    //     $amount_credit = number_format($rounded_value, 0, '', '');

    //     $diff = abs((float) $boSettlement - (float) $bankSettlement);

    //     $treshold = Utils::calculateTreshold($trxCount);
    //     $status = Utils::getStatusReconcile($treshold, $boSettlement, $bankSettlement);
    //     Log::info($status);
    //     $diff = abs((float) $boSettlement - (float) $bankSettlement);

    //     if ($status == "MATCH") {
    //         foreach ($selectedBank as $key => $value) {
    //             $det = ReconcileUnmatch::with('header')->where('id', $value)->first();
    //             // $internalBatch = InternalBatch::where('mid', 'like', '%' . $value->mid . '%')->get();
    //             $carbonDate = $det->settlement_date;
    //             // dd(date('Y-m-d', $carbonDate));
    //             $carbonDateParsed = Carbon::parse($carbonDate);
    //             $oldRec = ReconcileReport::where('mid', $batchMid)
    //                 ->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])
    //                 ->whereDate('settlement_date', $carbonDateParsed)
    //                 ->first();
    //             if ($oldRec) {
    //                 $oldRec->status = 'deleted';
    //                 $oldRec->modified_by = $user->name;
    //                 $oldRec->save();
    //             }
    //             // $reconcile = ReconcileResult::create([
    //             //     'token_applicant' => $det->token_applicant,
    //             //     'statement_id' => $det->id,
    //             //     'request_id' => $det->header->id,
    //             //     'status' => $status,
    //             //     'mid' => $batchMid,
    //             //     'trx_counts' => $trxCount, // total transaksi 1 batch
    //             //     'total_sales' => $totalSales, // sum transaction_amout di internal_taransaction 
    //             //     'processor_payment' => $det->processor_payment,
    //             //     'internal_payment' => $boSettlement, // bank_payment
    //             //     'merchant_payment' => $merchantPayment, // bank_payment - merchant_fee_amount
    //             //     'merchant_id' => $merchant_id,
    //             //     'transfer_amount' => $sumTransaction, // transaction_amount di internal_batch
    //             //     'bank_settlement_amount' => $amount_credit, // bank_settlement
    //             //     'dispute_amount' => $diff, // dispute_amount
    //             //     'created_by' => $user->name,
    //             //     'modified_by' => $user->name,
    //             //     'settlement_date' => $carbonDate
    //             // ]);

    //             $reconcile = ReconcileReport::create([
    //                 'draft_id' => $det->draft_id,
    //                 'bo_id' => $det->bo_id,
    //                 'bo_date' => "",
    //                 'token_applicant' => $det->token_applicant,
    //                 'statement_date' => $det->settlement_date,
    //                 // 'statement_id' => $det->statement_id,
    //                 // 'request_id' => $det->request_id,
    //                 'status' => $status,
    //                 'tid' => $det->tid,
    //                 'mid' => $det->mid,
    //                 'batch_fk' => $det->batch_fk,
    //                 'trx_counts' => $det->trx_counts,
    //                 'total_sales' => $det->total_sales,
    //                 'processor_payment' => $det->processor_payment,
    //                 'internal_payment' => $det->internal_payment,
    //                 'merchant_payment' => $det->merchant_payment,
    //                 'merchant_id' => $det->merchant_id,
    //                 'merchant_name' => $det->merchant_name,
    //                 'tax_payment' => $det->tax_payment,
    //                 'fee_mdr_merchant' => $det->fee_mdr_merchant,
    //                 'fee_bank_merchant' => $det->fee_bank_merchant,
    //                 'bank_transfer' => $det->bank_transfer,
    //                 'transfer_amount' => $det->transfer_amount,
    //                 'bank_settlement_amount' => $det->bank_settlement_amount,
    //                 'dispute_amount' => $det->dispute_amount,
    //                 'created_by' => $det->created_by,
    //                 'modified_by' => $det->modified_by,
    //                 'settlement_date' => $carbonDateParsed,
    //                 'variance' => $det->variance,
    //                 'bank_id' => $bank_id,
    //                 'category_report' => 'manual',
    //                 'status_manual' => true,
    //                 'status_reconcile' => true,

    //             ]);
    //             if ($reconcile) {
    //                 foreach ($selectedBo as $key => $value) {

    //                     DraftBackOffice::where('id', $value)->update([
    //                         'status_reconcile' => 'reconciled',
    //                         'reconcile_date' => Carbon::now()
    //                     ]);
    //                 }

    //                 $det->status = $status;
    //                 $det->modified_by = "manual";
    //                 $det->reconcile_date = Carbon::now();
    //                 $det->status_reconcile = true;
    //                 $det->save();

    //                 $internalBatch->status_reconcile = 'reconciled';
    //                 $internalBatch->reconcile_date = Carbon::now();
    //                 $internalBatch->save();

    //                 // $draft = ReconcileDraft::where('id', $det->draft_id)->update([
    //                 //     "status_manual" => false,
    //                 //     "status" => $status,
    //                 //     "status_reconcile" => "draft",
    //                 //     "reconcile_date" => Carbon::now()->format('Y-m-d H:i:s'),
    //                 //     "updated_at" => Carbon::now()->format('Y-m-d H:i:s'),
    //                 // ]);
    //             }
    //             return response()->json(['message' => 'Successfully Reconcile data!', 'status' => true], 200);
    //         }
    //         return response()->json(['message' => ['Failed Reconcile Data!'], 'status' => false], 200);
    //     } else {
    //         return response()->json(['message' => ['Data Not Match!'], 'status' => false], 200);
    //     }
    // }

    // public function reconcile2(Request $request)
    // {
    //     $user = Auth::user();

    //     if (!isset($request->selectedBo)) {
    //         return response()->json(['message' => ["Please select Back Office Settlement!"], 'status' => false], 200);
    //     }

    //     if (!isset($request->selectedBank)) {
    //         return response()->json(['message' => ["Please select Bank Settlement!"], 'status' => false], 200);
    //     }

    //     $selectedBo = explode(',', $request->selectedBo);
    //     $selectedBank = explode(',', $request->selectedBank);

    //     $trxCount = 0;
    //     $boSettlement = 0;
    //     $feeMdrMerchant = 0;
    //     $feeBankMerchant = 0;
    //     $taxPayment = 0;
    //     $totalSales = 0;
    //     $sumTransaction = 0;
    //     $merchantPayment = 0;
    //     $bankSettlement = 0;
    //     $batchMid = '';
    //     $merchant_id = null;
    //     $bank_id = null;

    //     // Loop untuk selectedBo
    //     foreach ($selectedBo as $value) {
    //         $internalBatch = DraftBackOffice::where('id', $value)->first();

    //         $trxCount += $internalBatch->transaction_count;
    //         $boSettlement += $internalBatch->bank_transfer;
    //         $feeMdrMerchant += $internalBatch->fee_mdr_merchant;
    //         $feeBankMerchant += $internalBatch->fee_bank_merchant;
    //         $taxPayment += $internalBatch->tax_payment;
    //         $totalSales += $internalBatch->transaction_amount;
    //         $merchant_id = $internalBatch->merchant_id;
    //         $bank_id = $internalBatch->bank_id;
    //         $sumTransaction += $internalBatch->transaction_amount;
    //         $batchMid = $internalBatch->mid;

    //         $merchantPayment += Utils::calculateMerchantPayment($boSettlement, $feeMdrMerchant, $feeBankMerchant, $taxPayment);
    //     }

    //     // Loop untuk selectedBank
    //     foreach ($selectedBank as $value) {
    //         $bank = ReconcileUnmatch::where('id', $value)->first();
    //         $bankSettlement += (float) $bank->bank_transfer;
    //     }

    //     $rounded_value = round((int) $bankSettlement);
    //     $amount_credit = number_format($rounded_value, 0, '', '');
    //     $diff = abs((float) $boSettlement - (float) $bankSettlement);
    //     $treshold = Utils::calculateTreshold($trxCount);
    //     $status = Utils::getStatusReconcile($treshold, $boSettlement, $bankSettlement);

    //     if ($status == "MATCH") {
    //         foreach ($selectedBank as $value) {
    //             $det = ReconcileUnmatch::with('header')->where('id', $value)->first();
    //             $carbonDate = Carbon::parse($det->settlement_date);

    //             // Menghapus rekaman lama jika ditemukan
    //             $oldRec = ReconcileReport::where('mid', $batchMid)
    //                 ->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])
    //                 ->whereDate('settlement_date', $carbonDate)
    //                 ->first();

    //             if ($oldRec) {
    //                 $oldRec->status = 'deleted';
    //                 $oldRec->modified_by = $user->name;
    //                 $oldRec->save();
    //             }

    //             // Membuat laporan rekonsiliasi
    //             $reconcile = ReconcileReport::create([
    //                 'draft_id' => $det->draft_id,
    //                 'bo_id' => $det->bo_id,
    //                 'token_applicant' => $det->token_applicant,
    //                 'statement_date' => $det->settlement_date,
    //                 'status' => $status,
    //                 'tid' => $det->tid,
    //                 'mid' => $det->mid,
    //                 'trx_counts' => $det->trx_counts,
    //                 'total_sales' => $det->total_sales,
    //                 'processor_payment' => $det->processor_payment,
    //                 'internal_payment' => $boSettlement,
    //                 'merchant_payment' => $merchantPayment,
    //                 'merchant_id' => $merchant_id,
    //                 'merchant_name' => $det->merchant_name,
    //                 'tax_payment' => $det->tax_payment,
    //                 'fee_mdr_merchant' => $det->fee_mdr_merchant,
    //                 'fee_bank_merchant' => $det->fee_bank_merchant,
    //                 'bank_transfer' => $det->bank_transfer,
    //                 'transfer_amount' => $sumTransaction,
    //                 'bank_settlement_amount' => $amount_credit,
    //                 'dispute_amount' => $diff,
    //                 'created_by' => $user->name,
    //                 'modified_by' => $user->name,
    //                 'settlement_date' => $carbonDate,
    //                 'variance' => $det->variance,
    //                 'bank_id' => $bank_id,
    //                 'category_report' => 'manual',
    //                 'status_manual' => true,
    //                 'status_reconcile' => true,
    //             ]);

    //             if ($reconcile) {
    //                 // Update status DraftBackOffice
    //                 foreach ($selectedBo as $value) {
    //                     DraftBackOffice::where('id', $value)->update([
    //                         'status_reconcile' => 'reconciled',
    //                         'reconcile_date' => Carbon::now(),
    //                     ]);
    //                 }

    //                 // Update status ReconcileUnmatch
    //                 foreach ($selectedBank as $value) {
    //                     ReconcileUnmatch::where('id', $value)->update([
    //                         'status' => $status,
    //                         'modified_by' => $user->name,
    //                         'status_reconcile' => true,
    //                         'reconcile_date' => Carbon::now(),
    //                     ]);
    //                 }
    //                 //    $det->status = $status;
    //                 //    $det->modified_by = $user->name;
    //                 //    $det->reconcile_date = Carbon::now();
    //                 //    $det->status_reconcile = true;
    //                 //    $det->save(); 
    //             }

    //             return response()->json(['message' => 'Successfully Reconcile data!', 'status' => true], 200);
    //         }
    //     } else {
    //         return response()->json(['message' => ['Data Not Match!'], 'status' => false], 200);
    //     }
    // }


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
                    'dispute_amount' => $diff, // dispute_amount
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

        $disp = ReconcileReport::where('dispute_amount', '>', 0)
            ->where('status_reconcile', 'approved')
            ->pluck('dispute_amount')->sum();
        $dispcount = ReconcileReport::where('dispute_amount', '>', 0)
            ->where('status_reconcile', 'approved')
            ->pluck('dispute_amount')->count();

        $status = request()->query('status');

        $query1 = ReconcileReport::where('status_reconcile', 'approved');
        $query2 = ReconcileReport::where('status_reconcile', 'approved');
        $query3 = ReconcileReport::where('status_reconcile', 'approved');
        $query4 = ReconcileReport::where('status_reconcile', 'approved');
        $query5 = ReconcileReport::where('status_reconcile', 'approved');
        $query6 = ReconcileReport::where('status_reconcile', 'approved');

        $report = ReconcileReport::pluck('id')->where('status_reconcile', 'approved');

        $resmatch = $query1->where('status', 'MATCH')->count();
        $resdispute = $query2->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])->count();
        $resonHold = $query3->where('status', 'ON_HOLD')->count();

        $ressumMatch = $query4->where('status', 'MATCH')->sum('total_sales');
        $ressumDispute = $query5->whereIn('status', ['NOT_MATCH', 'NOT_FOUND'])->sum('total_sales');
        $ressumHold = $query6->where('status', 'ON_HOLD')->sum('total_sales');


        return view(
            'modules.reconcile.show',
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
    public function approveddata(Request $request)
    {
        $status = request()->query('status');

        $query = ReconcileReport::with('merchant', 'bank_account')->where('status_reconcile', 'approved');

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
    public function reportdata(Request $request)
    {
        $token_applicant = request()->query('token');
        $status = request()->query('status');

        $query = ReconcileReport::with('merchant', 'bank_account')->where('status_reconcile', '!=', 'draft');
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
        return Excel::download(new ReconcileExport($token_applicant), 'reconcile-' . $filename . '.xlsx');
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
            Log::info($th);
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
            $query->where(DB::raw('DATE(created_at)'), '>=', $startDate);
            $query->where(DB::raw('DATE(created_at)'), '<=', $endDate);
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

}
