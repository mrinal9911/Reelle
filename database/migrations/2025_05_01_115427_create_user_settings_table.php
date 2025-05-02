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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
            // Privacy
            $table->boolean('show_age')->default(true);
            $table->boolean('show_profile_picture')->default(true);
            $table->boolean('show_net_worth')->default(false);
            $table->boolean('map_visibility')->default(true);
            $table->boolean('online_status_visible')->default(true);
        
            // Messaging
            $table->enum('message_permission', ['everyone', 'friends', 'near_me'])->default('friends');
        
            // Consent
            $table->boolean('accept_event_invites')->default(false);
            $table->boolean('accept_brand_promotions')->default(false);
        
            // Visibility
            $table->enum('visibility_scope', ['public', 'verified_users', 'friends_of_friends'])->default('public');
            $table->boolean('auto_hide_in_high_risk_regions')->default(true);
            $table->json('country_visibility_controls')->nullable(); // e.g., {"IN": false, "US": true}
        
            // Contact Sharing
            $table->boolean('share_contact_with_lost_item_finders')->default(false);
            $table->boolean('share_contact_with_event_organizers')->default(false);
            $table->boolean('share_contact_with_brands')->default(false);
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
