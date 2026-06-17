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
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('region_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('prefecture_id')
                ->nullable()
                ->after('region_id')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('area_id')
                ->nullable()
                ->after('prefecture_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('area_id');
            $table->dropConstrainedForeignId('prefecture_id');
            $table->dropConstrainedForeignId('region_id');
        });
    }
};
