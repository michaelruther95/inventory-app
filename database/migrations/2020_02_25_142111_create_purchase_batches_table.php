<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_batches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_id');
            $table->foreign('purchase_id')
                  ->references('id')->on('purchases')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('product_batch_id');
            $table->foreign('product_batch_id')
                  ->references('id')->on('product_batches')
                  ->onDelete('cascade');
            $table->longText('information');
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
        Schema::dropIfExists('purchase_batches');
    }
}
