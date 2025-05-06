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
        Schema::disableForeignKeyConstraints();
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
            // Classification
            $table->foreignId('category_id')->constrained('asset_categories');
            $table->foreignId('subcategory_id')->nullable()->constrained('asset_subcategories');
        
            // Asset Details
            $table->string('name');
            $table->string('serial_number')->nullable();
            $table->text('description')->nullable();
            $table->decimal('value', 15, 2)->nullable();
            $table->enum('verification_level', ['none', 'basic', 'verified'])->default('none');
            $table->json('attachments')->nullable(); // image/document paths
        
            // Blockchain & Visibility
            $table->string('blockchain_token_id')->nullable();
            $table->json('transfer_history')->nullable(); // Can expand to a separate table
            $table->enum('visibility', ['private', 'friends', 'public'])->default('private');
        
            // Flags
            $table->boolean('is_reported_lost')->default(false);
            $table->boolean('is_listed_for_sale')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->boolean('status')->default(true);
        
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
