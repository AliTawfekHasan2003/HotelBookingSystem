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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('count_month');
            $table->integer('count_day');
            $table->decimal('total_cost', 12, 2);
            $table->enum('status', ['paid', 'pending', 'cancelled'])->default('pending');
            $table->string('payment_id');

            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
