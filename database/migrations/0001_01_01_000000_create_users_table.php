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
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('email')->unique();
            $table->enum('account_type', ['individual', 'company'])->default('individual')->after('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('status', ['active', 'disabled'])->default('active');
            $table->string('admin_approval_status')->default('pending')->after('status'); // 'pending' | 'approved' | 'rejected'
            $table->timestamp('admin_approved_at')->nullable()->after('admin_approval_status');
            $table->string('phone')->nullable();
            $table->foreignId('assigned_agent_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('company')->nullable();
            $table->date('date_of_birth')->nullable();

            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->boolean('backoffice_access')->default(false);
            $table->rememberToken();
            $table->timestamp('terms_accepted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
