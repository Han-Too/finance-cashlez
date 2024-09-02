<?php

namespace App\Exports;

use App\Models\ReconcileDraft;
use App\Models\ReconcileReport;
use App\Models\ReconcileResult;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReconcileDisburstExport implements FromCollection, WithHeadings, WithMapping
{
    // protected $token_applicant, $status, $startDate, $endDate, $channel;
    protected $token_applicant, $status, $startDate, $endDate, $channel;

    // public function __construct($token_applicant, $status, $startDate, $endDate, $channel)
    public function __construct($startDate, $endDate,)
    {
        // $this->token_applicant = $token_applicant;
        // $this->status = $status;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        // $this->channel = $channel;
    }
    // public function __construct($token_applicant)
    // {
    //     $this->token_applicant = $token_applicant;
    //     $this->status = "MATCH";
    // }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = ReconcileReport::with('merchant', 'bank_account');
        

        $query->where(DB::raw('DATE(created_at)'), '>=', $this->startDate);
        $query->where(DB::raw('DATE(created_at)'), '<=', $this->endDate);
        // $query->where('processor_payment', $this->channel);
        // $query->where('status', '!=', 'deleted');

        return $query->get();
    }

    public function map($data): array
    {
        if ($data->status == 'MATCH') {
            $stt = 'MATCH';
        } elseif($data->status == 'NOT_MATCH' || $data->status == 'deleted'){
            $stt = 'DISPUTE';
        } else{
            $stt = 'ONHOLD';
        }

        if(!$data->bank_account){
            $acnum = "-";
        } else{
            $acnum = $data->bank_account->account_number;
        }

        if(!$data->bank_account){
            $bc = "-";
        } else{
            $bc = $data->bank_account->bank_code;
        }

        if(!$data->bank_account){
            $acn = "-";
        } else{
            $acn = substr($data->bank_account->account_number,0,5);
        }
        if(!$data->merchant){
            $email = "-";
        } else{
            $email = $data->merchant->email;
        }

        if(!$data->merchant){
            $mrc = "-";
        } else{
            $mrc = $data->merchant->reference_code;
        }

        if(!$data->bank_account){
            $achold = "-";
        } else{
            $achold = $data->bank_account->account_holder;
        }
        if(!$data->bank_account){
            $bn = "-";
        } else{
            $bn = $data->bank_account->bank_name;
        }
        if(!$data->merchant){
            $mername = "-";
        } else{
            $mername = $data->merchant->name;
        }

        if(!$data->bank_account){
            $banktype = "VLOOKUP";
        } else if(substr($data->bank_account->account_number,0,5)== "88939"){
            $banktype = "VIRTUAL ACCOUNT";
        } else {
            $banktype = "REGULER";
        }


        return [
            strval($mrc),
            strval($data->mid),
            strval($data->settlement_date),
            strval($mername),
            strval($banktype),
            strval($data->total_sales),
            strval($data->bank_transfer),
            strval($data->bank_settlement_amount),
            strval($data->variance),
            strval($data->transfer_amount),
            strval($data->tax_payment),
            strval($data->transfer_amount - $data->tax_payment),
            strval($data->fee_mdr_merchant),
            strval($data->fee_bank_merchant),
            strval(""),
            strval(""),
            strval($acnum),
            strval($bc),
            strval($achold),
            strval($email),
            strval($bn),
            strval(""),
            strval(""),
            strval($data->updated_at),
            strval(""),
        ];
    }

    public function oldheadings(): array
    {
        return [
            'Settlement Date',
            'Sequence Batch Number',
            'Merchant Reference Code',
            'MID',
            'Merchant Name',
            'Bank Type',
            // 'Trx Status',
            'Reconcile Status',
            'BO Settlement Amount',
            'BANK Settlement Amount',
            'Dispute Amount',
            'Total Sales',
            'Transfer Amount',
            'Account Number',
            'Bank Code',
            'Bank Name',
            'Account Holder',
            'Email',
            'Bank Type',
            'Others',
            // 'Id',
            // 'Transaction Id',
        ];
    }
    public function headings(): array
    {
        return [
            'MRC',
            'MID SECURE',
            'SETTLEMENT DATE',
            'MERCHANT NAME',
            'BANK TYPE',
            'TOTAL SALES',
            'BANK TRANSFER',
            'BANK MOVEMENT',
            'VARIANCE',
            'TRANSFER AMOUNT',
            'ADM',
            'TOTAL',
            'FEE MDR MERCHANT',
            'FEE MDR BANK',
            'SKEMA SALES VOLUME',
            'NET TRANSFER AFTER SKEMA SALES VOLUME',
            'ACCOUNT NUMBER',
            'BANK CODE',
            'ACCOUNT HOLDER',
            'EMAIL',
            'BANK',
            'STATUS RELEASE',
            'REMARK',
            'DATE CONVERTED',
            'TRX ID PARTIAL PAYMENT',
        ];
    }
}
