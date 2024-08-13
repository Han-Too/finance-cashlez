<?php

namespace App\Helpers;

use App\Models\DraftBackOffice;
use App\Models\InternalBatch;
use App\Models\InternalTransaction;
use App\Models\ReconcileDraft;
use App\Models\ReconcileResult;
use App\Models\ReconcileUnmatch;
use App\Models\ReportPartner;
use App\Models\UploadBank;
use App\Models\UploadBankDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Reconcile
{
    public static function midBoBankDraft($BoStartDate, $BoEndDate, $token, $name,$tokenlist)
    {
        // $channelName = Utils::getChannel($channel);
        DB::beginTransaction();
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
                    settlement_audit_id,tax_payment,created_by,created_at,status
                ')
                ->where(DB::raw('DATE(created_at)'), '>=', $BoStartDate)
                ->where(DB::raw('DATE(created_at)'), '<=', $BoEndDate)
                // ->where('bank_id', $channel)
                ->where('status', 'SUCCESSFUL')
                ->groupBy('mid', 'merchant_id', 'created_date','merchant_name','id','bank_id',
                'tid','merchant_name','processor','batch_running_no','merchant_id','mid_ppn',
                'settlement_audit_id','tax_payment','created_by','created_at','status')
                ->get();
            foreach ($boData as $key => $value) {
                $modMid = substr($value->mid, 5);
                $bsData = UploadBankDetail::selectRaw('
                        SUM(amount_credit) as amount_credit,
                        mid, token_applicant,id
                    ')
                    ->with('header')
                    ->where('token_applicant', $token)
                    ->where('mid', 'like', '%' . $modMid . '%')
                    ->where('type_code', '001')
                    ->where('is_reconcile', false)
                    // ->where(DB::raw('DATE(transfer_date)'), '>=', $BsStartDate)
                    // ->where(DB::raw('DATE(transfer_date)'), '<=', $BsEndDate)
                    ->groupBy('mid', 'token_applicant','id')
                    ->first();
                    // dd($bsData);
                if ($bsData) {
                    $bankSettlement = $bsData->amount_credit;
                    $token_applicant = $bsData->header->token_applicant;
                } else {
                    $bankSettlement = 0;
                    $token_applicant = null;
                }

                $trxCount = $value->transaction_count;
                $boSettlement = Utils::customRound($value->bank_transfer);

                $feeMdrMerchant = $value->fee_mdr_merchant;
                $feeBankMerchant = $value->fee_bank_merchant;
                $taxPayment = $value->tax_payment;
                $totalSales = $value->total_sales_amount != 0 ? $value->total_sales_amount :  1;

                $merchant_id = $value->merchant_id;
                $sumTransaction = $value->transaction_amount;

                $merchantPayment = Utils::calculateMerchantPayment($boSettlement, $feeMdrMerchant, $feeBankMerchant, $taxPayment); // tanya mas tri

                $rounded_value = round((int) $bankSettlement);
                $amount_credit = number_format($rounded_value, 0, '', '');

                $diff = abs((float) $boSettlement - (float) $bankSettlement);
                $treshold = Utils::calculateTreshold($trxCount);
                // $status = Utils::getNewStatusReconcile($totalSales, abs((float) $boSettlement - (float) $bankSettlement));
                $status = Utils::getStatusReconcile($treshold, $boSettlement, $bankSettlement);

                $reconcile = ReconcileDraft::create([
                    'name' => $name,
                    'merchant_name' => $value->merchant_name,
                    'token_applicant' => $tokenlist,
                    'status' => $status,
                    'mid' => $value->mid,
                    'trx_counts' => $trxCount, // total transaksi 1 batch
                    'bank_transfer' => $boSettlement,
                    'tax_payment' => $value->tax_payment,
                    "fee_mdr_merchant" => $value->fee_mdr_merchant,
                    "fee_bank_merchant" => $value->fee_bank_merchant,
                    'total_sales' => $totalSales, // sum transaction_amout di internal_taransaction 
                    'processor_payment' => "5",
                    'internal_payment' => $boSettlement, // bank_payment
                    'merchant_payment' => $merchantPayment, // bank_payment - merchant_fee_amount
                    'merchant_id' => $value->merchant_id,
                    'transfer_amount' => $sumTransaction, // transaction_amount di internal_batch
                    'bank_settlement_amount' => $amount_credit, // bank_settlement
                    'dispute_amount' => $diff, // dispute_amount
                    'created_by' => 'System',
                    'variance' => $diff, // dispute_amount
                    'modified_by' => "-",
                    'status_parnert' => '0',
                    'status_reconcile' => 'draft',
                    'status_manual' => false,
                    'reconcile_date' => Carbon::now(),
                    'settlement_date' => $value->created_date,
                    'bo_id' => $value->id,
                    'bank_id' => $value->bank_id,
                    // 'bo_date' => $trans->settlement_date,
                ]);

                DraftBackOffice::create([
                    'bo_id' => $value->id,
                    'batch_fk' => $value->batch_fk,
                    'transaction_count' => $value->transaction_count,
                    'status' => $value->status,
                    'tid' => $value->tid,
                    'mid' => $value->mid,
                    'merchant_name' => $value->merchant_name,
                    'processor' => $value->processor,
                    'batch_running_no' => $value->batch_running_no,
                    'merchant_id' => $value->merchant_id,
                    'mid_ppn' => $value->mid_ppn,
                    'transaction_amount' => $value->transaction_amount,
                    'total_sales_amount' => $value->total_sales_amount,
                    'settlement_audit_id' => $value->settlement_audit_id,
                    'tax_payment' => $value->tax_payment,
                    'fee_mdr_merchant' => $value->fee_mdr_merchant,
                    'fee_bank_merchant' => $value->fee_bank_merchant,
                    'bank_transfer' => $value->bank_transfer,
                    'bank_id' => $value->bank_id,
                    'created_by' => $value->created_by,
                    'created_at' => $value->created_at,
                    'draft_token' => $tokenlist,
                ]);

                // if($status == "MATCH"){
                //     ReconcileResult::create([
                //         'token_applicant' => $reconcile->token_applicant,
                //         'statement_id' => $reconcile->id,
                //         'request_id' => $reconcile->header->id,
                //         'status' => $status,
                //         'mid' => $value->mid,
                //         'trx_counts' => $trxCount, // total transaksi 1 batch
                //         'total_sales' => $totalSales, // sum transaction_amout di internal_taransaction 
                //         'processor_payment' => $reconcile->processor_payment,
                //         'internal_payment' => $boSettlement, // bank_payment
                //         'merchant_payment' => $merchantPayment, // bank_payment - merchant_fee_amount
                //         'merchant_id' => $merchant_id,
                //         'transfer_amount' => $sumTransaction, // transaction_amount di internal_batch
                //         'bank_settlement_amount' => $amount_credit, // bank_settlement
                //         'dispute_amount' => $diff, // dispute_amount
                //         'created_by' => $user->name,
                //         'modified_by' => $user->name,
                //         'settlement_date' => $reconcile->created_date
                //     ]);
                // } else {
                //     ReconcileUnmatch::create([
                //         'draft_id' => $reconcile->id,
                //         'name' => $reconcile->name,
                //         'merchant_name' => $reconcile->merchant_name,
                //         'token_applicant' => $reconcile->token_applicant,
                //         'status' => $reconcile->status,
                //         'mid' => $reconcile->mid,
                //         'trx_counts' => $reconcile->trx_counts,
                //         'bank_transfer' => $reconcile->bank_transfer,
                //         'tax_payment' => $reconcile->tax_payment,
                //         "fee_mdr_merchant" => $reconcile->fee_mdr_merchant,
                //         "fee_bank_merchant" => $reconcile->fee_bank_merchant,
                //         'total_sales' => $reconcile->total_sales,
                //         'processor_payment' => $reconcile->processor_payment,
                //         'internal_payment' => $reconcile->internal_payment,
                //         'merchant_payment' => $reconcile->merchant_payment,
                //         'merchant_id' => $reconcile->merchant_id,
                //         'transfer_amount' => $reconcile->transfer_amount,
                //         'bank_settlement_amount' => $reconcile->bank_settlement_amount,
                //         'dispute_amount' => $reconcile->dispute_amount,
                //         'created_by' => $reconcile->created_by,
                //         'variance' => $reconcile->variance,
                //         'modified_by' => $reconcile->modified_by,
                //         'status_parnert' => $reconcile->status_parnert,
                //         'status_reconcile' => false,
                //         'settlement_date' => $reconcile->settlement_date,
                //     ]);
                // }

                
                UploadBank::where('token_applicant',$token)
                ->update([
                    'is_reconcile' => "3",
                ]);
                UploadBankDetail::where('token_applicant', $token)
                    ->where('mid', 'like', '%' . $modMid . '%')
                    ->where('type_code', '001')
                    ->where('is_reconcile', false)
                    ->update([
                        'is_reconcile' => "3",
                    ]);
            }
            

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::info($th);
            return false;
        }
    }
    public static function midBoBank($BoStartDate, $BoEndDate, $channel, $BsStartDate, $BsEndDate)
    {
        $channelName = Utils::getChannel($channel);

        DB::beginTransaction();
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
                    mid,
                    DATE(created_at) as created_date
                ')
                ->where(DB::raw('DATE(created_at)'), '>=', $BoStartDate)
                ->where(DB::raw('DATE(created_at)'), '<=', $BoEndDate)
                ->where('bank_id', $channel)
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
                    ->where('mid', 'like', '%' . $modMid . '%')
                    ->where('description2', $channelName)
                    ->where('type_code', '001')
                    ->where('is_reconcile', false)
                    ->where(DB::raw('DATE(transfer_date)'), '>=', $BsStartDate)
                    ->where(DB::raw('DATE(transfer_date)'), '<=', $BsEndDate)
                    ->groupBy('mid', 'token_applicant')
                    ->first();
                if ($bsData) {
                    $bankSettlement = $bsData->amount_credit;
                    $token_applicant = $bsData->header->token_applicant;
                } else {
                    $bankSettlement = 0;
                    $token_applicant = null;
                }

                $trxCount = $value->transaction_count;
                $boSettlement = Utils::customRound($value->bank_transfer);

                $feeMdrMerchant = $value->fee_mdr_merchant;
                $feeBankMerchant = $value->fee_bank_merchant;
                $taxPayment = $value->tax_payment;
                $totalSales = $value->total_sales_amount;

                $merchant_id = $value->merchant_id;
                $sumTransaction = $value->transaction_amount;

                $merchantPayment = Utils::calculateMerchantPayment($boSettlement, $feeMdrMerchant, $feeBankMerchant, $taxPayment); // tanya mas tri

                $rounded_value = round((int) $bankSettlement);
                $amount_credit = number_format($rounded_value, 0, '', '');

                $diff = abs((float) $boSettlement - (float) $bankSettlement);
                $treshold = Utils::calculateTreshold($trxCount);
                $status = Utils::getStatusReconcile($treshold, $boSettlement, $bankSettlement,$totalSales);

                $reconcile = ReconcileResult::create([
                    'token_applicant' => $token_applicant,
                    'statement_id' => $bsData ? $bsData->id : null,
                    'request_id' => $bsData ? $bsData->header->id : null,
                    'status' => $status,
                    // 'tid' => $tid,
                    'mid' => $value->mid,
                    // 'batch_fk' => $batch_fk, 
                    'trx_counts' => $trxCount, // total transaksi 1 batch
                    'total_sales' => $totalSales, // sum transaction_amout di internal_taransaction 
                    'processor_payment' => $channelName,
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
                    'settlement_date' => $value->created_date
                ]);
                if ($token_applicant) {
                    $uploadBank = UploadBank::where('token_applicant', $token_applicant)->update([
                        'is_reconcile' => true
                    ]);
                    $bankDetail = UploadBankDetail::where('mid', 'like', '%' . $modMid . '%')
                        ->where('description2', $channelName)
                        ->where('type_code', '001')
                        ->where(DB::raw('DATE(transfer_date)'), '>=', $BsStartDate)
                        ->where(DB::raw('DATE(transfer_date)'), '<=', $BsEndDate)
                        ->update([
                            'is_reconcile' => $status == 'MATCH' ? true : false
                        ]);
                }
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public static function vlookupBoBank($BoStartDate, $BoEndDate, $channel, $BsStartDate, $BsEndDate)
    {
        $channelName = Utils::getChannel($channel);

        DB::beginTransaction();
        try {
            $boData = InternalBatch::selectRaw('
                    SUM(transaction_count) as transaction_count,
                    SUM(bank_transfer) as bank_transfer,
                    SUM(fee_mdr_merchant) as fee_mdr_merchant,
                    SUM(fee_bank_merchant) as fee_bank_merchant,
                    SUM(tax_payment) as tax_payment,
                    SUM(transaction_amount) as transaction_amount,
                    SUM(total_sales_amount) as total_sales_amount,
                    DATE(created_at) as created_date
                ')
                ->where(DB::raw('DATE(created_at)'), '>=', $BoStartDate)
                ->where(DB::raw('DATE(created_at)'), '<=', $BoEndDate)
                ->where('bank_id', $channel)
                ->where('status', 'SUCCESSFUL')
                ->groupBy('created_date')
                ->get();

            $trxCount = 0;
            $boSettlement = 0;

            $feeMdrMerchant = 0;
            $feeBankMerchant = 0;
            $taxPayment = 0;
            $totalSales = 0;

            $sumTransaction = 0;
            $merchantPayment = 0;

            foreach ($boData as $key => $value) {
                $bsData = UploadBankDetail::selectRaw('
                        SUM(amount_credit) as amount_credit
                    ')
                    ->with('header')
                    ->where('description2', $channelName)
                    ->where('type_code', '001')
                    ->where('is_reconcile', false)
                    ->where(DB::raw('DATE(transfer_date)'), '>=', $BsStartDate)
                    ->where(DB::raw('DATE(transfer_date)'), '<=', $BsEndDate)
                    ->first();

                if ($bsData) {
                    $bankSettlement = $bsData->amount_credit;
                    $token_applicant = null;
                } else {
                    $bankSettlement = 0;
                    $token_applicant = null;
                }

                $trxCount += $value->transaction_count;
                $boSettlement += Utils::customRound($value->bank_transfer);

                $feeMdrMerchant += $value->fee_mdr_merchant;
                $feeBankMerchant += $value->fee_bank_merchant;
                $taxPayment += $value->tax_payment;
                $totalSales += $value->total_sales_amount;

                $sumTransaction += $value->transaction_amount;

                $merchantPayment += Utils::calculateMerchantPayment($boSettlement, $feeMdrMerchant, $feeBankMerchant, $taxPayment); // tanya mas tri

                $rounded_value = round((int) $bankSettlement);
                $amount_credit = number_format($rounded_value, 0, '', '');

                $diff = abs((float) $boSettlement - (float) $bankSettlement);
                $treshold = Utils::calculateTreshold($trxCount);
                $status = Utils::getStatusReconcile($treshold, $boSettlement, $bankSettlement,$totalSales);

                $settlementDate = Carbon::createFromFormat('Y-m-d', $value->created_date);
                // $settlementDate = $value->created_date;
                // dd($settlementDate);

                $reconcile = ReconcileResult::create([
                    'token_applicant' => $token_applicant,
                    'statement_id' => $bsData ? $bsData->id : null,
                    'request_id' => $bsData->header ? $bsData->header->id : null,
                    'status' => $status,
                    // 'tid' => $tid,
                    'mid' => '',
                    // 'batch_fk' => $batch_fk, 
                    'trx_counts' => $trxCount, // total transaksi 1 batch
                    'total_sales' => $totalSales, // sum transaction_amout di internal_taransaction 
                    'processor_payment' => $channelName,
                    'internal_payment' => $boSettlement, // bank_payment
                    'merchant_payment' => $merchantPayment, // bank_payment - merchant_fee_amount
                    'merchant_id' => '',
                    'transfer_amount' => $sumTransaction, // transaction_amount di internal_batch
                    'bank_settlement_amount' => $amount_credit, // bank_settlement
                    'dispute_amount' => $diff, // dispute_amount
                    // 'tax_payment',
                    // 'fee_mdr_merchant',
                    // 'fee_bank_merchant',
                    // 'bank_transfer',
                    'created_by' => Auth::user()->name,
                    'modified_by' => null,
                    'settlement_date' => $settlementDate
                ]);
                if ($token_applicant) {
                    $uploadBank = UploadBank::where('token_applicant', $token_applicant)->update([
                        'is_reconcile' => true
                    ]);
                    $bankDetail = UploadBankDetail::where('description2', $channelName)
                        ->where('type_code', '001')
                        ->where(DB::raw('DATE(transfer_date)'), '>=', $BsStartDate)
                        ->where(DB::raw('DATE(transfer_date)'), '<=', $BsEndDate)
                        ->update([
                            'is_reconcile' => $status == 'MATCH' ? true : false
                        ]);
                }
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            return false;
        }
    }

    public static function rrnBoPartner($BoStartDate, $BoEndDate, $channel, $BsStartDate, $BsEndDate)
    {
        $channelName = Utils::getChannel($channel);

        DB::beginTransaction();
        try {
            $boData = InternalTransaction::selectRaw('
                    COUNT(*) as transaction_count,
                    SUM(bank_payment) as bank_payment,
                    SUM(merchant_fee_amount) as merchant_fee_amount,
                    SUM(bank_fee_amount) as bank_fee_amount,
                    SUM(tax_amount) as tax_amount,
                    SUM(transaction_amount) as transaction_amount,
                    retrieval_number
                ')
                ->where(DB::raw('DATE(created_at)'), '>=', $BoStartDate)
                ->where(DB::raw('DATE(created_at)'), '<=', $BoEndDate)
                ->where('bank_id', $channel)
                ->groupBy('retrieval_number')
                ->get();

            foreach ($boData as $key => $value) {
                $rrn = $value['retrieval_number'];

                $partnerData = ReportPartner::selectRaw('
                        SUM(net_amount) as net_amount,
                        token_applicant,
                        rrn,
                        DATE(created_at) as created_date
                    ')
                    ->with('header')
                    ->where('rrn', $rrn)
                    ->where('channel', $channelName)
                    ->where('is_reconcile', false)
                    ->where(DB::raw('DATE(date)'), '>=', $BsStartDate)
                    ->where(DB::raw('DATE(date)'), '<=', $BsEndDate)
                    ->groupBy('rrn', 'created_date', 'token_applicant')
                    ->first();

                if ($partnerData) {
                    $bankSettlement = $partnerData->net_amount;
                    $token_applicant = $partnerData->header->token_applicant;
                } else {
                    $bankSettlement = 0;
                    $token_applicant = null;
                }

                $trxCount = $value->transaction_count;
                $boSettlement = Utils::customRound($value->bank_transfer);

                $feeMdrMerchant = $value->merchant_fee_amount;
                $feeBankMerchant = $value->bank_fee_amount;
                $taxPayment = $value->tax_amount;
                $totalSales = $value->transaction_amount;

                $merchant_id = $value->merchant_id;
                $sumTransaction = $value->transaction_amount;

                $merchantPayment = Utils::calculateMerchantPayment($boSettlement, $feeMdrMerchant, $feeBankMerchant, $taxPayment); // tanya mas tri

                $rounded_value = round((int) $bankSettlement);
                $amount_credit = number_format($rounded_value, 0, '', '');

                $diff = abs((float) $boSettlement - (float) $bankSettlement);
                $treshold = Utils::calculateTreshold($trxCount);
                $status = Utils::getStatusReconcile($treshold, $boSettlement, $bankSettlement,$totalSales);


                $reconcile = ReconcileResult::create([
                    'token_applicant' => $token_applicant,
                    'statement_id' => $partnerData ? $partnerData->id : null,
                    'request_id' => $partnerData ? $partnerData->header->id : null,
                    'status' => $status,
                    // 'tid' => $tid,
                    'mid' => $rrn,
                    // 'batch_fk' => $batch_fk, 
                    'trx_counts' => $trxCount, // total transaksi 1 batch
                    'total_sales' => $totalSales, // sum transaction_amout di internal_taransaction 
                    'processor_payment' => $channelName,
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
                    'settlement_date' => $partnerData->created_date
                ]);
                if ($token_applicant) {
                    $uploadBank = UploadBank::where('token_applicant', $token_applicant)->update([
                        'is_reconcile' => true
                    ]);
                    $partnerDetail = ReportPartner::where('rrn', 'like', '%' . $rrn . '%')
                        ->where('channel', $channelName)
                        ->where(DB::raw('DATE(date)'), '>=', $BsStartDate)
                        ->where(DB::raw('DATE(date)'), '<=', $BsEndDate)
                        ->update([
                            'is_reconcile' => $status == 'MATCH' ? true : false
                        ]);
                }
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            return false;
        }
    }

}
