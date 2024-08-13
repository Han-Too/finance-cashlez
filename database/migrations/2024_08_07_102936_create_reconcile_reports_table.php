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
        Schema::create('reconcile_reports', function (Blueprint $table) {
            $table->id();
            $table->string('token_applicant')->nullable();
            $table->bigInteger('bank_id')->nullable();
            $table->string('draft_id',255)->nullable();
            $table->bigInteger('statement_id')->nullable();
            $table->text('statement_date')->nullable();
            $table->string('bo_id',255)->nullable();
            $table->text('bo_date')->nullable();
            $table->bigInteger('request_id')->nullable();
            $table->string('tid')->nullable();
            $table->string('mid')->nullable();
            $table->string('batch_fk')->nullable();
            $table->string('trx_counts')->nullable();
            $table->string('total_sales')->nullable();
            $table->string('processor_payment')->nullable();
            $table->string('internal_payment')->nullable();
            $table->string('merchant_payment')->nullable();
            $table->string('merchant_name')->nullable();
            $table->bigInteger('merchant_id')->nullable();
            $table->string('dispute_amount')->nullable();
            $table->string('transfer_amount')->nullable();
            $table->string('bank_settlement_amount')->nullable();
            $table->string('tax_payment')->nullable();
            $table->string('fee_mdr_merchant')->nullable();
            $table->string('fee_bank_merchant')->nullable();
            $table->string('bank_transfer')->nullable();
            $table->string('created_by')->nullable();
            $table->string('modified_by')->nullable();
            $table->timestamp('settlement_date')->nullable();
            $table->string('status')->nullable();
            $table->tinyInteger('status_manual')->nullable();
            $table->string('status_reconcile',150)->nullable();
            $table->string('status_parnert',150)->nullable();
            $table->string('variance',150)->nullable();
            $table->timestamp('reconcile_date')->nullable();
            $table->string('category_report',255)->nullable();
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
        Schema::dropIfExists('reconcile_reports');
    }
};
