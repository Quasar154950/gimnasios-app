<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'ultimo_login_at')) {

            Schema::table('users', function (Blueprint $table) {

                $table->timestamp('ultimo_login_at')->nullable();

            });

        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'ultimo_login_at')) {

            Schema::table('users', function (Blueprint $table) {

                $table->dropColumn('ultimo_login_at');

            });

        }
    }
};
