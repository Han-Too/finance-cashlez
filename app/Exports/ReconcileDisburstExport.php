<?php

namespace App\Exports;

use App\Models\ReconcileDraft;
use App\Models\ReconcileReport;
use App\Models\ReconcileResult;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReconcileDisburstExport implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting, WithEvents
{
    // protected $token_applicant, $status, $startDate, $endDate, $channel;
    protected $token_applicant, $status, $startDate, $endDate, $channel;

    // public function __construct($token_applicant, $status, $startDate, $endDate, $channel)
    public function __construct($startDate, $endDate, )
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
        } elseif ($data->status == 'NOT_MATCH' || $data->status == 'deleted') {
            $stt = 'DISPUTE';
        } else {
            $stt = 'ONHOLD';
        }

        $acnum = $data->bank_account ? $data->bank_account->account_number : "-";
        $bc = $data->bank_account ? $data->bank_account->bank_code : "-";
        $acn = $data->bank_account ? substr($data->bank_account->account_number, 0, 5) : "-";
        $email = $data->merchant ? $data->merchant->email : "-";
        $mrc = $data->merchant ? $data->merchant->reference_code : "-";
        $achold = $data->bank_account ? $data->bank_account->account_holder : "-";
        $bn = $data->channel ? $data->channel->channel : "-";
        // $mername = $data->merchant_name == "-" ? $data->merchant_name : "-";

        $banktype = !$data->bank_account ? "VLOOKUP" :
            (substr($data->bank_account->account_number, 0, 5) == "88939" ? "VIRTUAL ACCOUNT" : "REGULER");

        if(substr($data->bank_settlement_amount - $data->bank_transfer,0,1) == "-"){
            $var = "(".$data->bank_settlement_amount - $data->bank_transfer.")";
        } else {
            $var = $data->bank_settlement_amount - $data->bank_transfer;
        }


        return [
            strval($mrc),
            strval($data->mid),
            strval($data->settlement_date),
            strval($data->merchant_name),
            strval($banktype),
            strval($data->total_sales),
            strval($data->bank_transfer),
            strval($data->bank_settlement_amount),
            strval($var),
            // strval($data->variance),
            strval($data->transfer_amount),
            // strval($data->tax_payment),
            strval(""),
            // strval($data->transfer_amount - $data->tax_payment),
            strval(""),
            strval($data->fee_mdr_merchant),
            strval($data->fee_bank_merchant),
            strval(""),
            strval(""),
            strval($acnum),
            strval($bc),
            strval($achold),
            strval($email),
            strval($bn),
            strval("BANK DIBURSE"),
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
            'RECONCILIATION DATE',
            'TRX ID PARTIAL PAYMENT',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // Merchant Ref Code as text
            'B' => NumberFormat::FORMAT_TEXT, // MID as text
            'Q' => NumberFormat::FORMAT_TEXT, // Account Number as text
            // Tambahkan kolom lain yang perlu di-set sebagai teks
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Menetapkan tipe data kolom tertentu sebagai string
                foreach ($sheet->getRowIterator() as $row) {
                    $sheet->getCell('A' . $row->getRowIndex())->setDataType(DataType::TYPE_STRING);
                    $sheet->getCell('B' . $row->getRowIndex())->setDataType(DataType::TYPE_STRING);
                    $sheet->getCell('Q' . $row->getRowIndex())->setDataType(DataType::TYPE_STRING);
                    // Tambahkan kolom lain yang perlu di-set sebagai string
                }
            },
        ];
    }
}
