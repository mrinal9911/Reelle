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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nickname')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name'); // Required for Level 2 verification
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('occupation')->nullable();
            $table->string('relationship_status')->nullable();
            
            // Language & Education
            $table->string('primary_language')->nullable();
            $table->string('secondary_language')->nullable();
            $table->string('education_level')->nullable();
            $table->string('net_worth_range')->nullable();
        
            // Verification
            $table->string('id_document_path')->nullable();
            $table->boolean('govt_verified')->default(false);
        
            // Security
            $table->boolean('two_fa_enabled')->default(false);
            $table->json('device_logs')->nullable();
        
            // Auth
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('status')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
