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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('address')->nullable();
            $table->enum('financing_type', ['Personal_Financing', 'Real_Estate_Financing', 'Car_Financing'])->nullable();
            $table->string('job')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->string('work_nature')->nullable();
            $table->string('nationality')->nullable();
            $table->string('other_income_sources')->nullable();
            $table->string('religion')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('national_id')->nullable();
            $table->boolean('has_previous_loan')->default(false);
            $table->string('previous_loan_name')->nullable();
            $table->decimal('previous_loan_value', 12, 2)->nullable();
           
            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
