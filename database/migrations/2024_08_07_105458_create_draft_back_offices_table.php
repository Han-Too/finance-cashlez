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
        Schema::create('draft_back_offices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bo_id');
            $table->bigInteger('batch_fk')->nullable();
            $table->bigInteger('transaction_count');
            $table->string('status')->nullable();
            $table->string('tid')->nullable();
            $table->string('mid')->nullable();
            $table->string('merchant_name')->nullable();
            $table->string('processor')->nullable();
            $table->string('batch_running_no')->nullable();
            $table->bigInteger('merchant_id')->nullable();
            $table->bigInteger('bank_id')->nullable();
            $table->bigInteger('mid_ppn')->nullable();
            $table->string('transaction_amount')->nullable();
            $table->string('total_sales_amount')->nullable();
            $table->bigInteger('settlement_audit_id')->nullable();
            $table->string('tax_payment')->nullable();
            $table->string('fee_mdr_merchant')->nullable();
            $table->string('fee_bank_merchant')->nullable();
            $table->string('bank_transfer')->nullable();
            $table->string('created_by');
            $table->string('draft_token');
            $table->string('status_reconcile');
            $table->timestamps('reconcile_date');
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
        Schema::dropIfExists('draft_back_offices');
    }
};
