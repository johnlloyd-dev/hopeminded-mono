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
         Schema::table('textbooks', function (Blueprint $table) {
            $table->jsonb('image')->nullable()->change();
            $table->jsonb('video')->nullable()->change();
            $table->string('object')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('textbooks', function (Blueprint $table) {
            $table->jsonb('image')->nullable()->change();
            $table->jsonb('video')->nullable()->change();
            $table->string('object')->nullable()->change();
        });
    }
};
