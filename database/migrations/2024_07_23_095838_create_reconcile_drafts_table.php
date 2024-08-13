<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reconcile_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('token_applicant');
            $table->bigInteger('statement_id');
            $table->bigInteger('request_id');
            $table->string('tid')->nullable();
            $table->string('mid');
            $table->string('batch_fk')->nullable();
            $table->string('trx_counts');
            $table->string('total_sales');
            $table->string('processor_payment');
            $table->string('internal_payment');
            $table->string('merchant_payment');
            $table->string('merchant_name');
            $table->bigInteger('merchant_id');
            $table->string('dispute_amount');
            $table->string('transfer_amount');
            $table->string('bank_settlement_amount');
            $table->string('tax_payment');
            $table->string('fee_mdr_merchant');
            $table->string('fee_bank_merchant');
            $table->string('bank_transfer');
            $table->string('created_by');
            $table->string('modified_by');
            $table->timestamp('settlement_date');
            $table->string('status');
            $table->tinyInteger('status_manual')->nullable();
            $table->string('status_reconcile',150)->nullable();
            $table->string('status_parnert',150)->nullable();
            $table->string('variance',150)->nullable();
            $table->timestamp('reconcile_date')->nullable();
            $table->bigInteger('statement_id');
            $table->bigInteger('bo_id');
            $table->bigInteger('request_id');
            $table->bigInteger('bank_id');
            $table->timestamps('bo_date');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reconcile_drafts');
    }
};
