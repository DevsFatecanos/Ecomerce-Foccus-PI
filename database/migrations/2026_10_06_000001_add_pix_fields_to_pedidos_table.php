<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table): void {
            $table->string('payment_id', 64)->nullable()->after('provedor');
            $table->text('pix_qr_code')->nullable()->after('payment_id');
            $table->longText('pix_qr_code_base64')->nullable()->after('pix_qr_code');
            $table->timestamp('pix_expira_em')->nullable()->after('pix_qr_code_base64');

            $table->index('payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table): void {
            $table->dropIndex(['payment_id']);
            $table->dropColumn(['payment_id', 'pix_qr_code', 'pix_qr_code_base64', 'pix_expira_em']);
        });
    }
};
