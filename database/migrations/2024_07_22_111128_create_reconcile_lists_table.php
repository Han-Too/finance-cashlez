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
        Schema::create('reconcile_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('token_applicant', 150)->nullable();
            $table->string('type', 150)->nullable();
            $table->string('settlement_file', 150)->nullable();
            $table->string('bo_date', 150)->nullable();
            $table->string('status', 150)->nullable();
            $table->boolean('is_parnert')->nullable();
            $table->timestamp('reconcile_date')->nullable();
            $table->string('reconcile_by',255)->nullable();
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
        Schema::dropIfExists('reconcile_lists');
    }
};
