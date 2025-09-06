<?php
// database/migrations/xxxx_add_stripe_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('email');
            $table->string('current_plan')->default('free')->after('stripe_customer_id');
            $table->timestamp('plan_expires_at')->nullable()->after('current_plan');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['stripe_customer_id', 'current_plan', 'plan_expires_at']);
        });
    }
};