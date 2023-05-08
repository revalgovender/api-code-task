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
        Schema::create('payouts', function (Blueprint $table) {
            $table->id()->unsigned()->nullable(false);
            $table->bigInteger('seller_reference')
                ->unsigned()
                ->nullable(false);
            $table->decimal('amount', 10);
            $table->string('currency');
            $table->timestamps();
        });

        Schema::table('payouts', function($table) {
            $table->foreign('seller_reference')->references('id')->on('sellers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
