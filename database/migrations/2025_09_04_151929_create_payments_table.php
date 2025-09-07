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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('order_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();

            $table->enum('method', ['cash', 'transfer']);
            $table->decimal('amount', 20, 2);
            $table->decimal('change', 20, 2)->default(0);
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
