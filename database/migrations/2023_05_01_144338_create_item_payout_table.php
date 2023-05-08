<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_payout', function (Blueprint $table) {
            $table->id()->unsigned()->nullable(false);
            $table->bigInteger('payout_id')->unsigned();
            $table->bigInteger('item_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('item_payout', function($table) {
            $table->foreign('payout_id')->references('id')->on('payouts');
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_items');
    }
};
