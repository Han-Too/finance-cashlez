<?php

namespace App\Exports;

use App\Models\ReconcileReport;
use App\Models\ReconcileResult;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReconcileExport implements FromCollection, WithHeadings, WithMapping
{
    protected $token_applicant, $status, $startDate, $endDate, $channel;

    public function __construct($token_applicant, $status, $startDate, $endDate, $channel)
    {
        $this->token_applicant = $token_applicant;
        $this->status = $status;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->channel = $channel;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function oldcollection()
    {
        $query = ReconcileResult::with('merchant', 'bank_account');
        if ($this->token_applicant) {
            $query->where('token_applicant', $this->token_applicant);
        }
        if ($this->status != 'all') {
            if ($this->status) {
                if ($this->status == "match") {
                    $query->where('status', 'MATCH');
                } elseif ($this->status == "dispute") {
                    $query->whereIn('status', ['NOT_MATCH', 'NOT_FOUND']);
                }
            }
        }

        $query->where(DB::raw('DATE(settlement_date)'), '>=', $this->startDate);
        $query->where(DB::raw('DATE(settlement_date)'), '<=', $this->endDate);
        $query->where('processor_payment', $this->channel);
        $query->where('status', '!=', 'deleted');

        return $query->get();
    }
    public function collection()
    {
        $query = ReconcileReport::with('merchant', 'bank_account');
        if ($this->token_applicant) {
            $query->where('token_applicant', $this->token_applicant);
        }
        if ($this->status != 'all') {
            if ($this->status) {
                if ($this->status == "match") {
                    $query->where('status', 'MATCH');
                } elseif ($this->status == "dispute") {
                    $query->whereIn('status', ['NOT_MATCH', 'NOT_FOUND']);
                }
            }
        }

        $query->where(DB::raw('DATE(settlement_date)'), '>=', $this->startDate);
        $query->where(DB::raw('DATE(settlement_date)'), '<=', $this->endDate);
        // $query->where('processor_payment', $this->channel);
        $query->where('status', '!=', 'deleted');

        return $query->get();
    }

    public function oldmap($data): array
    {
        if ($data->status == 'MATCH') {
            $stt = 'MATCH';
        } elseif($data->status == 'NOT_MATCH' || $data->status == 'NOT_FOUND'){
            $stt = 'DISPUTE';
        } else{
            $stt = 'ONHOLD';
        }
        return [
            $data->settlement_date,
            $data->batch_fk,
            $data->merchant->reference_code,
            $data->mid,
            $data->merchant->name,
            $data->processor_payment,
            $stt,
            $data->internal_payment,
            $data->bank_settlement_amount,
            $data->dispute_amount,
            $data->total_sales,
            $data->transfer_amount,
            " " . $data->bank_account->account_number,
            $data->bank_account->bank_code,
            $data->bank_account->bank_name,
            $data->bank_account->account_holder,
            $data->merchant->email,
            $data->processor_payment
        ];
    }
    public function map($data): array
    {
        if ($data->status == 'MATCH') {
            $stt = 'MATCH';
        } elseif($data->status == 'NOT_MATCH' || $data->status == 'NOT_FOUND'){
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
            $mrc = "-";
        } else{
            $mrc = $data->merchant->reference_code;
        }

        if(!$data->bank_account){
            $achold = "-";
        } else{
            $achold = $data->bank_account->account_holder;
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
            strval(substr($data->mid,5)),
            strval($bc),
            strval($mername),
            strval($acnum),
            strval($acn),
            strval($banktype),
            strval($achold),
            strval($data->transfer_amount),
            strval($data->total_sales),
            strval($data->bank_transfer),
            strval($data->bank_settlement_amount),
            strval($data->dispute_amount),
            strval($stt),
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
            'MID',
            'MID SECURE',
            'BANK CODE',
            'MERCHANT NAME',
            'ACCOUNT NUMBER',
            'IDENTIFY',
            'BANK TYPE',
            'ACCOUNT NAME',
            'TRANSFER AMOUNT',
            'SALES AMOUNT',
            'BANK TRANSFER',
            'BANK MOVEMENT',
            'VARIANCE',
            'STATUS',

        ];
    }
}
