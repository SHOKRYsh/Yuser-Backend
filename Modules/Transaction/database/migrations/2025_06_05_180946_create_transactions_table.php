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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('frontline_liaison_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('main_case_handler_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('financial_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('executive_director_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('legal_supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('quality_assurance_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('bank_liaison_officer_id')->nullable()->constrained('users')->onDelete('set null');
          
            $table->enum('current_status', [
                'Pending',
                'In_Review',
                'Approved',
                'Rejected',
                'On_Hold',
                'Completed',
                'Cancelled'
            ])->default('Pending');
            $table->json('status_history')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
