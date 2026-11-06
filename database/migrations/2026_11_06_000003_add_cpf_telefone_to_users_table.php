<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // As colunas ja existem no banco de producao (criadas manualmente); esta migration
    // apenas as registra para novos ambientes e para os testes.
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'cpf')) {
                $table->string('cpf', 14)->nullable();
            }

            if (! Schema::hasColumn('users', 'telefone')) {
                $table->string('telefone', 20)->nullable();
            }
        });
    }

    public function down(): void
    {
        // Sem rollback: em producao as colunas antecedem esta migration e guardam dados reais.
    }
};
