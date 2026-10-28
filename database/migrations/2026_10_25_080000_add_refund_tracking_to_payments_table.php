<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('refund_status', 20)->nullable()->after('snap_token')->index();
            $table->text('refund_reason')->nullable()->after('refund_status');
            $table->text('refund_note')->nullable()->after('refund_reason');
            $table->timestamp('refund_requested_at')->nullable()->after('refund_note');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['refund_status']);
            $table->dropColumn([
                'refund_status',
                'refund_reason',
                'refund_note',
                'refund_requested_at',
                'refund_processed_at',
            ]);
        });
    }
};
