<?php
// =============================================================================
// MIGRATION: 2024_01_01_000004_create_subscriptions_table.php
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stripe_subscription_id')->unique()->nullable(); // Allow null initially
            $table->string('stripe_customer_id');
            $table->string('plan_type'); // monthly, yearly
            $table->string('status')->default('incomplete'); // Start with incomplete status
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->integer('amount'); // in cents
            $table->string('currency', 3)->default('usd');
            $table->timestamps();
            
            // Add index for better performance
            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};