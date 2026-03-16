<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Split name into first/last
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');

            // Account type: individual or company
            $table->string('account_type')->default('individual')->after('email'); // 'individual' | 'company'

            // Country & city (required by BRD)
            $table->string('country')->nullable()->after('phone');
            $table->string('city')->nullable()->after('country');

            // Approval workflow
            $table->string('approval_status')->default('pending')->after('status'); // 'pending' | 'approved' | 'rejected'
            $table->timestamp('approved_at')->nullable()->after('approval_status');

            // Terms acceptance
            $table->timestamp('terms_accepted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'account_type',
                'country', 'city', 'approval_status', 'approved_at',
                'terms_accepted_at',
            ]);
        });
    }
};
