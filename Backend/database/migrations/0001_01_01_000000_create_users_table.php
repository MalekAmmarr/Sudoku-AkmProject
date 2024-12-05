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
            $table->id(); // Primary key
            $table->string('name'); // User name
            $table->string('email')->unique(); // Email address (unique)
            $table->timestamp('email_verified_at')->nullable(); // Email verification timestamp
            $table->string('password'); // Encrypted password
            $table->rememberToken(); // For "remember me" functionality
            $table->timestamps(); // Created_at and updated_at timestamps
            $table->integer('score')->default(0); // User score (default is 0)

            // Credit card-related fields
            $table->string('payment_gateway_customer_id')->nullable(); // Gateway customer ID
            $table->string('card_number', 16)->nullable(); // Last four digits of the credit card
            $table->string('card_brand')->nullable(); // Card brand (e.g., Visa, MasterCard)
            $table->timestamp('card_expires_at')->nullable(); // Card expiration date

        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Primary key: email address
            $table->string('token'); // Password reset token
            $table->timestamp('created_at')->nullable(); // Token creation time
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Primary key: session ID
            $table->foreignId('user_id')->nullable()->index(); // Foreign key to users table
            $table->string('ip_address', 45)->nullable(); // IP address
            $table->text('user_agent')->nullable(); // User agent string
            $table->longText('payload'); // Session payload
            $table->integer('last_activity')->index(); // Last activity timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users'); // Drop users table
        Schema::dropIfExists('password_reset_tokens'); // Drop password reset tokens table
        Schema::dropIfExists('sessions'); // Drop sessions table
    }
};
