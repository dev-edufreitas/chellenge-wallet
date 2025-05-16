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
        Schema::table('transactions', function (Blueprint $table) {
           
            $table->boolean('is_reverted')->default(false)->after('transaction_reference');
            $table->uuid('original_transaction_reference')->nullable()->after('is_reverted');
            $table->string('reversal_reason')->nullable()->after('original_transaction_reference');
            $table->timestamp('reversed_at')->nullable()->after('reversal_reason');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('is_reverted');
            $table->dropColumn('original_transaction_reference');
            $table->dropColumn('reversal_reason');
            $table->dropColumn('reversed_at');
        });
    }
};