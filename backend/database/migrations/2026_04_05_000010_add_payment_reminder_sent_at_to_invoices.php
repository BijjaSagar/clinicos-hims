<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('invoices')) {
            return;
        }

        if (Schema::hasColumn('invoices', 'payment_reminder_sent_at')) {
            return;
        }

        Log::info('migration: add payment_reminder_sent_at to invoices');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dateTime('payment_reminder_sent_at')->nullable()->after('whatsapp_link_sent_at');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('invoices') || ! Schema::hasColumn('invoices', 'payment_reminder_sent_at')) {
            return;
        }

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('payment_reminder_sent_at');
        });
    }
};
